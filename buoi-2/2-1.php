<?php
$x = "5";
$y = 5;
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Kiểu dữ liệu trong PHP</title>
</head>
<body>
<h1>Kiểu dữ liệu: x = "5"; y = 5;</h1>
<pre>
<?php
echo '$x là: ' . gettype($x) . "\n";   // string
echo '$y là: ' . gettype($y) . "\n";   // integer

echo "\n--- Phép cộng (+) ---\n";
var_dump($x + $y);                      // int(10)

echo "\n--- Nối chuỗi (.) ---\n";
var_dump($x . $y);                      // string(2) "55"

echo "\n--- Chuỗi số thực ---\n";
var_dump("5.5" + 5);                    // float(10.5)
?>
</pre>
</body>
</html>