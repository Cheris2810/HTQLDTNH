<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kết nối cơ sở dữ liệu
    $conn = new mysqli("localhost", "root", "", "tochuctiec");
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Lấy các giá trị từ form
    $sopdt = $_POST['sopdt'];
    $soban_chinhthuc = $_POST['soban_chinhthuc'];
    $soban_dutru = $_POST['soban_dutru'];
    $soban_sudung = $_POST['soban_sudung'];
    $ten_btd = $_POST['ten_btd'];

    // Bước 1: Đối chiếu với bảng dattiec và bantiecdat
    $sql = "SELECT gia_btd FROM dattiec WHERE sopdt = '$sopdt'";
    $result = $conn->query($sql);
    $gia_btd = 0;
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $gia_btd = $row['gia_btd']; // Lấy giá trị gia_btd từ bảng dattiec
    }

    // Bước 2: Đối chiếu với bảng bantiecdat
    $sql_btd = "SELECT gia_btd FROM bantiecdat WHERE stt_btd = '$ten_btd'";
    $result_btd = $conn->query($sql_btd);
    if ($result_btd && $result_btd->num_rows > 0) {
        $row_btd = $result_btd->fetch_assoc();
        $gia_btd = $row_btd['gia_btd']; // Lấy giá trị gia_btd từ bảng bantiecdat
    }

    // Bước 3: Cập nhật tổng số tiền
    // Lấy tổng tiền từ các món ăn đã chọn
    $total_price = 0;
    $selected_items = $_POST['selected_items']; // Mảng chứa các món ăn đã chọn từ form
    foreach ($selected_items as $item) {
        // Lấy giá tiền của mỗi món ăn
        $sql_item = "SELECT gia_du FROM douong WHERE ma_du = '$item'";
        $result_item = $conn->query($sql_item);
        if ($result_item && $result_item->num_rows > 0) {
            $row_item = $result_item->fetch_assoc();
            $total_price += $row_item['gia_du']; // Cộng dồn giá tiền món ăn
        }
    }

    // Cập nhật gia_btd trong bảng dattiec
    $total_price += $gia_btd;
    $update_sql = "UPDATE dattiec SET gia_btd = '$total_price' WHERE sopdt = '$sopdt'";
    $conn->query($update_sql);

    // Bước 4: Thêm món ăn vào bảng datdouong
    foreach ($selected_items as $item) {
        // Kiểm tra xem món ăn đã tồn tại trong bảng datdouong chưa
        $check_sql = "SELECT * FROM datdouong WHERE sopdt = '$sopdt' AND madu = '$item'";
        $check_result = $conn->query($check_sql);
        
        if ($check_result && $check_result->num_rows == 0) {
            // Chưa có, thêm món ăn vào bảng datdouong
            $insert_sql = "INSERT INTO datdouong (sopdt, madu, sl_sudung, sl_dukien) 
                           VALUES ('$sopdt', '$item', 1, 0)";
            $conn->query($insert_sql);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css2/menu.css">
    <title>Quản lý đặt tiệc</title>
    <style>
        /* Định dạng danh sách các món đồ uống */
        .do-uong-list {
            margin-top: 1rem;
            display: flex;
            flex-wrap: wrap; /* Để các món tự động xuống dòng nếu không đủ chỗ */
            gap: 15px; /* Khoảng cách giữa các món */
            justify-content: center; /* Căn giữa các món */
            list-style: none;
            margin-left: rem;
        }

        /* Định dạng từng món đồ uống */
        .do-uong-item {
            display: flex; /* Sử dụng Flexbox */
            flex-direction: column; /* Hiển thị các phần tử theo cột: ảnh trên, tên dưới */
            justify-content: center; /* Căn giữa theo chiều dọc */
            align-items: center; /* Căn giữa theo chiều ngang */
            text-align: center; /* Căn giữa nội dung văn bản */
            width: 90px;
            height: 90px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        /* Định dạng hình ảnh trong mỗi món đồ uống */
        .do-uong-item img {
            margin-bottom: -5px; /* Khoảng cách giữa ảnh và tên món */
            width: 4rem; /* Đảm bảo ảnh không vượt quá chiều rộng của phần tử cha */
            height: 2.5rem;
            border: 1px solid;
            margin-left: rem;
            margin-top: 0.3rem;
            border-radius: 1rem; /* Tạo góc bo cho ảnh */
            display: block; /* Đảm bảo ảnh là khối độc lập */
        }

        /* Định dạng tên món đồ uống */
        .do-uong-item span {
            margin-top: 0.5rem;
            font-size: 10px;
            font-weight: bold;
            color: #333;
            display: block; /* Căn giữa văn bản trong Flexbox */
        }

        /* Hiệu ứng hover cho món đồ uống */
        .do-uong-item:hover {
            transform: translateY(-5px); /* Hiệu ứng hover */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Định dạng checkbox đồ uống */
        .do-uong-checkbox {
           ; /* Ẩn checkbox */
        }

        #show-do-uong button {
            margin-left: -5rem;
        }
        .pagination {
            margin-left: 6rem;
            margin-top: 10px;
            text-align: center;
            display: flex;
        }

        .pagination button {
            margin: 0 5px;
            padding: 5px 10px;
            cursor: pointer;
            border: 1px solid #ccc;
            border-radius: 3px;
            background-color: #f9f9f9;
        }

        .pagination button.active {
            background-color: #007bff;
            color: white;
        }

        body {
            font-family: Arial, sans-serif;
            margin-top: 3rem;
            background: white;
            margin-left: 0rem;        
        }
        .button-container {
            display: flex; /* Đặt container thành flexbox */
            justify-content: center; /* Căn giữa theo chiều ngang */
            align-items: center; /* Căn giữa theo chiều dọc */
            height: 100vh; /* Tùy chỉnh chiều cao container nếu muốn */
        }

        .submit-button {
            
        }

        .submit-button:hover {
            background-color: #45a049; /* Màu nền khi hover */
        }

               /* Định dạng chung cho container của nhóm món ăn */
        .group {
            display: none; /* Ẩn tất cả các nhóm món ăn */
            width: 25.3rem;
        }

        .group.active {
            display: block; /* Hiển thị nhóm món ăn được kích hoạt */
        }

        .group-buttons {
            margin-bottom: 20px;
            text-align: center;
            margin-left: -0.3rem;
            display: flex;
            font-size: x-small;
        }

        .group-button {
            padding: 10px 20px;
            margin: 5px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: x-small;
            font-weight: bolder;

        }

        .group-button:hover {
            background-color: #0056b3;
        }

        /* Định dạng tiêu đề của nhóm món ăn */
        .group h3 {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        /* Định dạng danh sách các món ăn */
        .group-items {
             margin-top: 1rem;
            display: flex; /* Căn ngang */
            flex-wrap: wrap; /* Để các món tự động xuống dòng nếu không đủ chỗ */
            gap: 15px; /* Khoảng cách giữa các món */
            justify-content: center; /* Căn giữa các món */
            list-style: none;
            margin-left: -2.3rem;
        }
        .group-items button {
            margin-top: 3rem;
        }

        /* Định dạng từng món ăn */
        

        .monan-item:hover {
            transform: translateY(-5px); /* Hiệu ứng hover */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Định dạng hình ảnh trong mỗi món ăn */
        .monan-item {
            display: flex; /* Sử dụng Flexbox */
            flex-direction: column; /* Hiển thị các phần tử theo cột: ảnh trên, tên dưới */
            justify-content: center; /* Căn giữa theo chiều dọc */
            align-items: center; /* Căn giữa theo chiều ngang */
            text-align: center; /* Căn giữa nội dung văn bản */
            width: 90px;
            height: 90px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .monan-item img {
            margin-bottom: 2px; /* Khoảng cách giữa ảnh và tên món */
            width: 4rem; /* Đảm bảo ảnh không vượt quá chiều rộng của phần tử cha */
            height: 2.5rem;
            border: 1px solid;
            margin-left: 0.9rem;
            margin-top: -0.5rem;
            border-radius: 1rem; /* Tạo góc bo cho ảnh */
            display: block; /* Đảm bảo ảnh là khối độc lập */
        }

        .monan-item span {
            margin-top: 0.5rem;
            font-size: 10px;
            font-weight: bold;
            color: #333;
            display: block; /* Căn giữa văn bản trong Flexbox */
        }

        #chayman-buttons button {
            margin-top: 0.5rem; /* Khoảng cách từ trên */
            background: #007BFF;
            margin-left: 1rem;
            color: white;
            width: 7rem;
            height: 2rem;
            font-size: 15px;
            font-weight: bold;
            border: 1px solid white;
            border-radius: 1rem;
        }

        #chayman-buttons {
            margin-left: 3rem;
        }
/* Định dạng thanh menu */
        .nav .sub-menu{
           display: none;
           position: absolute;
           top: 0px;
           left: 0rem;
           width: 0px;
           margin-top: -5px;
           color: black;
           background-image: linear-gradient(to bottom right ,#FF99CC,#33FFFF);
           border: none;
           transition: background-color 0s ease;
        }
        .navbar {
            background: #007BFF;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 100;
        }
        .navbar ul {
            list-style: none;
            display: flex;
            justify-content: start;
            padding: 0;
            margin: 0;
        }
        .navbar ul li {
            margin: 0 20px;
            cursor: pointer;
            padding: 10px 20px;
            border-radius: 3px;
            transition: background-color 0s ease;
        }
        .navbar ul li a {
            text-decoration: none;
            color: white;
            font-size: 1rem;
        }
        .navbar a{
            color: lightgray ;
            font-weight: bold;
        }

        .nav ul:hover {
            background-image: linear-gradient(to bottom right ,#FF99CC,#33FFFF);
        }

        /* Màu sắc khi hover */
        .navbar ul li a:hover {
        /* background-color: #696969;*/
            background: transparent; /* Trong suốt để lộ nền của .related */
            color: black;
        }
        .navbar ul li:hover {
            background-image: linear-gradient(to bottom right ,#FF99CC,#33FFFF);
        }
        /* Màu sắc khi đang active */
        .navbar ul li a.active {
            background-image: linear-gradient(to bottom right ,#FF99CC,#33FFFF);
            color: black;
        }
        .navbar ul li.active {
            background-image: linear-gradient(to bottom right ,#FF99CC,#33FFFF);
            color: black;
        }
        
        #booking-form {
            background-color: #ffffff;
            padding: 5rem;
            margin-left: 10rem;
            margin-top: 5rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 1000px; /* Tăng chiều rộng tối đa của form */
            display: flex;
            flex-direction: column; /* Để các cột xếp chồng lên nhau */
        }

        .form-columns {
            display: flex; /* Sử dụng flexbox cho cột */
            justify-content: space-between; /* Đảm bảo khoảng cách giữa hai cột */
            margin-bottom: 15px; /* Khoảng cách dưới cùng cho cột */
            margin-top: -3rem;
        }

        .form-column {
            flex: 1; /* Cả hai cột sẽ có chiều rộng bằng nhau */
            margin: 10px; /* Khoảng cách giữa hai cột */
        }

        .form-column div {
            margin-bottom: 15px;
            display: flex; /* Sử dụng flexbox cho div */
            align-items: center; /* Căn giữa label và input theo chiều dọc */
        }

        #booking-form label {
            display: block;
            font-weight: bold;
            margin-right: 10px; /* Khoảng cách bên phải của label */
            font-size: 14px; /* Giảm kích thước chữ của label */
            flex: 0 0 120px; /* Đặt chiều rộng cố định cho label */
        }

        #booking-form input, #booking-form select {
            width: 100%;
            padding: 6.5px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        #booking-form button {
            width: 100%;
            padding: 10px;
            font-size: 13px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        #booking-form button:hover {
            background-color: #45a049;
        }
        /* Phần bên phải - danh sách món ăn đã chọn */
        .right {
            margin-top: 30px;
            padding: 10px;
/*            border: 1px solid #ccc;*/
/*            max-width: 300px;*/
        }

        .selected-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .selected-item img {
            margin-right: 10px;
        }
        .right {
            width: 15rem;
/*            padding: 20px;*/
            margin-left: rem;
            justify-content: center;
/*            border: 1px solid*/
/*            justify-items: center;*/
        font-size: x-small;
        }

        .left {
            margin-left: 0rem;
            width: 0rem;
/*            border: 1px solid;*/
            justify-items: center;
             box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            height: 25rem;
            margin-top: 1rem;
        }
        .item-quantity{
            width: 3rem;
            margin-left: 1rem;
        }
        /* Kiểu dáng mặc định của các nút */
.chayman-button {
    background-color: #FFFFFF; /* Màu nền mặc định */
    color: #333333; /* Màu chữ mặc định */
    border: 1px solid #333333; /* Viền nút */
    padding: 10px 20px; /* Khoảng cách bên trong nút */
    cursor: pointer;
    transition: background-color 0.3s ease; /* Thêm hiệu ứng chuyển màu */
}

/* Màu nền khi nút được chọn */
.chayman-button.selected {
    background-color: #FF99CC; /* Màu nền khi chọn */
    color: white; /* Màu chữ khi chọn */
}

    </style>
</head>
<script>
    function handlePageChange(page) {
        window.location.href = `?page=${page}`;
    }
</script>
<header>
    <nav class="navbar">
        <ul class="nav">
           <li class="menu-item"><a href="../halls/halls.php">Sơ đồ sảnh</a></li>
           <li class="menu-item" style="border: 2px solid #007BFF">
             <a style="width: 7.1rem; " href="#">Thực đơn</a>
             <ul style="width: 9.5rem;margin-top: 0.1px;" class="sub-menu">
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 8rem;margin-left: -2rem;" href="../monman/monman.php">Món mặn</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 8rem;margin-left: -2rem;" href="../monchay/monchay.php">Món chay</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 8rem;margin-left: -2rem;" href="../douong/douong.php">Đồ uống</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 8rem;margin-left: -2rem;" href="../combomonman/combomonman.php">Bàn tiệc mẫu</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 9rem;margin-left: -2rem;" href="../bandattiec/formbandattiec.php">Bàn đặt tiệc</a></li>
             </ul>
           </li>
           <li class="menu-item" style="border: 2px solid #007BFF">
             <a style="width: 8.5rem;" href="#">Quản lý thông tin</a>
             <ul class="sub-menu" style="margin-top: 0.1px;">
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */" class="menu-item"><a style="margin-left: -1.5rem;" href="../customer/customer.php">Khách hàng</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */" class="menu-item"><a style="margin-left: -1.5rem;" href="#">Nhân viên</a></li>
             </ul>
           </li>
           <li class="menu-item" style="border: 2px solid #007BFF">
             <a style="width: 7.5rem;" href="#">Quản lý tài liệu</a>
             <ul style="width: 9.9rem;margin-top: 0.1px;" class="sub-menu">
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 9rem;margin-left: -2rem;" href="../phieudattiec/dsphieudattiec.php">Phiếu đặt tiệc</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 9rem;margin-left: -2rem;" href="../hopdong/hopdong.php">Hợp đồng</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 9rem;margin-left: -2rem;" href="../thongke/thongke.php">Thống kê/Báo cáo</a></li>
             </ul>
           </li>
           <li class="menu-item"><a href="#">Đăng xuất</a></li>
        </ul>
    </nav>
</header>

<body>
    <div>
        <form action="formdouong.php" method="POST">
        <label for="ten_btd">Tên bàn tiệc đặt:</label>
        <select name="ten_btd" id="ten_btd" required>
            <option value="Bàn tiệc đặt số 1">Bàn tiệc đặt số 1</option>
            <option value="Bàn tiệc đặt số 2">Bàn tiệc đặt số 2</option>
            <option value="Bàn tiệc đặt số 3">Bàn tiệc đặt số 3</option>
        </select>

        <label for="sopdt">Số phiếu đặt tiệc:</label>
<select name="sopdt" id="sopdt" required>
    <?php
    // Kết nối cơ sở dữ liệu
    $conn = new mysqli("localhost", "root", "", "tochuctiec");
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Lấy danh sách sopdt và hoten từ bảng phieudattiec và khachhang
    $sql = "SELECT p.sopdt, k.hoten 
            FROM phieudattiec p
            JOIN khachhang k ON p.makh = k.makh
            WHERE p.trangthai = 'Chưa hoàn chỉnh'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // Nếu có kết quả, hiển thị từng giá trị
        while ($row = $result->fetch_assoc()) {
            // Lấy giá trị sopdt và hoten
            $sopdt_value = intval($row['sopdt']);
            $hoten = htmlspecialchars($row['hoten'], ENT_QUOTES, 'UTF-8'); // Bảo vệ dữ liệu
            echo "<option value='" . $sopdt_value . "'>" . $sopdt_value . " - " . $hoten . "</option>";
        }
    } else {
        echo "<option value=''>Không có phiếu đặt tiệc nào</option>";
    }

    
    ?>
</select>



        <label for="soban_chinhthuc">Số bàn chính thức:</label>
        <select name="soban_chinhthuc" id="soban_chinhthuc" required>
            <option value="10">10 bàn</option>
            <option value="20">20 bàn</option>
           
        </select>

        <label for="soban_dutru">Số bàn dự trù:</label>
        <select name="soban_dutru" id="soban_dutru" required>
            <option value="1">1 bàn</option>
            <option value="2">2 bàn</option>
        </select>

        <label for="soban_sudung">Số bàn sử dụng:</label>
        <select name="soban_sudung" id="soban_sudung" required>
            <option value="15">0 bàn</option>
           
        </select>

        <label for="gia_btd"></label>
              <div style="justify-items: center; display: flex;">
                <div class="left" style="width:75rem">
                    <div id="menu" class="menu">
                       
<div id="do-uong-list" class="do-uong-list" style="display:flex;">

   <?php
    // Lấy danh mục đồ uống từ cơ sở dữ liệu và kết hợp với giá từ bảng giadouong
    $do_uong_query = "SELECT d.ten_du, d.ma_du, d.hinh_anh_du, g.giadouong 
                      FROM douong d
                      JOIN giadouong g ON d.ma_du = g.ma_du";

    $do_uong_result = $conn->query($do_uong_query);

    // Hiển thị các món đồ uống
    while ($do_uong_row = $do_uong_result->fetch_assoc()) {
        $do_uong_name = $do_uong_row['ten_du'];
        $do_uong_id = $do_uong_row['ma_du'];
        $do_uong_image = !empty($do_uong_row['hinh_anh_du']) ? $do_uong_row['hinh_anh_du'] : 'default.png';
        $do_uong_price = $do_uong_row['giadouong'];

        echo '<div class="do-uong-item" data-id="' . htmlspecialchars($do_uong_id) . '">';
        echo '<img src="' . htmlspecialchars($do_uong_image) . '" alt="' . htmlspecialchars($do_uong_name) . '" width="50">';
        echo '<span>' . htmlspecialchars($do_uong_name) . '</span>';
        echo '<span>  ' . number_format($do_uong_price, 0, ',', '.') . ' VND</span>';
        echo '<input type="checkbox" class="do-uong-checkbox" 
                      data-id="' . htmlspecialchars($do_uong_id) . '" 
                      data-name="' . htmlspecialchars($do_uong_name) . '" 
                      data-image="' . htmlspecialchars($do_uong_image) . '" 
                      data-price="' . htmlspecialchars($do_uong_price) . '">';
        echo '</div>';
    }
    ?>
</div>


</div>
</div>

                    <div class="right" style="">
                         <ul id="selected-items" class="selected-items" style="margin-left: -1rem;">
        <!-- Các món đồ uống được chọn sẽ hiển thị tại đây -->
                        </ul>
                        <div id="total-price" style="margin-top: 10px; font-weight: bold;">
                            Tổng giá: 0 VND
                        </div>
                    </div>

                </div>

                    <button type="submit" style="padding: 10px 20px; /* Tạo khoảng cách bên trong nút */font-size: 16px; /* Đặt kích thước chữ */background-color: #007BFF; /* Màu nền nút */color: white; /* Màu chữ */border: none; /* Bỏ đường viền */border-radius: 5px; /* Bo góc nút */cursor: pointer; /* Thêm con trỏ chuột khi di chuột qua */transition: background-color 0.3s; /* Hiệu ứng chuyển màu khi hover */ margin-left: 40rem; margin-top:2rem; margin-bottom:5rem">Đặt bàn tiệc</button>
            </div>
        </form>
    </div>

<script>

//-----------------------------------------
document.querySelectorAll('.do-uong-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        const item = this.closest('.do-uong-item');
        const selectedList = document.querySelector('.selected-items');

        if (this.checked) {
            const ma_du = item.dataset.id;

            // Gửi AJAX để lấy giadouong
            fetch('get_price_du.php', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ma_du })
            })
                .then(response => response.text()) // Lấy phản hồi dưới dạng text
                .then(text => {
                    console.log('Phản hồi từ PHP:', text); // Kiểm tra nội dung phản hồi
                    return JSON.parse(text); // Phân tích JSON
                })
                .then(data => {
                    if (data.success) {
                        // Tạo phần tử mới
                        const selectedItem = document.createElement('li');
                        selectedItem.className = 'selected-item';
                        selectedItem.dataset.id = ma_du;
                        selectedItem.dataset.price = data.giadouong; // Thêm giá vào data-price

                        // Hiển thị giá đồ uống và tính tổng tiền
                        selectedItem.innerHTML = `
                            <img src="${item.querySelector('img').src}" alt="${item.querySelector('span').innerText}" width="50">
                            <span>${item.querySelector('span').innerText}</span>
                            <span class="drink-price">${data.giadouong}</span> VND
                            <input type="number" class="item-quantity" value="1" min="1">
                        `;
                        selectedList.appendChild(selectedItem);

                        // Cập nhật tổng tiền
                        calculateTotalPrice();
                    } else {
                        alert(data.message || 'Không tìm thấy giá đồ uống!');
                    }
                })
                .catch(error => console.error('Lỗi:', error));
        } else {
            // Xóa món khỏi danh sách
            const itemToRemove = selectedList.querySelector(`.selected-item[data-id="${item.dataset.id}"]`);
            if (itemToRemove) {
                itemToRemove.remove();
                calculateTotalPrice();
            }
        }
    });
});

