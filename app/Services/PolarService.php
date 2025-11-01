<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\CustomerListData;
use App\Data\PolarOrganizationData;
use App\Data\PolarRevenueData;
use DateTimeImmutable;
use Exception;
use Illuminate\Support\Facades\Http;

final readonly class PolarService implements PaymentProvider
{
    private const string BASE_URL = 'https://api.polar.sh/v1';

    public function getOrganizationDetails(string $apiKey): PolarOrganizationData
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Accept' => 'application/json',
        ])->get($this->url('/organizations'));

        if ($response->failed()) {
            $responseData = $response->json();
            $errorMessage = $responseData['detail'][0]['msg'] ?? "API request failed with status {$response->status()}";
            throw new Exception($errorMessage);
        }

        $result = $response->json();

        if (! isset($result['items']) || empty($result['items'])) {
            throw new Exception('No organization found for API key ' . encrypt($apiKey));
        }

        $org = $result['items'][0];
        $createdAt = (new DateTimeImmutable($org['created_at']))->format('Y-m-d');

        return new PolarOrganizationData(
            name: $org['name'],
            website: $org['website'] ?? null,
            avatarUrl: $org['avatar_url'] ?? null,
            description: null,
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

    public function validateCheckout(string $checkoutId): bool
    {
        $apiKey = config('services.polar.api_key');

        if (! $apiKey) {
            throw new Exception('Polar API key is not configured');
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Accept' => 'application/json',
        ])->get($this->url("/checkouts/{$checkoutId}"));

        if ($response->failed()) {
            return false;
        }

        $result = $response->json();

        return isset($result['status']) && $result['status'] === 'succeeded';
    }

    /**
     * Fetch a page of customers from Polar API
     */
    public function fetchCustomers(string $apiKey, int $page = 1, int $limit = 15): CustomerListData
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Accept' => 'application/json',
        ])->get($this->url('/customers'), [
            'page' => $page,
            'limit' => $limit,
        ]);

        if ($response->failed()) {
            throw new Exception("API request failed with status {$response->status()}");
        }

        $result = $response->json();
        $items = $result['items'] ?? [];
        $pagination = $result['pagination'] ?? [];
        $maxPage = $pagination['max_page'] ?? 1;

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
            hasMorePages: $page < $maxPage,
            hasPreviousPage: $page > 1,
        );
    }

    private function getMrrAggregates(string $apiKey, ?string $lastProcessedId = null, ?string $lastProcessedYearMonth = null): array
    {
        $monthlyData = [];
        $page = 1;
        $lastProcessedIdFound = $lastProcessedId === null;
        $hasResumePoint = $lastProcessedYearMonth !== null;
        $lastProcessedSubscriptionId = null;

        while (true) {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Accept' => 'application/json',
            ])->get($this->url('/subscriptions'), [
                'page' => $page,
            ]);

            if ($response->failed()) {
                throw new Exception("API request failed with status {$response->status()}");
            }

            $result = $response->json();
            $items = $result['items'] ?? [];
            $processNextPage = true;

            foreach ($items as $subscription) {
                if ($lastProcessedId && ! $lastProcessedIdFound && $subscription['id'] === $lastProcessedId) {
                    $lastProcessedIdFound = true;

                    continue;
                }

                if (! $lastProcessedIdFound) {
                    continue;
                }

                $createdDate = new DateTimeImmutable($subscription['created_at']);
                $yearMonth = $createdDate->format('Y-m');

                if ($hasResumePoint && $yearMonth < $lastProcessedYearMonth) {
                    $processNextPage = false;
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

                $amount = $subscription['amount'] / 100.0;

                if ($subscription['recurring_interval'] === 'month') {
                    $monthlyData[$yearMonth]['monthly_recurring_revenue'] += $amount;
                } elseif ($subscription['recurring_interval'] === 'year') {
                    $amount = $amount / 12.0;
                    $monthlyData[$yearMonth]['monthly_recurring_revenue'] += $amount;
                }

                $lastProcessedSubscriptionId = $subscription['id'];
            }

            if (! $processNextPage) {
                break;
            }

            $pagination = $result['pagination'] ?? [];
            $maxPage = $pagination['max_page'] ?? 1;

            if ($page >= $maxPage) {
                break;
            }

            $page++;
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
        $page = 1;
        $lastProcessedIdFound = $lastProcessedId === null;
        $hasResumePoint = $lastProcessedYearMonth !== null;
        $lastProcessedOrderId = null;

        while (true) {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Accept' => 'application/json',
            ])->get($this->url('/orders'), [
                'page' => $page,
            ]);

            if ($response->failed()) {
                throw new Exception("API request failed with status {$response->status()}");
            }

            $result = $response->json();
            $items = $result['items'] ?? [];
            $processNextPage = true;

            foreach ($items as $order) {
                if ($lastProcessedId && ! $lastProcessedIdFound && $order['id'] === $lastProcessedId) {
                    $lastProcessedIdFound = true;

                    continue;
                }

                if (! $lastProcessedIdFound) {
                    continue;
                }

                $createdDate = new DateTimeImmutable($order['created_at']);
                $orderYearMonth = $createdDate->format('Y-m');

                if ($hasResumePoint && $orderYearMonth < $lastProcessedYearMonth) {
                    $processNextPage = false;
                    break;
                }

                if (! isset($monthlyData[$orderYearMonth])) {
                    $monthlyData[$orderYearMonth] = [
                        'gross_revenue' => 0.0,
                    ];
                }

                $amount = $order['total_amount'] / 100.0;
                $monthlyData[$orderYearMonth]['gross_revenue'] += $amount;

                $lastProcessedOrderId = $order['id'];
            }

            if (! $processNextPage) {
                break;
            }

            $pagination = $result['pagination'] ?? [];
            $maxPage = $pagination['max_page'] ?? 1;

            if ($page >= $maxPage) {
                break;
            }

            $page++;
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
        ])
            ->get($this->url('/customers'));

        if ($response->failed()) {
            throw new Exception("API request failed with status {$response->status()}");
        }

        $result = $response->json();

        return $result['pagination']['total_count'] ?? 0;
    }
}
