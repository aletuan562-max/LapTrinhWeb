<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $category_id = $_POST['category_id'];
    $code = trim($_POST['code']);
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE products SET category_id = ?, code = ?, name = ?, price = ?, stock = ?, status = ? WHERE id = ?");
    $stmt->execute([$category_id, $code, $name, $price, $stock, $status, $id]);

    header('Location: products.php');
    exit;
}