// Hàm tính tổng giá
function calculateTotalPrice() {
    let totalPrice = 0;
    document.querySelectorAll('.selected-item').forEach(item => {
        const price = parseFloat(item.dataset.price); // Lấy giá từ data-price
        const quantity = parseInt(item.querySelector('.item-quantity').value, 10);
        totalPrice += price * quantity;
    });

    // Hiển thị tổng giá
    document.getElementById('total-price').textContent = `Tổng giá: ${totalPrice.toLocaleString()} VND`;
}

// document.querySelector('button[type="submit"]').addEventListener('click', function(event) {
//     event.preventDefault(); // Ngừng gửi form mặc định

//     let selectedItems = [];
//     document.querySelectorAll('.selected-item').forEach(item => {
//         const id = item.dataset.id;
//         const name = item.querySelector('span').textContent.trim();
//         const price = parseFloat(item.dataset.price);
//         const quantity = parseInt(item.querySelector('.item-quantity').value, 10);

//         selectedItems.push({
//             ma_du: id,
//             quantity: quantity
//         });
//     });

//     const sopdt = document.getElementById('sopdt').value;  // Lấy giá trị từ input field
//     const formData = new FormData();
//     formData.append('sopdt', sopdt);  // Thay thế 'some_sopdt_value' bằng giá trị thực tế của sopdt
//     formData.append('selected_items', JSON.stringify(selectedItems));  // Dữ liệu items được chọn

