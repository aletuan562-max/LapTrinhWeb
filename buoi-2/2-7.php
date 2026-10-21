<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Hóa đơn học liệu</title>
</head>
<body>
<?php
function money(float $value): string {
  return number_format($value, 0, ',', '.') . ' đ';
}
$name = $_POST['name'] ?? '';
$qty = (int)($_POST['qty'] ?? 0);
$price = (float)($_POST['price'] ?? 0);
$total = $qty * $price;
?>
<form method="post">
  <input name="name" placeholder="Tên tài liệu">
  <input name="qty" type="number" min="1">
  <input name="price" type="number" min="0">
  <button>Tính</button>
</form>
<?php if ($total > 0): ?>
  <p><?= htmlspecialchars($name) ?>: <?= money($total) ?></p>
<?php endif; ?>
</body>
</html>