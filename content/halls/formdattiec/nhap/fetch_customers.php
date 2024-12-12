<?php
$conn = new mysqli("localhost", "root", "", "tochuctiec");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$query = "SELECT makh, hoten FROM khachhang";
$result = $conn->query($query);
$customers = [];

while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

echo json_encode($customers);
$conn->close();
?>
