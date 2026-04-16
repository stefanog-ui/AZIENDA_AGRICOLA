<?php

require_once __DIR__ . '/../models/CartItem.php';

class CartItemRepository
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM articolo_carrello ORDER BY idArticoloCarrello ASC");
        $rows = $stmt->fetchAll();

        return array_map([$this, 'mapRowToCartItem'], $rows);
    }

    public function findById(int $id): ?CartItem
    {
        $stmt = $this->pdo->prepare("SELECT * FROM articolo_carrello WHERE idArticoloCarrello = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->mapRowToCartItem($row) : null;
    }

    public function findByCartId(int $cartId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM articolo_carrello
            WHERE idCarrello = ?
            ORDER BY idArticoloCarrello ASC
        ");
        $stmt->execute([$cartId]);
        $rows = $stmt->fetchAll();

        return array_map([$this, 'mapRowToCartItem'], $rows);
    }

    public function findByCartIdAndProductId(int $cartId, int $productId): ?CartItem
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM articolo_carrello
            WHERE idCarrello = ? AND idProdotto = ?
        ");
        $stmt->execute([$cartId, $productId]);
        $row = $stmt->fetch();

        return $row ? $this->mapRowToCartItem($row) : null;
    }

    public function create(CartItem $item): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO articolo_carrello (idCarrello, idProdotto, quantita, prezzoUnitario)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $item->cartId,
            $item->productId,
            $item->quantity,
            $item->unitPrice,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function addOrUpdateItem(int $cartId, int $productId, int $quantity, float $unitPrice): bool
    {
        $existing = $this->findByCartIdAndProductId($cartId, $productId);

        if ($existing !== null) {
            $stmt = $this->pdo->prepare("
                UPDATE articolo_carrello
                SET quantita = quantita + ?, prezzoUnitario = ?
                WHERE idArticoloCarrello = ?
            ");

            return $stmt->execute([
                $quantity,
                $unitPrice,
                $existing->id,
            ]);
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO articolo_carrello (idCarrello, idProdotto, quantita, prezzoUnitario)
            VALUES (?, ?, ?, ?)
        ");

        return $stmt->execute([
            $cartId,
            $productId,
            $quantity,
            $unitPrice,
        ]);
    }

    public function update(CartItem $item): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE articolo_carrello
            SET idCarrello = ?, idProdotto = ?, quantita = ?, prezzoUnitario = ?
            WHERE idArticoloCarrello = ?
        ");

        return $stmt->execute([
            $item->cartId,
            $item->productId,
            $item->quantity,
            $item->unitPrice,
            $item->id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM articolo_carrello WHERE idArticoloCarrello = ?");
        return $stmt->execute([$id]);
    }

    public function deleteByCartId(int $cartId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM articolo_carrello WHERE idCarrello = ?");
        return $stmt->execute([$cartId]);
    }

    private function mapRowToCartItem(array $row): CartItem
    {
        return new CartItem(
            (int)$row['idArticoloCarrello'],
            (int)$row['idCarrello'],
            (int)$row['idProdotto'],
            (int)$row['quantita'],
            (float)$row['prezzoUnitario']
        );
    }
}