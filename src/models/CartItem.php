<?php

class CartItem
{
    public ?int $id;
    public int $cartId;
    public int $productId;
    public int $quantity;
    public float $unitPrice;

    public function __construct(
        ?int $id,
        int $cartId,
        int $productId,
        int $quantity,
        float $unitPrice
    ) {
        $this->id = $id;
        $this->cartId = $cartId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
    }

    public function getSubtotal(): float
    {
        return $this->quantity * $this->unitPrice;
    }
}