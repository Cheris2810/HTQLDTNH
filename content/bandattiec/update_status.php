
<?php
$SERVER = "localhost";
$user = "root";
$pass = "";
$database = "tochuctiec";

$conn = new mysqli($SERVER, $user, $pass, $database);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sopdt'])) {
    $sopdt = intval($_POST['sopdt']);

    // Kiểm tra SOPDT trong bảng dattiec liên kết với bảng phieudattiec
    $sql_check = "
        SELECT COUNT(*) 
        FROM dattiec 
        WHERE sopdt = ?
    ";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $sopdt);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        // Cập nhật trạng thái thành 'Hoàn chỉnh'
        $sql_update = "
            UPDATE phieudattiec 
            SET trangthai = 'Hoàn chỉnh' 
            WHERE sopdt = ?
        ";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $sopdt);
        if ($stmt_update->execute()) {
            echo "Cập nhật trạng thái thành công!";
        } else {
            echo "Lỗi: Không thể cập nhật trạng thái.";
        }
        $stmt_update->close();
    } else {
        echo "Lỗi: Không tìm thấy sopdt trong bảng dattiec.";
    }
} else {
    echo "Yêu cầu không hợp lệ.";
}

$conn->close();
?>