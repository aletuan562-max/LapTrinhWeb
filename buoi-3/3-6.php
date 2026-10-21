<?php
declare(strict_types=1);

const UPLOAD_DIR  = __DIR__ . '/uploads/';
const MAX_SIZE    = 2 * 1024 * 1024;   // 2 MB
const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'webp'];
// MIME thật của file -> phần mở rộng sẽ dùng khi lưu (không dùng đuôi do người dùng gửi)
const ALLOWED_MIME = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
];

function e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
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
 * Trả về [thông báo lỗi|null, phần mở rộng an toàn|null].
 */
function validateAvatar(array $file): array
{
    // Kiểm tra lỗi upload do PHP báo
    switch ($file['error'] ?? UPLOAD_ERR_NO_FILE) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return ['Vui lòng chọn một ảnh.', null];
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

    // KIỂM TRA 1: kích thước tối đa
    if (filesize($tmp) > MAX_SIZE) {
        return ['Ảnh vượt quá dung lượng cho phép (tối đa 2 MB).', null];
    }

    // KIỂM TRA 2: định dạng ảnh cho phép (đuôi file + MIME thật + đọc được như ảnh)
    $ext = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXT, true)) {
        return ['Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP.', null];
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
    if (!isset(ALLOWED_MIME[$mime]) || @getimagesize($tmp) === false) {
        return ['File không phải ảnh hợp lệ.', null];
    }

    return [null, ALLOWED_MIME[$mime]];
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['avatar'] ?? null;

    if (empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        // Dữ liệu gửi lên vượt post_max_size nên PHP bỏ trống cả $_FILES
        $error = 'Dung lượng gửi lên quá lớn.';
    } elseif (!is_array($file) || is_array($file['name'] ?? null)) {
        $error = 'Dữ liệu gửi lên không hợp lệ.';
    } else {
        [$error, $ext] = validateAvatar($file);

        if ($error === null) {
            ensureUploadDir();
            // KIỂM TRA 3: tên file mới ngẫu nhiên, không dùng tên người dùng gửi
            $newName = bin2hex(random_bytes(16)) . '.' . $ext;

            if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $newName)) {
                header('Location: 3-6.php?uploaded=' . $newName);
                exit;
            }
            $error = 'Không lưu được file lên máy chủ.';
        }
    }
}

// Chỉ hiển thị file có tên đúng mẫu do chương trình tạo và thực sự tồn tại
$uploaded = null;
$q = $_GET['uploaded'] ?? '';
if (is_string($q) && preg_match('/^[a-f0-9]{32}\.(jpg|png|webp)$/', $q) && is_file(UPLOAD_DIR . $q)) {
    $uploaded = $q;
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Upload ảnh đại diện</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; max-width: 440px; }
        .error { color: #b3261e; margin-top: 10px; }
        .ok { background: #e6f4ea; color: #1e5e34; padding: 10px; border-radius: 6px; }
        img { margin-top: 10px; border-radius: 8px; border: 1px solid #c3cad3; }
        button { margin-top: 12px; padding: 8px 18px; }
    </style>
</head>
<body>
<h1>Upload ảnh đại diện</h1>
<p>Chấp nhận JPG, PNG, WEBP, tối đa 2 MB.</p>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp">
    <br>
    <button type="submit">Tải lên</button>
</form>

<?php if ($error !== null): ?>
    <p class="error"><?= e($error) ?></p>
<?php endif; ?>

<?php if ($uploaded !== null): ?>
    <p class="ok">Tải lên thành công.</p>
    <img src="uploads/<?= e($uploaded) ?>" alt="Ảnh đại diện" width="160">
<?php endif; ?>
</body>
</html>