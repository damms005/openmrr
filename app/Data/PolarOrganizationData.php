<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

final class PolarOrganizationData extends Data
{
    public function __construct(
        public string $name,
        public ?string $website,
        public ?string $avatarUrl,
        public ?string $description,
        public ?string $createdAt = null,
    ) {}
}
