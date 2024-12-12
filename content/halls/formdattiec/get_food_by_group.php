<?php
include('config.php');  // Kết nối cơ sở dữ liệu

if (isset($_GET['group_id'])) {
    $group_id = $_GET['group_id'];

    // Lấy các món ăn của nhóm đã chọn
    $query_food = "SELECT * FROM monan WHERE ma_nhommon = '$group_id'";
    $result_food = mysqli_query($conn, $query_food);

    // Hiển thị các món ăn dưới dạng button
    while ($row = mysqli_fetch_assoc($result_food)) {
        echo "<button class='mon-an-btn' data-id='" . $row['ma_ma'] . "' onclick='addSelectedItem(this)'>
                <img src='" . $row['hinh_anh'] . "' alt='" . $row['ten_ma'] . "' class='mon-an-img' />
              </button>";
    }
}
?>
