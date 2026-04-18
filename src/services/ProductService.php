<?php

require_once __DIR__ . '/../repositories/ProductRepository.php';
require_once __DIR__ . '/../models/Product.php';

class ProductService
{
    public function __construct(private ProductRepository $productRepository) {}

    public function createProduct(
        string $name,
        string $description,
        int $stock,
        float $price,
        DateTime $expirationDate,
    ): int {
        if ($stock < 0) {
            throw new Exception('Stock cannot be negative.');
        }

        if ($price < 0) {
            throw new Exception('Price cannot be negative.');
        }

        $product = new Product(
            null,
            $name,
            $description,
            $stock,
            $price,
            $expirationDate
        );

        return $this->productRepository->create($product);
    }

    public function updateProduct(Product $product): bool
    {
        if ($product->stock < 0) {
            throw new Exception('Stock cannot be negative.');
        }

        if ($product->price < 0) {
            throw new Exception('Price cannot be negative.');
        }

        return $this->productRepository->update($product);
    }

    public function getProductById(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }

    public function getAllProducts(): array
    {
        return $this->productRepository->findAll();
    }

    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }

    public function checkProductAvailability(int $productId, int $quantity): bool
    {
        $product = $this->productRepository->findById($productId);

        if ($product === null) {
            throw new Exception('Product not found.');
        }

        return $product->stock >= $quantity;
    }
}