<?php

require_once __DIR__ . '/../repositories/CartRepository.php';
require_once __DIR__ . '/../repositories/CartItemRepository.php';
require_once __DIR__ . '/../repositories/ProductRepository.php';
require_once __DIR__ . '/../models/CartItem.php';

class CartService
{
    public function __construct(
        private CartRepository $cartRepository,
        private CartItemRepository $cartItemRepository,
        private ProductRepository $productRepository
    ) {}

    public function getOrCreateCart(int $clientId): Cart
    {
        return $this->cartRepository->findOrCreateByClientId($clientId);
    }

    public function getFullCart(int $clientId): ?Cart
    {
        return $this->cartRepository->findFullCartByClientId($clientId);
    }

    public function addProductToCart(int $clientId, int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new Exception('Quantity must be greater than zero.');
        }

        $product = $this->productRepository->findById($productId);

        if ($product === null) {
            throw new Exception('Product not found.');
        }

        if ($product->stock < $quantity) {
            throw new Exception('Not enough stock available.');
        }

        $cart = $this->cartRepository->findOrCreateByClientId($clientId);

        $existingItem = $this->cartItemRepository->findByCartIdAndProductId($cart->id, $productId);

        if ($existingItem !== null) {
            $newQuantity = $existingItem->quantity + $quantity;

            if ($product->stock < $newQuantity) {
                throw new Exception('Requested total quantity exceeds stock.');
            }

            $existingItem->quantity = $newQuantity;
            $existingItem->unitPrice = $product->price;

            $this->cartItemRepository->update($existingItem);
            return;
        }

        $cartItem = new CartItem(
            null,
            $cart->id,
            $productId,
            $quantity,
            $product->price
        );

        $this->cartItemRepository->create($cartItem);
    }

    public function updateCartItemQuantity(int $clientId, int $productId, int $newQuantity): void
    {
        $cart = $this->cartRepository->findByClientId($clientId);

        if ($cart === null) {
            throw new Exception('Cart not found.');
        }

        $item = $this->cartItemRepository->findByCartIdAndProductId($cart->id, $productId);

        if ($item === null) {
            throw new Exception('Cart item not found.');
        }

        if ($newQuantity <= 0) {
            $this->cartItemRepository->delete($item->id);
            return;
        }

        $product = $this->productRepository->findById($productId);

        if ($product === null) {
            throw new Exception('Product not found.');
        }

        if ($product->stock < $newQuantity) {
            throw new Exception('Not enough stock available.');
        }

        $item->quantity = $newQuantity;
        $item->unitPrice = $product->price;

        $this->cartItemRepository->update($item);
    }

    public function removeProductFromCart(int $clientId, int $productId): void
    {
        $cart = $this->cartRepository->findByClientId($clientId);

        if ($cart === null) {
            throw new Exception('Cart not found.');
        }

        $item = $this->cartItemRepository->findByCartIdAndProductId($cart->id, $productId);

        if ($item === null) {
            throw new Exception('Product not found in cart.');
        }

        $this->cartItemRepository->delete($item->id);
    }

    public function clearCart(int $clientId): void
    {
        $cart = $this->cartRepository->findByClientId($clientId);

        if ($cart === null) {
            return;
        }

        $this->cartRepository->clearCart($cart->id);
    }

    public function calculateCartTotal(int $clientId): float
    {
        $cart = $this->cartRepository->findFullCartByClientId($clientId);

        if ($cart === null) {
            return 0.0;
        }

        $total = 0.0;

        foreach ($cart->items as $item) {
            $total += $item->quantity * $item->unitPrice;
        }

        return $total;
    }
}