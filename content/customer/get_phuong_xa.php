<?php
$SERVER = "localhost";
$user = "root";
$pass = "";
$database = "tochuctiec";

$conn = new mysqli($SERVER, $user, $pass, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$maqh = $_GET['ma_qh'];
$sql = "SELECT mapx, tenpx FROM phuong_xa WHERE ma_qh = '$maqh'";
$result = $conn->query($sql);

$phuong_xa = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $phuong_xa[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($phuong_xa);
?>
