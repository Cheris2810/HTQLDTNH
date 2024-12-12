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

// Kiểm tra xem có dữ liệu được gửi qua POST không
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_ma = $_POST['ten_ma'];
    $ma_loaimon = $_POST['ma_loaimon'];
    $ma_nhommon = $_POST['ma_nhommon'];

    // Xử lý upload hình ảnh
    $target_dir = "uploads/"; // Thư mục lưu trữ hình ảnh
    $target_file = $target_dir . basename($_FILES["hinh_anh"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Kiểm tra kích thước hình ảnh
    if ($_FILES["hinh_anh"]["size"] > 500000) {
        echo json_encode(['status' => 'error', 'message' => 'Kích thước hình ảnh quá lớn.']);
        $uploadOk = 0;
    }

    // Kiểm tra định dạng file hình ảnh
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo json_encode(['status' => 'error', 'message' => 'Chỉ chấp nhận định dạng JPG, JPEG, PNG.']);
        $uploadOk = 0;
    }

    // Kiểm tra nếu không có lỗi
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_file)) {
            // Chèn món ăn vào cơ sở dữ liệu
            $sql = "INSERT INTO monan (ten_ma, ma_loaimon, ma_nhommon, hinh_anh) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $ten_ma, $ma_loaimon, $ma_nhommon, $target_file);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'ma_ma' => $conn->insert_id, 'ten_ma' => $ten_ma, 'hinh_anh' => $target_file]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Không thể thêm món ăn.']);
            }

            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Có lỗi trong việc upload hình ảnh.']);
        }
    }
}

$conn->close();
?>
