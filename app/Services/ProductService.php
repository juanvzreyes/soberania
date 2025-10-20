<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\DTOs\PhotoStorageConfig;

class ProductService
{
    protected $productData;

    public function __construct(Product $productData, private PhotoService $photoService)
    {
        $this->productData = $productData;
    }

    public function store(array $data): Product | null
    {
        return DB::transaction(function () use ($data) {
            $productData = Product::create($data);
            $this->photoService->storePhotos($productData, $data['photos'] ?? [], new PhotoStorageConfig(basePath: 'photos/products'));
        });
    }

    public function update(Product $product, array $data): Product | null
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update($data);
            $this->photoService->syncPhotos($product, $data['photos'] ?? [], new PhotoStorageConfig(basePath: 'photos/products'));

            return $product;
        });
    }
    public function delete(Product $product): void
    {
        $product->delete();
    }
}
