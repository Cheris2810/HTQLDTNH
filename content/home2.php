
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Sảnh Tổ Chức Tiệc</title>
</head>
<body>
    <h1>Đặt Sảnh Để Tổ Chức Tiệc</h1>
    <form action="process.php" method="POST">
        <label for="name">Tên người đặt:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="date">Ngày tiệc:</label>
        <input type="date" id="date" name="date" required><br><br>

        <label for="time">Giờ tiệc:</label>
        <input type="time" id="time" name="time" required><br><br>

        <label for="guests">Số lượng khách:</label>
        <input type="number" id="guests" name="guests" required><br><br>

        <label for="hall">Chọn sảnh:</label>
        <select id="hall" name="hall" required>
            <?php
                $SERVER = "localhost";
                $user = "root"; // Thay bằng tên người dùng của bạn
                $pass = ""; // Thay bằng mật khẩu của bạn
                $database = "tochuctiec"; // Thay bằng tên cơ sở dữ liệu của bạn

                // Kết nối đến cơ sở dữ liệu
                $conn = new mysqli($SERVER, $user, $pass, $database);

            // Lấy danh sách sảnh từ cơ sở dữ liệu
            $sql = "SELECT * FROM sanh";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['ten_sanh'] . "'>" . $row['ten_sanh'] . "</option>";
                }
            } else {
                echo "<option value=''>Không có sảnh nào</option>";
            }

            // Đóng kết nối
            $conn->close();
            ?>
        </select><br><br>

        <input type="submit" value="Đặt Sảnh">
    </form>
</body>
</html>
