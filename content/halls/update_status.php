<?php
$SERVER = "localhost";
$user = "root"; 
$pass = ""; 
$database = "tochuctiec";

$conn = new mysqli($SERVER, $user, $pass, $database);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if (isset($_POST['table_id']) && isset($_POST['action'])) {
    $table_id = (int)$_POST['table_id'];

    if ($_POST['action'] === 'book') {
        // Đặt bàn, chuyển trạng thái về booked
        $sql = "UPDATE sanh SET trangthai_sanh = 'booked' WHERE stt_sanh = $table_id";
        if ($conn->query($sql) === TRUE) {
            echo 'success';
        } else {
            echo 'error';
        }
    } elseif ($_POST['action'] === 'cancel') {
        // Hủy đặt bàn, chuyển trạng thái về available
        $sql = "UPDATE sanh SET trangthai_sanh = 'available' WHERE stt_sanh = $table_id";
        if ($conn->query($sql) === TRUE) {
            echo 'success';
        } else {
            echo 'error';
        }
    }
}

$conn->close();
?>
