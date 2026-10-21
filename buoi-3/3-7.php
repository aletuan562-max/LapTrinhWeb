<?php
function e($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

$errors = [];
$data = ['name' => '', 'email' => '', 'birthday' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name'] = trim($_POST['name'] ?? '');
    $data['email'] = trim($_POST['email'] ?? '');
    $data['birthday'] = trim($_POST['birthday'] ?? '');

    if ($data['name'] === '') $errors['name'] = 'Vui lòng nhập họ tên';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Email không hợp lệ';

    if ($data['birthday'] === '') {
        $errors['birthday'] = 'Vui lòng nhập ngày sinh';
    } else {
        // Bổ sung so với slide: ngày phải đúng định dạng và không ở tương lai
        $d = DateTime::createFromFormat('Y-m-d', $data['birthday']);
        if ($d === false || $d->format('Y-m-d') !== $data['birthday']) {
            $errors['birthday'] = 'Ngày sinh không hợp lệ';
        } elseif ($d > new DateTime('today')) {
            $errors['birthday'] = 'Ngày sinh không được ở tương lai';
        }
    }

    if (!$errors) {
        header('Location: success.php'); exit;
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Form đăng ký sinh viên</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; max-width: 420px; }
        label { display: block; font-weight: bold; margin: 14px 0 4px; }
        input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #c3cad3; border-radius: 6px; }
        input.invalid { border-color: #d93025; background: #fff5f5; }
        .error { color: #b3261e; font-size: 14px; margin-top: 4px; }
        button { margin-top: 18px; padding: 9px 20px; }
    </style>
</head>
<body>
<h1>Đăng ký sinh viên</h1>

<form method="post" novalidate>
    <label for="name">Họ tên</label>
    <input id="name" name="name" value="<?= e($data['name']) ?>" class="<?= isset($errors['name']) ? 'invalid' : '' ?>">
    <?php if (isset($errors['name'])): ?><div class="error"><?= e($errors['name']) ?></div><?php endif; ?>

    <label for="email">Email</label>
    <input id="email" name="email" value="<?= e($data['email']) ?>" class="<?= isset($errors['email']) ? 'invalid' : '' ?>">
    <?php if (isset($errors['email'])): ?><div class="error"><?= e($errors['email']) ?></div><?php endif; ?>

    <label for="birthday">Ngày sinh</label>
    <input id="birthday" name="birthday" type="date" value="<?= e($data['birthday']) ?>" class="<?= isset($errors['birthday']) ? 'invalid' : '' ?>">
    <?php if (isset($errors['birthday'])): ?><div class="error"><?= e($errors['birthday']) ?></div><?php endif; ?>

    <button type="submit">Đăng ký</button>
</form>
</body>
</html>