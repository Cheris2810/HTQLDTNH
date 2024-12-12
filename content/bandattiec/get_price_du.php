<?php
header('Content-Type: application/json');

// Kết nối cơ sở dữ liệu
$SERVER = "localhost";
$user = "root";
$pass = "";
$database = "tochuctiec";
$conn = new mysqli($SERVER, $user, $pass, $database);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối cơ sở dữ liệu thất bại!']);
    exit;
}

// Kiểm tra và xử lý yêu cầu GET
if (isset($_GET['ma_du'])) {
    $ma_du = $_GET['ma_du'];

    // Ghi debug thông tin đầu vào
    file_put_contents("debug.log", "Input ma_du (GET): $ma_du\n", FILE_APPEND);

    // Truy vấn giá đồ uống từ bảng giadouong liên kết với bảng douong
    $query = "
        SELECT giadouong.giadouong, douong.ten_du
        FROM giadouong
        INNER JOIN douong ON giadouong.ma_du = douong.ma_du
        WHERE giadouong.ma_du = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Lỗi chuẩn bị truy vấn: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $ma_du);  // Sử dụng kiểu dữ liệu int cho ma_du
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Lỗi thực thi truy vấn!']);
        exit;
    }
    $stmt->bind_result($giadouong, $ten_du);
    $stmt->fetch();
    $stmt->close();

    // Debug giá trị trả về
    file_put_contents("debug.log", "Truy vấn giá: $giadouong, Tên đồ uống: $ten_du\n", FILE_APPEND);

    if ($giadouong !== null) {
        echo json_encode(['success' => true, 'ten_du' => $ten_du, 'giadouong' => $giadouong]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy đồ uống với mã này!']);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Xử lý yêu cầu POST
    // Nhận dữ liệu JSON từ POST
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Kiểm tra xem ma_du có tồn tại và hợp lệ không
    if (isset($inputData['ma_du']) && !empty($inputData['ma_du'])) {
        $ma_du = $inputData['ma_du'];

        // Truy vấn giá đồ uống từ bảng giadouong và liên kết với douong
        $query = "SELECT giadouong.giadouong FROM giadouong
                  INNER JOIN douong ON giadouong.ma_du = douong.ma_du
                  WHERE giadouong.ma_du = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $ma_du);  // Sử dụng kiểu dữ liệu int cho ma_du
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if ($data) {
            echo json_encode(['success' => true, 'giadouong' => $data['giadouong']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy giá đồ uống!']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Tham số không hợp lệ!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức yêu cầu không hợp lệ!']);
}

$conn->close();
?>
