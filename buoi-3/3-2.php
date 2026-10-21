<?php
declare(strict_types=1);

function e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
}

// Mặc định là chuỗi của đề bài: có khoảng trắng đầu/cuối, viết hoa lẫn thường
$raw = $_POST['email'] ?? ' A@Example.COM ';
if (!is_string($raw)) {
    $raw = '';
}

// Bước 1 - Sanitization: bỏ khoảng trắng thừa
$trimmed = trim($raw);

// Bước 2 - Sanitization (tùy quy ước hệ thống): đưa về chữ thường
$normalized = strtolower($trimmed);

// Bước 3 - Validation: đúng định dạng email chưa?
$isValid = filter_var($normalized, FILTER_VALIDATE_EMAIL) !== false;

// Bước 4 - Output encoding: dùng e() mỗi khi đưa chuỗi ra HTML (xem bảng bên dưới)
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Xử lý email</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; }
        input { padding: 6px; width: 260px; }
        table { border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #c3cad3; padding: 8px 14px; text-align: left; }
        th { background: #f3f5f8; }
        code { background: #f3f5f8; padding: 2px 6px; border-radius: 4px; }
        .pass { color: #1e7a3c; font-weight: bold; }
        .fail { color: #b3261e; font-weight: bold; }
    </style>
</head>
<body>
<h1>Xử lý email: trim, chữ thường, validate, encode</h1>

<form method="post">
    <input name="email" value="<?= e($raw) ?>">
    <button>Xử lý</button>
</form>

<table>
    <tr>
        <th>Bước</th>
        <th>Loại</th>
        <th>Kết quả (dấu [ ] để thấy khoảng trắng)</th>
    </tr>
    <tr>
        <td>Dữ liệu thô</td>
        <td>-</td>
        <td><code>[<?= e($raw) ?>]</code></td>
    </tr>
    <tr>
        <td>1. <code>trim()</code></td>
        <td>Sanitization</td>
        <td><code>[<?= e($trimmed) ?>]</code></td>
    </tr>
    <tr>
        <td>2. <code>strtolower()</code></td>
        <td>Sanitization (tùy quy ước)</td>
        <td><code>[<?= e($normalized) ?>]</code></td>
    </tr>
    <tr>
        <td>3. <code>filter_var(..., FILTER_VALIDATE_EMAIL)</code></td>
        <td>Validation</td>
        <td class="<?= $isValid ? 'pass' : 'fail' ?>"><?= $isValid ? 'Hợp lệ' : 'Không hợp lệ' ?></td>
    </tr>
    <tr>
        <td>4. <code>htmlspecialchars()</code></td>
        <td>Output encoding</td>
        <td><code><?= e($normalized) ?></code> (mọi chuỗi in ra HTML đều qua bước này)</td>
    </tr>
</table>
</body>
</html>