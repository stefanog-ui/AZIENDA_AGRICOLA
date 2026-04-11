<?php
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

$stmt = $pdo->query("SELECT * FROM utente");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

print_r($products);