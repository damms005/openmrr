<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Rfc;
use App\Models\Startup;
use Illuminate\Database\Seeder;

final class RfcSeeder extends Seeder
{
    public function run(): void
    {
        $startups = Startup::all();
        $count = (int) ceil($startups->count() * 0.3);
        $startupIds = $startups->pluck('id')->random($count)->toArray();

        foreach ($startupIds as $startupId) {
            Rfc::factory(random_int(3, 15))
                ->for(Startup::find($startupId))
                ->create([
                    'response' => fake()->paragraph(3),
                ]);
        }

        $topStartups = Startup::orderBy('rfc_count', 'desc')->take(3)->get();

        foreach ($topStartups as $startup) {
            Rfc::factory(random_int(1111, 2111))
                ->for($startup)
                ->create([
                    'response' => fake()->paragraph(3),
                ]);
        }
    }
}
