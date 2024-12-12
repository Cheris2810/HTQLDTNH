<?php
// Kết nối đến cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'tochuctiec');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

// Lấy giá trị sopdt từ yêu cầu GET
$sopdt = $_GET['sopdt'];

// Truy vấn để tính tổng số bàn
// Query to calculate total number of tables
$query = "SELECT (SUM(CAST(soban_dutru AS INT)) + SUM(CAST(soban_sudung AS INT))) AS total_ban FROM dattiec WHERE sopdt = '$sopdt'";
$result = $conn->query($query);

// Check if the query executed successfully and display total number of tables
if ($result) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo $row['total_ban'];  // Output the total number of tables
    } else {
        echo "0";  // If no rows, output 0
    }
} else {
    echo "Lỗi truy vấn: " . $conn->error;
}


// Đóng kết nối
$conn->close();
?>
