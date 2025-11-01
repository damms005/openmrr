<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        $this->bootModelsDefaults();
    }

    private function bootModelsDefaults(): void
    {
        Model::unguard();
        Model::automaticallyEagerLoadRelationships();
    }
}
