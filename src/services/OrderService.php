<?php

require_once __DIR__ . '/../repositories/OrderRepository.php';
require_once __DIR__ . '/../repositories/OrderItemRepository.php';
require_once __DIR__ . '/../repositories/CartRepository.php';
require_once __DIR__ . '/../repositories/ProductRepository.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';

class OrderService
{
    public function __construct(
        private PDO $pdo,
        private OrderRepository $orderRepository,
        private OrderItemRepository $orderItemRepository,
        private CartRepository $cartRepository,
        private ProductRepository $productRepository
    ) {}

    public function checkout(int $userId): int
    {
        $cart = $this->cartRepository->findFullCartByClientId($userId);

        if ($cart === null || count($cart->items) === 0) {
            throw new Exception('Cart is empty.');
        }

        $this->pdo->beginTransaction();

        try {
            $total = 0.0;

            foreach ($cart->items as $cartItem) {
                $product = $this->productRepository->findById($cartItem->productId);

                if ($product === null) {
                    throw new Exception("Product {$cartItem->productId} not found.");
                }

                if ($product->stock < $cartItem->quantity) {
                    throw new Exception("Not enough stock for product {$product->name}.");
                }

                $total += $cartItem->quantity * $cartItem->unitPrice;
            }

            $order = new Order(
                null,
                $userId,
                new DateTime(),
                $total
            );

            $orderId = $this->orderRepository->create($order);

            foreach ($cart->items as $cartItem) {
                $orderItem = new OrderItem(
                    null,
                    $orderId,
                    $cartItem->productId,
                    $cartItem->quantity,
                    $cartItem->unitPrice
                );

                $this->orderItemRepository->create($orderItem);

                $updated = $this->productRepository->decreaseStock(
                    $cartItem->productId,
                    $cartItem->quantity
                );

                if (!$updated) {
                    throw new Exception("Failed to update stock for product {$cartItem->productId}.");
                }
            }

            $this->cartRepository->clearCart($cart->id);

            $this->pdo->commit();

            return $orderId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getOrderById(int $orderId): ?Order
    {
        return $this->orderRepository->findFullOrderById($orderId);
    }

    public function getOrdersByUserId(int $userId): array
    {
        return $this->orderRepository->findByUserId($userId);
    }
}