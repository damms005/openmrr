<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\CustomerListData;

interface PaymentProvider
{
    /**
     * Fetch a page of customers from the payment provider.
     *
     * @param  string  $apiKey  The API key for authentication
     * @param  int  $page  The page number to fetch (defaults to 1)
     * @param  int  $limit  The number of items per page (defaults to 15)
     * @return CustomerListData Normalized list of customers with pagination info
     */
    public function fetchCustomers(string $apiKey, int $page = 1, int $limit = 15): CustomerListData;
}
