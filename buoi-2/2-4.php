<?php
// Mảng lồng nhau: mỗi phần tử là một mảng kết hợp gồm name và score
$students = [
    ['name' => 'An',   'score' => 8.0],
    ['name' => 'Bình', 'score' => 6.5],
    ['name' => 'Chi',  'score' => 4.5],
];
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Danh sách sinh viên</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; }
        table { border-collapse: collapse; }
        th, td { border: 1px solid #c3cad3; padding: 8px 14px; text-align: left; }
        th { background: #f3f5f8; }
    </style>
</head>
<body>
<h1>Danh sách sinh viên</h1>

<table>
    <tr>
        <th>STT</th>
        <th>Tên</th>
        <th>Điểm</th>
    </tr>
    <?php foreach ($students as $i => $student): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($student['name'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= $student['score'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>