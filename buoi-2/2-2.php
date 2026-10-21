<?php
// Mỗi dòng: [biểu thức hiển thị, kết quả thực tế, giải thích]
$rows = [
    ['0 == "0"',   0 == "0",   '== cho phép đổi kiểu: "0" thành số 0, rồi so 0 == 0'],
    ['0 === "0"',  0 === "0",  '=== so cả kiểu: int khác string nên false'],
    ['false == 0', false == 0, '== đổi 0 thành bool (false), rồi so false == false'],
];

function show(bool $b): string
{
    return $b ? 'true' : 'false';
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>So sánh == và ===</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; }
        table { border-collapse: collapse; }
        th, td { border: 1px solid #c3cad3; padding: 8px 14px; text-align: left; }
        th { background: #f3f5f8; }
        .true  { color: #1e7a3c; font-weight: bold; }
        .false { color: #b3261e; font-weight: bold; }
        code { background: #f3f5f8; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
<h1>So sánh <code>==</code> và <code>===</code> trong PHP</h1>

<table>
    <tr>
        <th>Biểu thức</th>
        <th>Kết quả</th>
        <th>Giải thích</th>
    </tr>
    <?php foreach ($rows as [$expr, $result, $note]): ?>
        <tr>
            <td><code><?= htmlspecialchars($expr, ENT_QUOTES, 'UTF-8') ?></code></td>
            <td class="<?= show($result) ?>"><?= show($result) ?></td>
            <td><?= htmlspecialchars($note, ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>