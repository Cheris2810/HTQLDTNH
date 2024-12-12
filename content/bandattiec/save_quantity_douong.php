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

// Kiểm tra dữ liệu đầu vào
if (isset($_GET['id'])) {
    $ma_du = $_GET['id']; // Lấy mã đồ uống từ tham số GET

    // Truy vấn giá từ bảng giadouong
    $query = "SELECT giadouong FROM giadouong WHERE ma_du = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $ma_du);
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();

    // Kiểm tra và trả về giá đồ uống
    if ($price !== null) {
        echo json_encode(['success' => true, 'price' => $price]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy giá đồ uống']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Mã đồ uống không hợp lệ']);
}

$conn->close();
?>
