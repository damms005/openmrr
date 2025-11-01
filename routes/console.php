<?php

declare(strict_types=1);

use App\Console\Commands\SyncRevenueData;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SyncRevenueData::class)->hourly();
