<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\DTOs\ProductImportDTO;

class FakeStoreService
{
    protected string $baseUrl = 'https://fakestoreapi.com';

    public function getProducts(): array
    {
        try {
            $response = Http::retry(3, 100)->get("{$this->baseUrl}/products");

            return $response->successful()
                ? array_map(fn (array $item) => ProductImportDTO::fromApi($item), $response->json())
                : [];
        } catch (\Exception $e) {
            Log::error("Erro ao buscar produtos na FakeStoreAPI: " . $e->getMessage());
            return [];
        }
    }
}