//     // Gửi dữ liệu tới insertdouong.php
//     fetch('insertdouong.php', { // Gọi file insertdouong.php
//         method: 'POST',
//         body: formData
//     })
//     .then(response => response.text())
//     .then(data => {
//         console.log(data); // Hiển thị kết quả trả về từ server
//         alert('Đặt đồ uống thành công!');
//     })
//     .catch(error => console.error('Error:', error));
// });
document.querySelector("form").addEventListener("submit", function(e) {
    // Thu thập các món ăn đã chọn
    var selectedItems = [];
    var checkboxes = document.querySelectorAll(".do-uong-checkbox:checked");
    checkboxes.forEach(function(checkbox) {
        selectedItems.push(checkbox.getAttribute("data-id"));
    });

    // Gửi danh sách món ăn đã chọn qua form
    var hiddenInput = document.createElement("input");
    hiddenInput.type = "hidden";
    hiddenInput.name = "selected_items[]";
    hiddenInput.value = selectedItems.join(',');
    this.appendChild(hiddenInput); // Thêm vào form để gửi đi
});
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault(); // Ngừng gửi form mặc định

    // Lấy dữ liệu từ form
    const sopdt = document.querySelector('#sopdt').value;
    const ten_btd = document.querySelector('#ten_btd').value;
    const gia_btd = document.querySelector('#total-price').innerText.replace("Tổng tiền: ", "").replace(" VND", "");

    // Lấy các món ăn đã chọn từ danh sách
    const selected_items = [];
    const items = document.querySelectorAll(".selected-item");
    for (let i = 0; i < items.length; i++){
            const item_id =items[i].dataset.id;
            const item_name =items[i].dataset.name ;
            const item_price =items[i].dataset.price ; // Giả sử bạn có giá của món      
            const item_quantity = items[i].querySelector(".item-quantity").value;


            selected_items.push({
            id: item_id,
            name: item_name,
            price: item_price,
            quantity: item_quantity
           });
    }
   /* document.querySelectorAll('.do-uong-checkbox:checked').forEach(function(checkbox) {
        const item_id = checkbox.getAttribute('data-id');
        const item_name = checkbox.getAttribute('data-name');
        const item_price = parseInt(checkbox.getAttribute('data-price')); // Giả sử bạn có giá của món      
        const item_quantity = document.querySelector(`.item-quantity`).value;
       //const item_id=selectedList.getElementById()
       

        selected_items.push({
            id: item_id,
            name: item_name,
            price: item_price,
            quantity: item_quantity
        });
        
    });
*/
    // Gửi dữ liệu tới PHP
    const formData = new FormData();
    formData.append('sopdt', sopdt);
    formData.append('ten_btd', ten_btd);
    formData.append('gia_btd', gia_btd);
    formData.append('selected_items', JSON.stringify(selected_items));
//echo "vidu 1";
    //fetch('formdouong.php', {
    fetch('insertdouong.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        //alert(data); // Hiển thị thông báo từ PHP
        window.location="../phieudattiec/dsphieudattiec.php";

    });
});


</script>


</body>
</html>
