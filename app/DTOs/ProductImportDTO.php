<?php

namespace App\DTOs;

readonly class ProductImportDTO
{
    public function __construct(
        public string $externalId,
        public string $title,
        public float $price,
        public string $description,
        public string $category,
        public string $image,
        public float $ratingRate,
        public int $ratingCount,
    ) {}

    public static function fromApi(array $data): self
    {
        return new self(
            externalId: (string) $data['id'],
            title: $data['title'],
            price: (float) $data['price'],
            description: $data['description'],
            category: $data['category'],
            image: $data['image'],
            ratingRate: (float) ($data['rating']['rate'] ?? 0),
            ratingCount: (int) ($data['rating']['count'] ?? 0),
        );
    }
}
