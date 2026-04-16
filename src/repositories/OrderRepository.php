<?php

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';

class OrderRepository
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM ordine ORDER BY idOrdine ASC");
        $rows = $stmt->fetchAll();

        return array_map([$this, 'mapRowToOrder'], $rows);
    }

    public function findById(int $id): ?Order
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ordine WHERE idOrdine = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->mapRowToOrder($row) : null;
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM ordine
            WHERE idUtente = ?
            ORDER BY dataOrdine DESC
        ");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();

        return array_map([$this, 'mapRowToOrder'], $rows);
    }

    public function findFullOrderById(int $orderId): ?Order
    {
        $order = $this->findById($orderId);

        if ($order === null) {
            return null;
        }

        $stmt = $this->pdo->prepare("
            SELECT * FROM articolo_ordine
            WHERE idOrdine = ?
            ORDER BY idArticoloOrdine ASC
        ");
        $stmt->execute([$orderId]);
        $rows = $stmt->fetchAll();

        $order->items = array_map(fn(array $row) => new OrderItem(
            (int)$row['idArticoloOrdine'],
            (int)$row['idOrdine'],
            (int)$row['idProdotto'],
            (int)$row['quantita'],
            (float)$row['prezzoUnitario']
        ), $rows);

        return $order;
    }

    public function create(Order $order): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO ordine (idUtente, dataOrdine, totale)
            VALUES (?, NOW(), ?)
        ");

        $stmt->execute([
            $order->userId,
            $order->total,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function createOrder(int $userId, float $total): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO ordine (idUtente, dataOrdine, totale)
            VALUES (?, NOW(), ?)
        ");

        $stmt->execute([$userId, $total]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(Order $order): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE ordine
            SET idUtente = ?, dataOrdine = ?, totale = ?
            WHERE idOrdine = ?
        ");

        return $stmt->execute([
            $order->userId,
            $order->orderDate,
            $order->total,
            $order->id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM ordine WHERE idOrdine = ?");
        return $stmt->execute([$id]);
    }

    private function mapRowToOrder(array $row): Order
    {
        return new Order(
            (int)$row['idOrdine'],
            (int)$row['idUtente'],
            new DateTime($row['dataOrdine']),
            (float)$row['totale']
        );
    }
}