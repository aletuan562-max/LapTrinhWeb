<?php
declare(strict_types=1);

// Trọng số: 10% chuyên cần, 30% giữa kỳ, 60% cuối kỳ
const W_CC    = 0.10;
const W_MID   = 0.30;
const W_FINAL = 0.60;

function finalScore(float $cc, float $mid, float $final): float
{
    return $cc * W_CC + $mid * W_MID + $final * W_FINAL;
}

// Dữ liệu mẫu: [chuyên cần, giữa kỳ, cuối kỳ]
$samples = [
    [10, 8, 9],
    [9, 7, 6],
    [8, 5, 4],
    [10, 10, 10],
];
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Điểm tổng kết</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; }
        table { border-collapse: collapse; }
        th, td { border: 1px solid #c3cad3; padding: 8px 14px; text-align: center; }
        th { background: #f3f5f8; }
    </style>
</head>
<body>
<h1>Điểm tổng kết học phần</h1>
<p>Công thức: 10% chuyên cần + 30% giữa kỳ + 60% cuối kỳ</p>

<table>
    <tr>
        <th>Chuyên cần</th>
        <th>Giữa kỳ</th>
        <th>Cuối kỳ</th>
        <th>Tổng kết</th>
    </tr>
    <?php foreach ($samples as [$cc, $mid, $final]): ?>
        <tr>
            <td><?= $cc ?></td>
            <td><?= $mid ?></td>
            <td><?= $final ?></td>
            <td><strong><?= number_format(finalScore($cc, $mid, $final), 2) ?></strong></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>