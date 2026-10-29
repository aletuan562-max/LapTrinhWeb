<?php
require_once 'db.php';
// Lấy danh sách danh mục để đổ vào thẻ <select>
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sản phẩm mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container my-4" style="max-width: 600px;">
    <h2>Thêm sản phẩm mới</h2>
    <form action="product-add-process.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Danh mục:</label>
            <select name="category_id" class="form-select" required>
                <option value="">-- Chọn danh mục --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Mã sản phẩm (Code):</label>
            <input type="text" name="code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tên sản phẩm:</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Giá (Price):</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tồn kho (Stock):</label>
            <input type="number" name="stock" class="form-control" value="0" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Trạng thái:</label>
            <select name="status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Lưu sản phẩm</button>
        <a href="products.php" class="btn btn-secondary">Quay lại</a>
    </form>
</body>
</html>