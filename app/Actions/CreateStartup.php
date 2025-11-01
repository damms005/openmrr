<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\AccountType;
use App\Jobs\AssignCategory;
use App\Models\Founder;
use App\Models\Startup;
use App\Services\PolarService;
use App\Services\StripeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final readonly class CreateStartup
{
    public function __construct(
        private PolarService $polarService,
        private StripeService $stripeService
    ) {}

    public function handle(string $apiKey, ?string $xHandle = null, ?string $description = null): Startup
    {
        info('Creating startup: ' . encrypt($apiKey));

        $accountType = $this->determineAccountType($apiKey);
        $service = $accountType === AccountType::STRIPE ? $this->stripeService : $this->polarService;

        $revenue = $service->getPolarData($apiKey);
        $organization = $service->getOrganizationDetails($apiKey);

        if ($description && !$organization->description) {
            $organization->description = $description;
        }

        $lock = Cache::lock('create-startup', 10);
        $lock->block(10);

        $rank = $this->calculateRankForRevenue($revenue->totalRevenue);

        DB::beginTransaction();

        $founder = Founder::updateOrCreate(
            ['x_handle' => $xHandle],
        );

        $startup = Startup::createFor($founder, $organization, $revenue, $apiKey, $rank, $accountType->value);

        DB::commit();

        $lock->release();

        Startup::where('rank', '>=', $rank)
            ->where('id', '!=', $startup->id)
            ->increment('rank');

        if ($accountType === AccountType::POLAR) {
            AssignCategory::dispatch($startup);
        }

        return $startup;
    }

    private function determineAccountType(string $apiKey): AccountType
    {
        return str_starts_with($apiKey, 'polar_oat_') ? AccountType::POLAR : AccountType::STRIPE;
    }

    private function calculateRankForRevenue(float $totalRevenue): int
    {
        return Startup::where('total_revenue', '>', $totalRevenue)->count() + 1;
    }
}
