<?php
header('Content-Type: application/json');

$SERVER = "localhost";
$user = "root";
$pass = "";
$database = "tochuctiec";

$conn = new mysqli($SERVER, $user, $pass, $database);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Kết nối cơ sở dữ liệu thất bại!']));
}

if (isset($_GET['id_du']) && isset($_GET['madvt'])) {
    $ma_du = $_GET['id_du'];
    $madvt = $_GET['madvt'];

    // Debug thông tin nhận được
    file_put_contents("debug.log", "ma_du: $ma_du, madvt: $madvt\n", FILE_APPEND);

    $query = "
        SELECT giadouong.giadouong 
        FROM giadouong 
        WHERE giadouong.ma_du = ? AND giadouong.madvt = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Lỗi chuẩn bị truy vấn: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("ss", $ma_du, $madvt);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Lỗi ràng buộc tham số!']);
        exit;
    }
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();
    $stmt->close();

    // Debug giá trị trả về
    file_put_contents("debug.log", "Price: $price\n", FILE_APPEND);

    if ($price !== null) {
        echo json_encode(['success' => true, 'price' => $price]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy giá đồ uống với thông tin này!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ!']);
}

$conn->close();
?>
