<?php
header('Content-Type: application/json');
// Kết nối đến cơ sở dữ liệu
$SERVER = "localhost";
$user = "root"; // Tên đăng nhập MySQL
$pass = ""; // Mật khẩu MySQL
$database = "tochuctiec"; // Tên cơ sở dữ liệu

$conn = new mysqli($SERVER, $user, $pass, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
if (isset($_GET['id'])) {
    $ma_ma = $_GET['id'];

    $query = "SELECT giabanle FROM giabanle WHERE ma_ma = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $ma_ma);
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();

    if ($price !== null) {
        echo json_encode(['success' => true, 'price' => $price]);
    } else {
        echo json_encode(['success' => false]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Mã món ăn không hợp lệ']);
}

$conn->close();
?>
