<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\CustomerListData;
use App\Data\PolarOrganizationData;
use App\Data\PolarRevenueData;
use DateTimeImmutable;
use Exception;
use Illuminate\Support\Facades\Http;

final readonly class StripeService implements PaymentProvider
{
    private const string BASE_URL = 'https://api.stripe.com/v1';

    public function getOrganizationDetails(string $apiKey): PolarOrganizationData
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Accept' => 'application/json',
        ])->get($this->url('/accounts'));

        if ($response->failed()) {
            $responseData = $response->json();
            $errorMessage = $responseData['error']['message'] ?? "API request failed with status {$response->status()}";
            throw new Exception($errorMessage);
        }

        $result = $response->json();

        if (! isset($result['business_profile'])) {
            throw new Exception('No business profile found for Stripe account');
        }

        $businessProfile = $result['business_profile'];
        $createdAt = (new DateTimeImmutable())->setTimestamp($result['created'])->format('Y-m-d');

        return new PolarOrganizationData(
            name: $businessProfile['name'] ?? 'Unknown Business',
            website: $businessProfile['url'] ?? null,
            avatarUrl: null,
            description: $businessProfile['product_description'] ?? null,
            createdAt: $createdAt,
        );
    }

    public function getPolarData(string $apiKey, ?string $lastProcessedSubscriptionId = null, ?string $lastProcessedOrderId = null, ?string $lastProcessedYearMonth = null): PolarRevenueData
    {
        if (! $apiKey) {
            throw new Exception('API key is required');
        }

        $mrrResult = $this->getMrrAggregates($apiKey, $lastProcessedSubscriptionId, $lastProcessedYearMonth);
        $grossRevenueResult = $this->getGrossRevenueAggregates($apiKey, $lastProcessedOrderId, $lastProcessedYearMonth);
        $subscriberCount = $this->getActiveSubscriberCount($apiKey);

        $monthlyMrrData = $mrrResult['monthly_data'];
        $lastProcessedSubscriptionId = $mrrResult['last_processed_id'];
        $monthlyGrossRevenueData = $grossRevenueResult['monthly_data'];
        $lastProcessedOrderId = $grossRevenueResult['last_processed_id'];

        $mrr = 0.0;
        if (! empty($monthlyMrrData)) {
            $latestMonth = array_key_last($monthlyMrrData);
            $latestMrrData = $monthlyMrrData[$latestMonth];
            $mrr = $latestMrrData['monthly_recurring_revenue'];
        }

        $totalRevenue = 0.0;
        if (! empty($monthlyGrossRevenueData)) {
            $latestMonth = array_key_last($monthlyGrossRevenueData);
            $latestRevenueData = $monthlyGrossRevenueData[$latestMonth];
            $totalRevenue = $latestRevenueData['gross_revenue'];
        }

        return new PolarRevenueData(
            monthlyRecurringRevenue: round($mrr, 2),
            totalRevenue: round($totalRevenue, 2),
            subscriberCount: $subscriberCount,
            monthlyMrrAggregates: $monthlyMrrData,
            monthlyGrossRevenueAggregates: $monthlyGrossRevenueData,
            lastProcessedSubscriptionId: $lastProcessedSubscriptionId,
            lastProcessedOrderId: $lastProcessedOrderId,
        );
    }

    /**
     * Fetch a page of customers from Stripe API
     */
    public function fetchCustomers(string $apiKey, int $page = 1, int $limit = 15): CustomerListData
    {
        $offset = ($page - 1) * $limit;
        $params = [
            'limit' => $limit,
            'offset' => $offset,
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Accept' => 'application/json',
        ])->get($this->url('/customers'), $params);

        if ($response->failed()) {
            throw new Exception("API request failed with status {$response->status()}");
        }

        $result = $response->json();
        $items = $result['data'] ?? [];
        $hasMore = $result['has_more'] ?? false;

        $customers = collect($items)
            ->map(fn(array $customer) => [
                'id' => $customer['id'],
                'name' => $customer['name'] ?? 'Unknown',
                'email' => $customer['email'] ?? '',
            ])
            ->toArray();

        return new CustomerListData(
            customers: $customers,
            currentPage: $page,
            hasMorePages: $hasMore,
            hasPreviousPage: $page > 1,
        );
    }

    private function getMrrAggregates(string $apiKey, ?string $lastProcessedId = null, ?string $lastProcessedYearMonth = null): array
    {
        $monthlyData = [];
        $hasMore = true;
        $startingAfter = $lastProcessedId;
        $hasResumePoint = $lastProcessedYearMonth !== null;
        $lastProcessedSubscriptionId = null;

        while ($hasMore) {
            $params = ['limit' => 100];
            if ($startingAfter) {
                $params['starting_after'] = $startingAfter;
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Accept' => 'application/json',
            ])->get($this->url('/subscriptions'), $params);

            if ($response->failed()) {
                throw new Exception("API request failed with status {$response->status()}");
            }

            $result = $response->json();
            $items = $result['data'] ?? [];
            $hasMore = $result['has_more'] ?? false;

            foreach ($items as $subscription) {
                $createdDate = (new DateTimeImmutable())->setTimestamp($subscription['created']);
                $yearMonth = $createdDate->format('Y-m');

                if ($hasResumePoint && $yearMonth < $lastProcessedYearMonth) {
                    $hasMore = false;
                    break;
                }

                if (! isset($monthlyData[$yearMonth])) {
                    $monthlyData[$yearMonth] = [
                        'monthly_recurring_revenue' => 0.0,
                    ];
                }

                if ($subscription['status'] !== 'active') {
                    continue;
                }

                foreach ($subscription['items']['data'] as $item) {
                    $price = $item['price'];
                    $amount = ($price['unit_amount'] ?? 0) / 100.0;

                    if ($price['recurring']['interval'] === 'month') {
                        $monthlyData[$yearMonth]['monthly_recurring_revenue'] += $amount;
                    } elseif ($price['recurring']['interval'] === 'year') {
                        $amount = $amount / 12.0;
                        $monthlyData[$yearMonth]['monthly_recurring_revenue'] += $amount;
                    }
                }

                $lastProcessedSubscriptionId = $subscription['id'];
                $startingAfter = $subscription['id'];
            }
        }

        ksort($monthlyData);

        return [
            'monthly_data' => $monthlyData,
            'last_processed_id' => $lastProcessedSubscriptionId,
        ];
    }

    private function getGrossRevenueAggregates(string $apiKey, ?string $lastProcessedId = null, ?string $lastProcessedYearMonth = null): array
    {
        $monthlyData = [];
        $hasMore = true;
        $startingAfter = $lastProcessedId;
        $hasResumePoint = $lastProcessedYearMonth !== null;
        $lastProcessedOrderId = null;

        while ($hasMore) {
            $params = ['limit' => 100];
            if ($startingAfter) {
                $params['starting_after'] = $startingAfter;
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Accept' => 'application/json',
            ])->get($this->url('/charges'), $params);

            if ($response->failed()) {
                throw new Exception("API request failed with status {$response->status()}");
            }

            $result = $response->json();
            $items = $result['data'] ?? [];
            $hasMore = $result['has_more'] ?? false;

            foreach ($items as $charge) {
                if (! $charge['paid']) {
                    continue;
                }

                $createdDate = (new DateTimeImmutable())->setTimestamp($charge['created']);
                $orderYearMonth = $createdDate->format('Y-m');

                if ($hasResumePoint && $orderYearMonth < $lastProcessedYearMonth) {
                    $hasMore = false;
                    break;
                }

                if (! isset($monthlyData[$orderYearMonth])) {
                    $monthlyData[$orderYearMonth] = [
                        'gross_revenue' => 0.0,
                    ];
                }

                $amount = $charge['amount'] / 100.0;
                $monthlyData[$orderYearMonth]['gross_revenue'] += $amount;

                $lastProcessedOrderId = $charge['id'];
                $startingAfter = $charge['id'];
            }
        }

        ksort($monthlyData);

        return [
            'monthly_data' => $monthlyData,
            'last_processed_id' => $lastProcessedOrderId,
        ];
    }

    private function url(string $path): string
    {
        return self::BASE_URL . $path;
    }

    private function getActiveSubscriberCount(string $apiKey): int
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Accept' => 'application/json',
        ])->get($this->url('/customers'), [
            'limit' => 1,
        ]);

        if ($response->failed()) {
            throw new Exception("API request failed with status {$response->status()}");
        }

        $result = $response->json();

        return $result['total_count'] ?? 0;
    }
}
