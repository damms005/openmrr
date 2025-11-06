<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('startups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('founder_id')->constrained()->cascadeOnDelete();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('business_category_id')->nullable();
            $table->string('website_url')->nullable();
            $table->string('avatar_url')->nullable();
            $table->date('business_created_at')->nullable();
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->integer('rank');
            $table->decimal('monthly_recurring_revenue', 15, 2)->default(0);
            $table->integer('subscriber_count')->default(0);
            $table->string('encrypted_api_key')->nullable();
            $table->string('account_type');
            $table->timestamp('last_synced_at')->nullable();
            $table->string('last_processed_subscription_id')->nullable();
            $table->string('last_processed_order_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('startups');
    }
};
