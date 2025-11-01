<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Startup;
use Database\Seeders\AdvertiserSeeder;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\AdvertiserFactory;

arch()->preset()->php();

arch()->preset()->strict()
    ->ignoring([
        'App\Filament\Widgets',
        Startup::class,
    ]);

arch()->preset()->security()->ignoring([
    AdvertiserSeeder::class,
    AdvertiserFactory::class,
]);

arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toBeUsed();

arch('models should be classes')
    ->expect('App\Models')
    ->toBeClasses()
    ->toExtend(Model::class)
    ->ignoring(User::class);

arch('livewire components should be classes')
    ->expect('App\Livewire')
    ->toBeClasses();
