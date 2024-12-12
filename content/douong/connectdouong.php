<?php
// Kết nối đến cơ sở dữ liệu
$SERVER = "localhost";
$user = "root"; // Tên đăng nhập MySQL
$pass = ""; // Mật khẩu MySQL
$database = "tochuctiec"; // Tên cơ sở dữ liệu

$conn = new mysqli($SERVER, $user, $pass, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra xem form đã được submit chưa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $ten_du = $_POST['ten_du'];
    $madvt = $_POST['madvt'];

    // Xử lý file hình ảnh
    if (isset($_FILES['hinh_anh_du']) && $_FILES['hinh_anh_du']['error'] == 0) {
        $image_tmp = $_FILES['hinh_anh_du']['tmp_name'];
        $image_name = $_FILES['hinh_anh_du']['name'];
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $image_new_name = uniqid('img_') . '.' . $image_extension;
        $image_path = 'uploads/' . $image_new_name; // Đường dẫn lưu ảnh

        // Di chuyển hình ảnh vào thư mục uploads
        if (move_uploaded_file($image_tmp, $image_path)) {
            // Thực hiện truy vấn INSERT vào cơ sở dữ liệu
            $sql = "INSERT INTO douong (ten_du, madvt, hinh_anh_du) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sis", $ten_du, $madvt, $image_path); // s: string, i: integer
            if ($stmt->execute()) {
                $response = [
                    'status' => 'success',
                    'ma_du' => $conn->insert_id,
                    'ten_du' => $ten_du,
                    'hinh_anh_du' => $image_path
                ];
                echo json_encode($response); // Gửi phản hồi dưới dạng JSON
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Lỗi khi thêm đồ uống: ' . $conn->error]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi khi tải hình ảnh lên!']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng chọn hình ảnh!']);
    }
}
?>
