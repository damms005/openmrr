<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\StripeMerchantCategoryCode;
use App\Models\BusinessCategory;
use Illuminate\Database\Seeder;

final class BusinessCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (StripeMerchantCategoryCode::cases() as $case) {
            $label = $case->getLabel();


            if (BusinessCategory::where('slug', str($label)->slug())->exists()) {
                continue;
            }

            BusinessCategory::create([
                'code' => $case->value,
                'label' => $label,
                'slug' => str($label)->slug(),
                'description' => null,
                'seo_title' => null,
                'seo_meta_description' => null,
            ]);
        }
    }
}
