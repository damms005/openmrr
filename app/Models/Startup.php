<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\PolarOrganizationData;
use App\Data\PolarRevenueData;
use App\Enums\AccountType;
use App\Services\PolarService;
use App\Services\StripeService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Startup extends Model
{
    /** @use HasFactory<\Database\Factories\StartupFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public static function createFor(
        Founder $founder,
        PolarOrganizationData $organization,
        PolarRevenueData $revenue,
        string $apiKey,
        int $rank,
        string $accountType = 'polar',
    ): self {
        return self::updateOrCreate(
            ['name' => $organization->name],
            [
                'founder_id' => $founder->id,
                'slug' => str($organization->name)->slug(),
                'description' => $organization->description,
                'website_url' => $organization->website,
                'avatar_url' => $organization->avatarUrl,
                'business_created_at' => $organization->createdAt,
                'encrypted_api_key' => encrypt($apiKey),
                'account_type' => $accountType,
                'monthly_recurring_revenue' => $revenue->monthlyRecurringRevenue,
                'total_revenue' => $revenue->totalRevenue,
                'subscriber_count' => $revenue->subscriberCount,
                'last_processed_subscription_id' => $revenue->lastProcessedSubscriptionId,
                'last_processed_order_id' => $revenue->lastProcessedOrderId,
                'last_synced_at' => now(),
                'rank' => $rank,
            ]
        );
    }

    public function casts(): array
    {
        return [
            'total_revenue' => 'decimal:2',
            'monthly_recurring_revenue' => 'decimal:2',
            'business_created_at' => 'date',
            'last_synced_at' => 'datetime',
            'account_type' => AccountType::class,
        ];
    }

    public function founder(): BelongsTo
    {
        return $this->belongsTo(Founder::class);
    }

    public function businessCategory(): BelongsTo
    {
        return $this->belongsTo(BusinessCategory::class);
    }

    public function monthlyMrrs(): HasMany
    {
        return $this->hasMany(StartupMonthlyMrr::class);
    }

    public function grossRevenues(): HasMany
    {
        return $this->hasMany(StartupGrossRevenue::class);
    }

    public function rfcs(): HasMany
    {
        return $this->hasMany(Rfc::class);
    }

    public function getPaymentHandler()
    {
        return match ($this->account_type) {
            AccountType::POLAR => new PolarService(),
            AccountType::STRIPE => new StripeService(),
        };
    }

    protected function decryptedApiKey(): Attribute
    {
        return Attribute::make(
            get: fn (): string => decrypt($this->encrypted_api_key),
        );
    }

    protected function pageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => route('startup.show', $this->slug),
        );
    }
}
