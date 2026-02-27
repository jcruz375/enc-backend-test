<?php

namespace App\Jobs;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\FakeStoreService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ImportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(FakeStoreService $service): void
    {
        $products = $service->getProducts();

        foreach ($products as $item) {
            $data = [
                'external_id'  => $item->externalId,
                'title'        => $item->title,
                'price'        => $item->price,
                'description'  => $item->description,
                'category'     => $item->category,
                'image'        => $item->image,
                'rating_rate'  => $item->ratingRate,
                'rating_count' => $item->ratingCount,
            ];

            $rules = (new ProductRequest())->rules();
            unset($rules['external_id']);

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                Log::warning("Produto ignorado por falha na validação (ID: {$item->externalId})", $validator->errors()->toArray());
                continue;
            }

            Product::updateOrCreate(
                ['external_id' => $data['external_id']],
                $data
            );
        }
    }
}
