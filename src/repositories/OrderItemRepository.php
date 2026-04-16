<?php

require_once __DIR__ . '/../models/OrderItem.php';

class OrderItemRepository
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM articolo_ordine ORDER BY idArticoloOrdine ASC");
        $rows = $stmt->fetchAll();

        return array_map([$this, 'mapRowToOrderItem'], $rows);
    }

    public function findById(int $id): ?OrderItem
    {
        $stmt = $this->pdo->prepare("SELECT * FROM articolo_ordine WHERE idArticoloOrdine = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->mapRowToOrderItem($row) : null;
    }

    public function findByOrderId(int $orderId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM articolo_ordine
            WHERE idOrdine = ?
            ORDER BY idArticoloOrdine ASC
        ");
        $stmt->execute([$orderId]);
        $rows = $stmt->fetchAll();

        return array_map([$this, 'mapRowToOrderItem'], $rows);
    }

    public function create(OrderItem $item): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO articolo_ordine (idOrdine, idProdotto, quantita, prezzoUnitario)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $item->orderId,
            $item->productId,
            $item->quantity,
            $item->unitPrice,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function addOrderItem(int $orderId, int $productId, int $quantity, float $unitPrice): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO articolo_ordine (idOrdine, idProdotto, quantita, prezzoUnitario)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $orderId,
            $productId,
            $quantity,
            $unitPrice,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(OrderItem $item): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE articolo_ordine
            SET idOrdine = ?, idProdotto = ?, quantita = ?, prezzoUnitario = ?
            WHERE idArticoloOrdine = ?
        ");

        return $stmt->execute([
            $item->orderId,
            $item->productId,
            $item->quantity,
            $item->unitPrice,
            $item->id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM articolo_ordine WHERE idArticoloOrdine = ?");
        return $stmt->execute([$id]);
    }

    public function deleteByOrderId(int $orderId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM articolo_ordine WHERE idOrdine = ?");
        return $stmt->execute([$orderId]);
    }

    private function mapRowToOrderItem(array $row): OrderItem
    {
        return new OrderItem(
            (int)$row['idArticoloOrdine'],
            (int)$row['idOrdine'],
            (int)$row['idProdotto'],
            (int)$row['quantita'],
            (float)$row['prezzoUnitario']
        );
    }
}