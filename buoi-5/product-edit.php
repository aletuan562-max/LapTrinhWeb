<?php
require_once 'db.php';
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: products.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("Sản phẩm không tồn tại.");
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container my-4" style="max-width: 600px;">
    <h2>Cập nhật sản phẩm</h2>
    <form action="product-edit-process.php" method="POST">
        <input type="hidden" name="id" value="<?= $product['id'] ?>">
        <div class="mb-3">
            <label class="form-label">Danh mục:</label>
            <select name="category_id" class="form-select" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Mã sản phẩm (Code):</label>
            <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($product['code']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tên sản phẩm:</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Giá (Price):</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tồn kho (Stock):</label>
            <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Trạng thái:</label>
            <select name="status" class="form-select">
                <option value="active" <?= $product['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $product['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="products.php" class="btn btn-secondary">Quay lại</a>
    </form>
</body>
</html>