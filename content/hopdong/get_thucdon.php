<?php
$SERVER = "localhost";
$user = "root";
$pass = "";
$database = "tochuctiec";

// Kết nối tới cơ sở dữ liệu
$conn = new mysqli($SERVER, $user, $pass, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT b.ten_btd, b.gia_btd FROM bantiecdat b INNER JOIN dattiec d ON b.sopdt = d.sopdt WHERE d.sopdt = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $sopdt);
$stmt->execute();
$result = $stmt->get_result();

$thucdon = [];
while ($row = $result->fetch_assoc()) {
    $thucdon[] = $row;
}

echo json_encode($thucdon);
$conn->close();
?>
