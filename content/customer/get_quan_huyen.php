<?php
$SERVER = "localhost";
$user = "root";
$pass = "";
$database = "tochuctiec";

$conn = new mysqli($SERVER, $user, $pass, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$matp = $_GET['ma_tinhtp'];
$sql = "SELECT maqh, tenqh FROM quan_huyen WHERE ma_tinhtp = '$matp'";
$result = $conn->query($sql);

$quan_huyen = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $quan_huyen[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($quan_huyen);
?>
