<?php

require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/CartItem.php';

class CartRepository
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM carrello ORDER BY idCarrello ASC");
        $rows = $stmt->fetchAll();

        return array_map(fn(array $row) => new Cart(
            (int)$row['idCarrello'],
            (int)$row['idCliente']
        ), $rows);
    }

    public function findById(int $id): ?Cart
    {
        $stmt = $this->pdo->prepare("SELECT * FROM carrello WHERE idCarrello = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? new Cart(
            (int)$row['idCarrello'],
            (int)$row['idCliente']
        ) : null;
    }

    public function findByClientId(int $clientId): ?Cart
    {
        $stmt = $this->pdo->prepare("SELECT * FROM carrello WHERE idCliente = ?");
        $stmt->execute([$clientId]);
        $row = $stmt->fetch();

        return $row ? new Cart(
            (int)$row['idCarrello'],
            (int)$row['idCliente']
        ) : null;
    }

    public function findOrCreateByClientId(int $clientId): Cart
    {
        $cart = $this->findByClientId($clientId);

        if ($cart !== null) {
            return $cart;
        }

        $stmt = $this->pdo->prepare("INSERT INTO carrello (idCliente) VALUES (?)");
        $stmt->execute([$clientId]);

        return new Cart((int)$this->pdo->lastInsertId(), $clientId);
    }

    public function findFullCartByClientId(int $clientId): ?Cart
    {
        $cart = $this->findByClientId($clientId);

        if ($cart === null) {
            return null;
        }

        $stmt = $this->pdo->prepare("
            SELECT * FROM articolo_carrello
            WHERE idCarrello = ?
            ORDER BY idArticoloCarrello ASC
        ");
        $stmt->execute([$cart->id]);
        $rows = $stmt->fetchAll();

        $items = array_map(fn(array $row) => new CartItem(
            (int)$row['idArticoloCarrello'],
            (int)$row['idCarrello'],
            (int)$row['idProdotto'],
            (int)$row['quantita'],
            (float)$row['prezzoUnitario']
        ), $rows);

        $cart->items = $items;
        return $cart;
    }

    public function create(Cart $cart): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO carrello (idCliente) VALUES (?)");
        $stmt->execute([$cart->clientId]);

        return (int)$this->pdo->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM carrello WHERE idCarrello = ?");
        return $stmt->execute([$id]);
    }

    public function clearCart(int $cartId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM articolo_carrello WHERE idCarrello = ?");
        return $stmt->execute([$cartId]);
    }
}