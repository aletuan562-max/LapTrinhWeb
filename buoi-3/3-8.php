<?php
declare(strict_types=1);

const UPLOAD_DIR   = __DIR__ . '/uploads/';
const MAX_SIZE     = 2 * 1024 * 1024;   // 2 MB
const ALLOWED_EXT  = ['jpg', 'jpeg', 'png', 'webp'];
const ALLOWED_MIME = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
];

// Danh sách chủ đề cho phép (khóa gửi lên => nhãn hiển thị)
$subjects = [
    'support'     => 'Hỗ trợ kỹ thuật',
    'feedback'    => 'Góp ý',
    'cooperation' => 'Hợp tác',
    'other'       => 'Khác',
];

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

// Tạo thư mục uploads và chặn thực thi mã PHP trong đó (Apache)
function ensureUploadDir(): void
{
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    $htaccess = UPLOAD_DIR . '.htaccess';
    if (!file_exists($htaccess)) {
        $rules = <<<'HT'
<FilesMatch "\.(php|phtml|phar)$">
    Require all denied
</FilesMatch>
HT;
        file_put_contents($htaccess, $rules);
    }
}

/**
 * 3 kiểm tra bắt buộc cho ảnh đại diện.
 * Trả về [thông báo lỗi|null, phần mở rộng an toàn|null].
 */
function validateAvatar(array $file): array
{
    switch ($file['error'] ?? UPLOAD_ERR_NO_FILE) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['Ảnh vượt quá dung lượng cho phép (tối đa 2 MB).', null];
        default:
            return ['Upload thất bại, vui lòng thử lại.', null];
    }

    $tmp = (string)$file['tmp_name'];
    if (!is_uploaded_file($tmp)) {
        return ['File không hợp lệ.', null];
    }

    // Kiểm tra 1: kích thước tối đa
    if (filesize($tmp) > MAX_SIZE) {
        return ['Ảnh vượt quá dung lượng cho phép (tối đa 2 MB).', null];
    }

    // Kiểm tra 2: định dạng ảnh cho phép (đuôi file + MIME thật + đọc được như ảnh)
    $ext = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXT, true)) {
        return ['Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP.', null];
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
    if (!isset(ALLOWED_MIME[$mime]) || @getimagesize($tmp) === false) {
        return ['File không phải ảnh hợp lệ.', null];
    }

    // (Kiểm tra 3 - tên file mới an toàn - thực hiện khi lưu file)
    return [null, ALLOWED_MIME[$mime]];
}

$data = ['name' => '', 'email' => '', 'subject' => 'support', 'message' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['name']    = post('name');
    $data['email']   = post('email');
    $data['subject'] = post('subject');
    $data['message'] = post('message');

    if (empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        // Dữ liệu vượt post_max_size nên PHP bỏ trống cả $_POST lẫn $_FILES
        $errors['form'] = 'Dung lượng gửi lên quá lớn.';
    } else {
        // Họ tên: không rỗng
        if ($data['name'] === '') {
            $errors['name'] = 'Vui lòng nhập họ tên.';
        } elseif (mb_strlen($data['name'], 'UTF-8') > 100) {
            $errors['name'] = 'Họ tên tối đa 100 ký tự.';
        }

        // Email: đúng định dạng
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ.';
        }

        // Chủ đề: phải nằm trong danh sách cho phép (giá trị select có thể bị sửa)
        if (!isset($subjects[$data['subject']])) {
            $errors['subject'] = 'Chủ đề không hợp lệ.';
        }

        // Nội dung: không rỗng, 10-500 ký tự (mb_strlen để đếm đúng chữ tiếng Việt)
        $len = mb_strlen($data['message'], 'UTF-8');
        if ($data['message'] === '') {
            $errors['message'] = 'Vui lòng nhập nội dung.';
        } elseif ($len < 10 || $len > 500) {
            $errors['message'] = 'Nội dung phải từ 10 đến 500 ký tự.';
        }

        // Ảnh đại diện: không bắt buộc chọn, nhưng nếu chọn thì phải qua kiểm tra
        $file = $_FILES['avatar'] ?? null;
        $ext = null;
        $hasAvatar = false;
        if (is_array($file) && !is_array($file['name'] ?? null)
            && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            [$avatarError, $ext] = validateAvatar($file);
            if ($avatarError !== null) {
                $errors['avatar'] = $avatarError;
            } else {
                $hasAvatar = true;
            }
        }

        // Chỉ lưu file sau khi mọi trường đều hợp lệ
        if (!$errors) {
            $location = '3-8.php?sent=1';
            if ($hasAvatar) {
                ensureUploadDir();
                // Kiểm tra 3: tên file mới ngẫu nhiên, không dùng tên người dùng gửi
                $newName = bin2hex(random_bytes(16)) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $newName)) {
                    $location .= '&avatar=' . $newName;
                } else {
                    $errors['avatar'] = 'Không lưu được ảnh lên máy chủ.';
                }
            }
            if (!$errors) {
                header('Location: ' . $location);   // POST-Redirect-GET
                exit;
            }
        }
    }
}

