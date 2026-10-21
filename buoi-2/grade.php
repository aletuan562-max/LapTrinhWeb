<?php
declare(strict_types=1);

// Chuẩn hóa: bỏ khoảng trắng, đổi dấu phẩy thành dấu chấm (8,5 -> 8.5)
function normalizeScore(string $raw): string
{
    return str_replace(',', '.', trim($raw));
}

// Hợp lệ: là số (không dấu, không ký hiệu khoa học) và nằm trong 0..10
function isValidScore(string $raw): bool
{
    $s = normalizeScore($raw);
    return preg_match('/^\d+(\.\d+)?$/', $s) === 1
        && (float)$s >= 0
        && (float)$s <= 10;
}

// 10% chuyên cần + 30% giữa kỳ + 60% cuối kỳ (làm tròn 2 chữ số để so sánh chính xác)
function finalScore(float $cc, float $mid, float $final): float
{
    return round($cc * 0.10 + $mid * 0.30 + $final * 0.60, 2);
}

// Kiểm tra từ cao xuống thấp
function xepLoai(float $score): string
{
    if ($score >= 8) {
        return 'Giỏi';
    } elseif ($score >= 6.5) {
        return 'Khá';
    } elseif ($score >= 5) {
        return 'Trung bình';
    }
    return 'Chưa đạt';
}

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

$fields = [
    'cc'    => 'Điểm chuyên cần',
    'mid'   => 'Điểm giữa kỳ',
    'final' => 'Điểm cuối kỳ',
];

$name = post('name');
$values = [];
foreach ($fields as $key => $label) {
    $values[$key] = post($key);
}

$errors = [];
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($name === '') {
        $errors['name'] = 'Vui lòng nhập họ tên.';
    }

    foreach ($fields as $key => $label) {
        if ($values[$key] === '') {
            $errors[$key] = "Vui lòng nhập $label.";
        } elseif (!isValidScore($values[$key])) {
            $errors[$key] = "$label phải là số từ 0 đến 10.";
        }
    }

    if (!$errors) {
        $score = finalScore(
            (float)normalizeScore($values['cc']),
            (float)normalizeScore($values['mid']),
            (float)normalizeScore($values['final'])
        );
        $result = ['score' => $score, 'grade' => xepLoai($score)];
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Máy tính điểm học phần</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f3f5f8; margin: 0; padding: 32px 16px; color: #1f2d3d; }
        .card { max-width: 440px; margin: 0 auto; background: #fff; padding: 28px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
        h1 { font-size: 22px; margin: 0 0 4px; }
        p.hint { margin: 0 0 18px; color: #6b7785; font-size: 14px; }
        label { display: block; font-weight: bold; margin: 14px 0 6px; }
        input { width: 100%; padding: 10px; border: 1px solid #c3cad3; border-radius: 8px; font-size: 16px; }
        input.invalid { border-color: #d93025; background: #fff5f5; }
        .error { color: #b3261e; font-size: 13px; margin-top: 4px; }
        button { margin-top: 20px; width: 100%; padding: 11px; border: 0; border-radius: 8px; background: #1f6fb2; color: #fff; font-size: 16px; cursor: pointer; }
        button:hover { background: #185a92; }
        .result { margin-top: 20px; padding: 14px; border-radius: 8px; background: #e6f4ea; color: #1e5e34; }
        .result strong { font-size: 18px; }
    </style>
</head>
<body>
<div class="card">
    <h1>Máy tính điểm học phần</h1>
    <p class="hint">10% chuyên cần + 30% giữa kỳ + 60% cuối kỳ. Mỗi điểm từ 0 đến 10.</p>

    <form method="post" novalidate>
        <label for="name">Họ tên</label>
        <input id="name" name="name" value="<?= e($name) ?>" class="<?= isset($errors['name']) ? 'invalid' : '' ?>">
        <?php if (isset($errors['name'])): ?><div class="error"><?= e($errors['name']) ?></div><?php endif; ?>

        <?php foreach ($fields as $key => $label): ?>
            <label for="<?= e($key) ?>"><?= e($label) ?></label>
            <input id="<?= e($key) ?>" name="<?= e($key) ?>" inputmode="decimal"
                   value="<?= e($values[$key]) ?>" class="<?= isset($errors[$key]) ? 'invalid' : '' ?>">
            <?php if (isset($errors[$key])): ?><div class="error"><?= e($errors[$key]) ?></div><?php endif; ?>
        <?php endforeach; ?>

        <button type="submit">Tính điểm</button>
    </form>

    <?php if ($result !== null): ?>
        <div class="result">
            <?= e($name) ?>: điểm tổng kết <strong><?= number_format($result['score'], 2) ?></strong>,
            xếp loại <strong><?= e($result['grade']) ?></strong>.
        </div>
    <?php endif; ?>
</div>
</body>
</html>