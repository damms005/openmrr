<?php

declare(strict_types=1);

namespace App\Data;

final readonly class CustomerData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
    ) {}
}
