<?php

require_once __DIR__ . '/../models/Product.php';

class ProductRepository
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM prodotto ORDER BY idProdotto ASC");
        $rows = $stmt->fetchAll();

        return array_map([$this, 'mapRowToProduct'], $rows);
    }

    public function findById(int $id): ?Product
    {
        $stmt = $this->pdo->prepare("SELECT * FROM prodotto WHERE idProdotto = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->mapRowToProduct($row) : null;
    }

    public function create(Product $product): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO prodotto (nomeProdotto, descrizione, stock, prezzo, dataScadenza)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $product->name,
            $product->description,
            $product->stock,
            $product->price,
            $product->expirationDate->format('Y-m-d H:i:s'),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(Product $product): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE prodotto
            SET nomeProdotto = ?, descrizione = ?, stock = ?, prezzo = ?, dataScadenza = ?
            WHERE idProdotto = ?
        ");

        return $stmt->execute([
            $product->name,
            $product->description,
            $product->stock,
            $product->price,
            $product->expirationDate->format('Y-m-d H:i:s'),
            $product->id,
        ]);
    }

    public function decreaseStock(int $productId, int $quantity): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE prodotto
            SET stock = stock - ?
            WHERE idProdotto = ? AND stock >= ?
        ");

        return $stmt->execute([$quantity, $productId, $quantity]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM prodotto WHERE idProdotto = ?");
        return $stmt->execute([$id]);
    }

    private function mapRowToProduct(array $row): Product
    {
        return new Product(
            (int)$row['idProdotto'],
            $row['nomeProdotto'],
            $row['descrizione'],
            (int)$row['stock'],
            (float)$row['prezzo'],
            new DateTime($row['dataScadenza']));
    }
}