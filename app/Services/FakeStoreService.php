<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FakeStoreService
{
    protected string $baseUrl = 'https://fakestoreapi.com';

    public function getProducts(): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/products");

            return $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            Log::error("Erro ao buscar produtos na FakeStoreAPI: " . $e->getMessage());
            return [];
        }
    }
}
