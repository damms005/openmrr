<?php

declare(strict_types=1);

namespace App\Data;

final readonly class CustomerListData
{
    /**
     * @param  array<array{id: string, name: string, email: string}>  $customers
     */
    public function __construct(
        public array $customers,
        public int $currentPage,
        public bool $hasMorePages,
        public bool $hasPreviousPage,
    ) {}
}
