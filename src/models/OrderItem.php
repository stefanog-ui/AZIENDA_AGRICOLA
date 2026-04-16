<?php

class OrderItem
{
    public ?int $id;
    public int $orderId;
    public int $productId;
    public int $quantity;
    public float $unitPrice;

    public function __construct(
        ?int $id,
        int $orderId,
        int $productId,
        int $quantity,
        float $unitPrice
    ) {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
    }

    public function getSubtotal(): float
    {
        return $this->quantity * $this->unitPrice;
    }
}