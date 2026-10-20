<?php
$score = trim($_POST['score'] ?? '');
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg = (is_numeric($score) && $score >= 0 && $score <= 10)
        ? 'Điểm hợp lệ'
        : 'Điểm không hợp lệ (phải là số từ 0 đến 10)';
}
?>
<form method="post">
    <input name="score" value="<?= htmlspecialchars($score) ?>">
    <button>Kiểm tra</button>
</form>
<p><?= $msg ?></p>