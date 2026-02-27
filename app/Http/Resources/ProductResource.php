<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->external_id,
            'title'       => $this->title,
            'price'       => (float) $this->price,
            'price_with_tax' => round((float) $this->price * 1.10, 2),
            'description' => $this->description,
            'category'    => $this->category,
            'image'       => $this->image,
            'rating'      => [
                'rate'  => (float) $this->rating_rate,
                'count' => (int) $this->rating_count,
            ],
        ];
    }
}
