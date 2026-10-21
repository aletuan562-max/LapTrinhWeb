<?php
declare(strict_types=1);

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

function validateRegistration(array $data): array
{
    $errors = [];
    if ($data['name'] === '') {
        $errors['name'] = 'Vui lòng nhập họ tên.';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không hợp lệ.';
    }
    return $errors;
}

// CHƯA lưu thật: chỉ giả lập thành công. Bài sau sẽ thay bằng PDO + prepared statement.
function saveToDatabase(array $data): bool
{
    return true;
}

$data = ['name' => '', 'email' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Bước 1: đọc POST
    $data['name']  = post('name');
    $data['email'] = post('email');

    // Bước 2: validate
    $errors = validateRegistration($data);

    // Bước 3: nếu hợp lệ thì lưu DB (nếu lỗi: không lưu, rơi xuống HTML để hiển thị lỗi)
    if (!$errors) {
        if (saveToDatabase($data)) {
            // Bước 4: redirect (POST-Redirect-GET), phải gọi exit ngay sau header()
            header('Location: 3-1.php?saved=1');
            exit;
        }
        $errors['form'] = 'Không lưu được dữ liệu, vui lòng thử lại.';
    }
}

$saved = ($_GET['saved'] ?? '') === '1';
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Luồng xử lý form</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; max-width: 420px; }
        label { display: block; font-weight: bold; margin: 12px 0 4px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        .error { color: #b3261e; font-size: 14px; margin-top: 4px; }
        .ok { background: #e6f4ea; color: #1e5e34; padding: 10px; border-radius: 6px; }
        button { margin-top: 16px; padding: 8px 18px; }
    </style>
</head>
<body>
<h1>Đăng ký</h1>

<?php if ($saved): ?>
    <p class="ok">Đăng ký thành công. Thử bấm F5: trang sẽ không gửi lại form.</p>
<?php endif; ?>

<?php if (isset($errors['form'])): ?>
    <p class="error"><?= e($errors['form']) ?></p>
<?php endif; ?>

<form method="post" novalidate>
    <label for="name">Họ tên</label>
    <input id="name" name="name" value="<?= e($data['name']) ?>">
    <?php if (isset($errors['name'])): ?><div class="error"><?= e($errors['name']) ?></div><?php endif; ?>

    <label for="email">Email</label>
    <input id="email" name="email" value="<?= e($data['email']) ?>">
    <?php if (isset($errors['email'])): ?><div class="error"><?= e($errors['email']) ?></div><?php endif; ?>

    <button type="submit">Gửi</button>
</form>
</body>
</html>