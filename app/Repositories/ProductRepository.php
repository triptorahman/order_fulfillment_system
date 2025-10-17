<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-17

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    protected Product $model;

    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    /**
     * find
     *
     * @param  int $id
     * @return Product
     */
    public function find(int $id): ?Product
    {
        return $this->model->find($id);
    }

    /**
     * reduceStock
     *
     * @param  Product $product
     * @param  int $qty
     * @return void
     */
    public function reduceStock(Product $product, int $qty): void
    {
        $product->decrement('stock_quantity', $qty);
    }
}
