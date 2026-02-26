<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\FakeStoreService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(FakeStoreService $service): void
    {
        $products = $service->getProducts();

        foreach ($products as $item) {
            Product::updateOrCreate(
                ['external_id' => $item->externalId],
                [
                    'title'        => $item->title,
                    'price'        => $item->price,
                    'description'  => $item->description,
                    'category'     => $item->category,
                    'image'        => $item->image,
                    'rating_rate'  => $item->ratingRate,
                    'rating_count' => $item->ratingCount,
                ]
            );
        }
    }
}
