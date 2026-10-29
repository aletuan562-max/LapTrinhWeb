<?php
require_once 'db.php';

// Lấy danh sách sản phẩm kết hợp với bảng categories để lấy tên danh mục
$stmt = $pdo->query("
    SELECT p.*, c.name AS category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id DESC
");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sản phẩm - MiniShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Sản phẩm</h2>
        <a href="product-add.php" class="btn btn-primary">+ Thêm sản phẩm mới</a>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Mã SKU</th>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr><td colspan="8" class="text-center">Không có sản phẩm nào.</td></tr>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['code']) ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['category_name']) ?></td>
                    <td><?= number_format($p['price'], 0, ',', '.') ?> đ</td>
                    <td><?= $p['stock'] ?></td>
                    <td>
                        <span class="badge bg-<?= $p['status'] == 'active' ? 'success' : 'secondary' ?>">
                            <?= $p['status'] ?>
                        </span>
                    </td>
                    <td>
                        <a href="product-edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                        
                        <!-- Form Xóa/Khóa dùng phương thức POST -->
                        <form action="product-delete.php" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>