<?php
$conn = new mysqli("localhost", "root", "", "tochuctiec");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$query = "SELECT malt, tenloaitiec FROM loaitiec";
$result = $conn->query($query);
$loaitiec = [];

while ($row = $result->fetch_assoc()) {
    $loaitiec[] = $row;
}

echo json_encode($loaitiec);
$conn->close();
?>
