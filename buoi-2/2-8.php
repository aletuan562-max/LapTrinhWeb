<?php
declare(strict_types=1);

// 1. Mảng 3 sinh viên: name, midterm, final
$students = [
    ['name' => 'An',   'midterm' => 8.0, 'final' => 7.0],
    ['name' => 'Bình', 'midterm' => 6.0, 'final' => 5.0],
    ['name' => 'Chi',  'midterm' => 3.0, 'final' => 6.0],
];

// 2. Điểm trung bình = (giữa kỳ + cuối kỳ) / 2, làm tròn 2 chữ số
function calculateAverage(float $midterm, float $final): float
{
    return round(($midterm + $final) / 2, 2);
}

function e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Bảng điểm 3 sinh viên</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; }
        table { border-collapse: collapse; }
        th, td { border: 1px solid #c3cad3; padding: 8px 14px; text-align: center; }
        th { background: #f3f5f8; }
        td.name { text-align: left; }
        .pass { color: #1e7a3c; font-weight: bold; }
        .fail { color: #b3261e; font-weight: bold; }
    </style>
</head>
<body>
<h1>Bảng điểm</h1>

<table>
    <tr>
        <th>STT</th>
        <th>Họ tên</th>
        <th>Giữa kỳ</th>
        <th>Cuối kỳ</th>
        <th>Điểm TB</th>
        <th>Kết quả</th>
    </tr>
    <?php foreach ($students as $i => $student): ?>
        <?php
        // 3. Tính trong vòng lặp để in từng dòng
        $average = calculateAverage($student['midterm'], $student['final']);
        // 4. Đạt nếu điểm trung bình >= 5
        $passed = $average >= 5;
        ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <!-- 5. Mã hóa tên trước khi in -->
            <td class="name"><?= e($student['name']) ?></td>
            <td><?= number_format($student['midterm'], 1) ?></td>
            <td><?= number_format($student['final'], 1) ?></td>
            <td><?= number_format($average, 2) ?></td>
            <td class="<?= $passed ? 'pass' : 'fail' ?>"><?= $passed ? 'Đạt' : 'Chưa đạt' ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>