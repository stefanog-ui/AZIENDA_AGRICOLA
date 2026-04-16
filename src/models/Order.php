<?php

class Order
{
    public ?int $id;
    public int $userId;
    public DateTime $orderDate;
    public float $total;
    /** @var OrderItem[] */
    public array $items = [];

    public function __construct(
        ?int $id,
        int $userId,
        DateTime $orderDate,
        float $total,
        array $items = []
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->orderDate = $orderDate;
        $this->total = $total;
        $this->items = $items;
    }
}