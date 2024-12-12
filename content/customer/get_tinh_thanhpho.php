<?php
$SERVER = "localhost";
$user = "root";
$pass = "";
$database = "tochuctiec";

// Kết nối CSDL
$conn = new mysqli($SERVER, $user, $pass, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy danh sách tỉnh/thành phố
$sql = "SELECT matinhtp, ten_tinhtp FROM tinh_thanhpho";
$result = $conn->query($sql);

$tinh_thanhpho = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $tinh_thanhpho[] = $row;
    }
}

$conn->close();

// Trả về JSON
header('Content-Type: application/json');
echo json_encode($tinh_thanhpho);
?>
