<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'];
    $code = trim($_POST['code']);
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO products (category_id, code, name, price, stock, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$category_id, $code, $name, $price, $stock, $status]);

    header('Location: products.php');
    exit;
}