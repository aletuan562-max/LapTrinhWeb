<?php
// Xếp loại: kiểm tra từ cao xuống thấp để không gán nhầm
function xepLoai(float $diem): string
{
    if ($diem >= 8) {
        return 'Giỏi';
    } elseif ($diem >= 6.5) {
        return 'Khá';
    } elseif ($diem >= 5) {
        return 'Trung bình';
    } else {
        return 'Chưa đạt';
    }
}

$input = trim($_POST['diem'] ?? '');
$input = str_replace(',', '.', $input);   // cho phép gõ 6,5
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($input === '') {
        $msg = 'Vui lòng nhập điểm.';
    } elseif (!preg_match('/^\d+(\.\d+)?$/', $input) || (float)$input > 10) {
        $msg = 'Điểm không hợp lệ (phải là số từ 0 đến 10).';
    } else {
        $diem = (float)$input;
        $msg = "Điểm $input: xếp loại " . xepLoai($diem);
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Xếp loại điểm</title>
</head>
<body>
<h1>Xếp loại điểm</h1>

<form method="post">
    <label>Điểm (0 - 10):
        <input name="diem" value="<?= htmlspecialchars($input, ENT_QUOTES, 'UTF-8') ?>">
    </label>
    <button>Xếp loại</button>
</form>

<p><strong><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></strong></p>
</body>
</html>