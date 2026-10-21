<?php
declare(strict_types=1);

function money(float $value): string
{
    return number_format($value, 0, ',', '.') . ' đ';
}

function e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

// Đọc POST an toàn: luôn trả về chuỗi đã trim
function post(string $key): string
{
    $v = $_POST[$key] ?? '';
    return is_string($v) ? trim($v) : '';
}

$name  = post('name');
$qty   = post('qty');
$price = post('price');
$errors = [];
$total = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra TRƯỚC, ép kiểu SAU
    if ($name === '') {
        $errors[] = 'Vui lòng nhập tên tài liệu.';
    }
    if (!preg_match('/^\d+$/', $qty) || (int)$qty < 1) {
        $errors[] = 'Số lượng phải là số nguyên từ 1 trở lên.';
    }
    if (!preg_match('/^\d+(\.\d+)?$/', $price)) {
        $errors[] = 'Đơn giá phải là số không âm.';
    }

    if (!$errors) {
        $total = (int)$qty * (float)$price;
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Hóa đơn học liệu</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; }
        input { padding: 6px; }
        .error { color: #b3261e; }
    </style>
</head>
<body>
<form method="post">
    <input name="name"  placeholder="Tên tài liệu" value="<?= e($name) ?>">
    <input name="qty"   placeholder="Số lượng" type="number" min="1" value="<?= e($qty) ?>">
    <input name="price" placeholder="Đơn giá" type="number" min="0" value="<?= e($price) ?>">
    <button>Tính</button>
</form>

<?php foreach ($errors as $error): ?>
    <p class="error"><?= e($error) ?></p>
<?php endforeach; ?>

<?php if ($total !== null): ?>
    <p><?= e($name) ?>: <?= money($total) ?></p>
<?php endif; ?>
</body>
</html>