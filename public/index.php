<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/models/Product.php';
require_once __DIR__ . '/../src/models/Cart.php';
require_once __DIR__ . '/../src/models/CartItem.php';
require_once __DIR__ . '/../src/models/Order.php';
require_once __DIR__ . '/../src/models/OrderItem.php';

require_once __DIR__ . '/../src/repositories/UserRepository.php';
require_once __DIR__ . '/../src/repositories/ProductRepository.php';
require_once __DIR__ . '/../src/repositories/CartRepository.php';
require_once __DIR__ . '/../src/repositories/CartItemRepository.php';
require_once __DIR__ . '/../src/repositories/OrderRepository.php';
require_once __DIR__ . '/../src/repositories/OrderItemRepository.php';

$host = '127.0.0.1';
$port = 3306;
$db   = 'azienda_agricola';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

try {
    $pdo = Database::getConnection();

    echo "Connected successfully!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

/*
$userRepository = new UserRepository($pdo);
$user1 = new User(null, "Marco","ggg@gmail.com","ciao","Cliente", new DateTime());
$userRepository->create($user1);
$users = $userRepository->findAll();


$productRepository = new ProductRepository($pdo);
$product1 = new Product(null,"Rape","km 0",20,5,new DateTime());
$productRepository->create($product1);
$products = $productRepository->findAll();
print_r($products);


$cartRepository = new CartRepository($pdo);
$cartItemRepository = new CartItemRepository($pdo);

$clientId = 3; // existing user id in table utente

// 1. Get existing cart or create a new one
$cart = $cartRepository->findOrCreateByClientId($clientId);

// 2. Add some items to that cart
$item1 = new CartItem(
    null,   // idArticoloCarrello, auto increment
    $cart->id,
    4,      // idProdotto
    3,      // quantita
    4.50    // prezzoUnitario
);

$item2 = new CartItem(
    null,
    $cart->id,
    5,
    1,
    9.99
);

$cartItemRepository->create($item1);
$cartItemRepository->create($item2);

// 3. Reload the full cart with its items
$fullCart = $cartRepository->findFullCartByClientId($clientId);
print_r($fullCart);

$orderRepository = new OrderRepository($pdo);
$orderItemRepository = new OrderItemRepository($pdo);

$userId = 3; // existing user id

// 1. Create the order
$order = new Order(
    null,
    $userId,
    new DateTime(),
    23.49
);

$orderId = $orderRepository->create($order);

// 2. Create some order items linked to that order
$item1 = new OrderItem(
    null,
    $orderId,
    5,      // idProdotto
    3,      // quantita
    4.50    // prezzoUnitario
);

$item2 = new OrderItem(
    null,
    $orderId,
    5,
    1,
    9.99
);

$orderItemRepository->create($item1);
$orderItemRepository->create($item2);

// 3. Reload full order with items
$fullOrder = $orderRepository->findFullOrderById($orderId);
*/