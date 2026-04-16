<?php

class Product
{
    public ?int $id;
    public string $name;
    public string $description;
    public int $stock;
    public float $price;
    public ?DateTime $expirationDate;

    public function __construct(
        ?int $id,
        string $name,
        string $description,
        int $stock,
        float $price,
        ?DateTime $expirationDate
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->stock = $stock;
        $this->price = $price;
        $this->expirationDate = $expirationDate;
    }
}