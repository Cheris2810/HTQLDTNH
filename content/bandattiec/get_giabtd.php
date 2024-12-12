<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kết nối cơ sở dữ liệu
    $conn = new mysqli("localhost", "root", "", "tochuctiec");
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Lấy sopdt và ten_btd từ dữ liệu gửi lên
    $data = json_decode(file_get_contents('php://input'), true);
    $sopdt = $data['sopdt'];
    $ten_btd = $data['ten_btd'];

    // Truy vấn để lấy gia_btd từ bảng bantiecdat thông qua stt_btd và đối chiếu với sopdt từ bảng dattiec
    $sql = "SELECT b.gia_btd 
            FROM bantiecdat b
            JOIN dattiec d ON b.stt_btd = d.stt_btd
            WHERE d.sopdt = '$sopdt' AND b.ten_btd = '$ten_btd'";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $gia_btd = $row['gia_btd']; // Lấy gia_btd từ bảng bantiecdat
        echo json_encode(['success' => true, 'gia_btd' => $gia_btd]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy gia_btd!']);
    }

    $conn->close();
}
?>