// Thông báo thành công (sau redirect). Chỉ hiện ảnh nếu tên đúng mẫu và file tồn tại.
$sent = ($_GET['sent'] ?? '') === '1';
$avatarShown = null;
$q = $_GET['avatar'] ?? '';
if ($sent && is_string($q) && preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/', $q) && is_file(UPLOAD_DIR . $q)) {
    $avatarShown = $q;
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liên hệ</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f3f5f8; margin: 0; padding: 32px 16px; color: #1f2d3d; }
        .card { max-width: 480px; margin: 0 auto; background: #fff; padding: 28px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
        h1 { text-align: center; color: #1f4e79; margin: 0 0 6px; }
        p.hint { text-align: center; color: #6b7785; font-size: 14px; margin: 0 0 12px; }
        label { display: block; font-weight: bold; font-size: 14px; margin: 16px 0 6px; }
        input[type="text"], select, textarea { width: 100%; padding: 10px; border: 1px solid #c3cad3; border-radius: 8px; font-size: 15px; font-family: inherit; }
        textarea { min-height: 120px; resize: vertical; }
        .invalid { border-color: #d93025 !important; background: #fff5f5; }
        .error { color: #b3261e; font-size: 13px; margin-top: 4px; }
        button { margin-top: 22px; width: 100%; padding: 12px; border: 0; border-radius: 8px; background: #386bab; color: #fff; font-size: 16px; font-weight: bold; cursor: pointer; }
        button:hover { background: #2d5a92; }
        .ok { background: #e6f4ea; color: #1e5e34; padding: 12px; border-radius: 8px; margin-bottom: 8px; text-align: center; }
        .ok img { display: block; margin: 10px auto 0; border-radius: 50%; width: 96px; height: 96px; object-fit: cover; }
    </style>
</head>
<body>
<div class="card">
    <h1>Liên hệ</h1>
    <p class="hint">Vui lòng nhập đầy đủ thông tin bên dưới.</p>

    <?php if ($sent): ?>
        <div class="ok">
            Cảm ơn bạn, thông tin liên hệ đã được gửi thành công.
            <?php if ($avatarShown !== null): ?>
                <img src="uploads/<?= e($avatarShown) ?>" alt="Ảnh đại diện">
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($errors['form'])): ?><div class="error"><?= e($errors['form']) ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
        <label for="name">Họ tên</label>
        <input type="text" id="name" name="name" value="<?= e($data['name']) ?>" class="<?= isset($errors['name']) ? 'invalid' : '' ?>">
        <?php if (isset($errors['name'])): ?><div class="error"><?= e($errors['name']) ?></div><?php endif; ?>

        <label for="email">Email</label>
        <input type="text" id="email" name="email" value="<?= e($data['email']) ?>" class="<?= isset($errors['email']) ? 'invalid' : '' ?>">
        <?php if (isset($errors['email'])): ?><div class="error"><?= e($errors['email']) ?></div><?php endif; ?>

        <label for="subject">Chủ đề</label>
        <select id="subject" name="subject" class="<?= isset($errors['subject']) ? 'invalid' : '' ?>">
            <?php foreach ($subjects as $key => $label): ?>
                <option value="<?= e($key) ?>" <?= $data['subject'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['subject'])): ?><div class="error"><?= e($errors['subject']) ?></div><?php endif; ?>

        <label for="message">Nội dung</label>
        <textarea id="message" name="message" placeholder="Nhập nội dung liên hệ..." class="<?= isset($errors['message']) ? 'invalid' : '' ?>"><?= e($data['message']) ?></textarea>
        <?php if (isset($errors['message'])): ?><div class="error"><?= e($errors['message']) ?></div><?php endif; ?>

        <label for="avatar">Ảnh đại diện (không bắt buộc)</label>
        <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp">
        <?php if (isset($errors['avatar'])): ?><div class="error"><?= e($errors['avatar']) ?></div><?php endif; ?>

        <button type="submit">Gửi liên hệ</button>
    </form>
</div>
</body>
</html>