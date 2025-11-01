<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('startup_monthly_mrrs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('startup_id')->constrained()->cascadeOnDelete();
            $table->string('year_month', 7);
            $table->unsignedBigInteger('monthly_recurring_revenue')->default(0);
            $table->timestamps();

            $table->unique(['startup_id', 'year_month']);
            $table->index(['startup_id', 'year_month']);
        });

        Schema::create('startup_gross_revenues', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('startup_id')->constrained()->cascadeOnDelete();
            $table->string('year_month', 7);
            $table->unsignedBigInteger('gross_revenue')->default(0);
            $table->timestamps();

            $table->unique(['startup_id', 'year_month']);
            $table->index(['startup_id', 'year_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('startup_gross_revenues');
        Schema::dropIfExists('startup_monthly_mrrs');
    }
};
