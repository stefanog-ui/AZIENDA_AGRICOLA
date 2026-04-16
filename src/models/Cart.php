<?php

class Cart
{
    public ?int $id;
    public int $clientId;
    /** @var CartItem[] */
    public array $items = [];

    public function __construct(?int $id, int $clientId, array $items = [])
    {
        $this->id = $id;
        $this->clientId = $clientId;
        $this->items = $items;
    }
}