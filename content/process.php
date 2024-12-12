<?php
$SERVER = "localhost";
$user = "root"; // Thay bằng tên người dùng của bạn
$pass = ""; // Thay bằng mật khẩu của bạn
$database = "tochuctiec"; // Thay bằng tên cơ sở dữ liệu của bạn

// Kết nối đến cơ sở dữ liệu
$conn = new mysqli($SERVER, $user, $pass, $database);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kết nối đến cơ sở dữ liệu
    $conn = new mysqli('SERVER', 'user', 'pass', 'database');

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Lấy dữ liệu từ form
    $name = htmlspecialchars($_POST['name']);
    $date = htmlspecialchars($_POST['date']);
    $time = htmlspecialchars($_POST['time']);
    $guests = htmlspecialchars($_POST['guests']);
    $hall = htmlspecialchars($_POST['hall']);

    // Kiểm tra và định dạng dữ liệu
    $dateTime = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
    if (!$dateTime) {
        die("Ngày hoặc giờ không hợp lệ.");
    }

    // Thực hiện truy vấn để lưu thông tin vào cơ sở dữ liệu (bạn có thể tạo bảng đặt tiệc)
    $sql = "INSERT INTO reservations (name, date, time, guests, hall) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssis", $name, $date, $time, $guests, $hall);

    if ($stmt->execute()) {
        echo "<h1>Thông Tin Đặt Tiệc</h1>";
        echo "<p><strong>Tên người đặt:</strong> $name</p>";
        echo "<p><strong>Ngày tiệc:</strong> $date</p>";
        echo "<p><strong>Giờ tiệc:</strong> $time</p>";
        echo "<p><strong>Số lượng khách:</strong> $guests</p>";
        echo "<p><strong>Sảnh đã chọn:</strong> $hall</p>";
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    // Đóng kết nối
    $stmt->close();
    $conn->close();
} else {
    echo "Yêu cầu không hợp lệ.";
}
?>
