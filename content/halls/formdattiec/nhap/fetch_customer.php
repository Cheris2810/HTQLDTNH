<?php
$conn = new mysqli("localhost", "root", "", "tochuctiec");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$makh = $_GET['makh'];
$query = "SELECT * FROM khachhang WHERE makh = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $makh);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);
$stmt->close();
$conn->close();
?>
