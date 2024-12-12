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

// Kiểm tra và xử lý yêu cầu POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra xem sopdt có tồn tại
    if (isset($_POST['sopdt']) && !empty($_POST['sopdt'])) {
        $sopdt = $_POST['sopdt'];
        $ten_btd = $_POST['ten_btd'];
        $gia_btd = $_POST['gia_btd'];
        
        if (isset($_POST['selected_items'])) {
            $selected_items = json_decode($_POST['selected_items']);
            $insertSuccess = true;

            // Lặp qua các đồ uống và thêm vào bảng datdouong
            foreach ($selected_items as $item) {
                // Thực hiện truy vấn để thêm dữ liệu vào bảng datdouong
                $query = "INSERT INTO datdouong (sopdt, ma_du, sl_sudung, sl_dukien) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $sl = 0; // Số lượng dự kiến mặc định là 0
                if ($stmt) {
                    $stmt->bind_param('iiii', $sopdt, $item->id, $item->quantity, $sl);
                    if (!$stmt->execute()) {
                        $insertSuccess = false; // Ghi nhận lỗi nếu chèn thất bại
                    }
                    $stmt->close();
                } else {
                    $insertSuccess = false;
                }
            }

            if ($insertSuccess) {
                // Cập nhật trạng thái của phiếu đặt tiệc trong bảng phieudattiec
                $updateQuery = "UPDATE phieudattiec SET trangthai = 'Hoàn chỉnh' WHERE sopdt = ? AND trangthai = 'Chưa hoàn chỉnh'";
                $updateStmt = $conn->prepare($updateQuery);
                if ($updateStmt) {
                    $updateStmt->bind_param('i', $sopdt);
                    if ($updateStmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Dữ liệu đã được lưu thành công và trạng thái đã được cập nhật thành "Hoàn chỉnh"!']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Lưu dữ liệu thành công nhưng cập nhật trạng thái thất bại!']);
                    }
                    $updateStmt->close();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Lưu dữ liệu thành công nhưng không thể chuẩn bị câu truy vấn cập nhật trạng thái!']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Lưu dữ liệu thất bại!']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đồ uống!']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Số phiếu đặt tiệc không hợp lệ!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức yêu cầu không hợp lệ!']);
}

$conn->close();
?>
