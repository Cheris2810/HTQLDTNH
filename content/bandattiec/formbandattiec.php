
<?php
// Kết nối với cơ sở dữ liệu
$SERVER = "localhost";
$user = "root"; // Tên đăng nhập MySQL
$pass = ""; // Mật khẩu MySQL
$database = "tochuctiec"; // Tên cơ sở dữ liệu

$conn = new mysqli($SERVER, $user, $pass, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra nếu dữ liệu từ form đã được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $ten_btd = $_POST['ten_btd'];
    $sopdt = $_POST['sopdt'];
    $soban_chinhthuc = $_POST['soban_chinhthuc'];
    $soban_dutru = $_POST['soban_dutru'];
    $soban_sudung = $_POST['soban_sudung'];
    $monan = $_POST['monan']; // Mảng món ăn đã chọn
    $selected_items = $_POST['selected_items'];

    // Kiểm tra xem sopdt có tồn tại trong bảng phieudattiec và có trạng thái là "Chưa hoàn chỉnh" không
    $sql_check_sopdt = "SELECT COUNT(*) FROM phieudattiec WHERE sopdt = ? AND trangthai = 'Chưa hoàn chỉnh'";
    $stmt = $conn->prepare($sql_check_sopdt);
    $stmt->bind_param("i", $sopdt);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count == 0) {
        die("Lỗi: sopdt không tồn tại trong bảng phieudattiec hoặc không có trạng thái 'Chưa hoàn chỉnh'!");
    }

    // Tính tổng giá tiền
    $total_price = 0;
    foreach ($selected_items as $item) {
        $ma_ma = intval($item['ma_ma']);
        $quantity = intval($item['quantity']);
        $sql_price = "
            SELECT giabanle 
            FROM giabanle 
            WHERE ma_ma = ? AND trangthai = 'Có hiệu lực'
            ORDER BY ngay DESC 
            LIMIT 1";
        $stmt_price = $conn->prepare($sql_price);
        $stmt_price->bind_param("i", $ma_ma);
        $stmt_price->execute();
        $stmt_price->bind_result($giabanle);
        $stmt_price->fetch();
        $stmt_price->close();
        $giabanle = $giabanle ?? 0;
        $total_price += $giabanle * $quantity;
    }

    // Insert vào bảng bantiecdat
    $sql_bantiecdat = "INSERT INTO bantiecdat (ten_btd, gia_btd) VALUES (?, ?)";
    $stmt_btd = $conn->prepare($sql_bantiecdat);
    $stmt_btd->bind_param("sd", $ten_btd, $total_price);
    $stmt_btd->execute();
    $stt_btd = $stmt_btd->insert_id;
    $stmt_btd->close();

    // Insert vào bảng dattiec
    $sql_dattiec = "INSERT INTO dattiec (sopdt, stt_btd, soban_chinhthuc, soban_dutru, soban_sudung) VALUES (?, ?, ?, ?, ?)";
    $stmt_dattiec = $conn->prepare($sql_dattiec);
    $stmt_dattiec->bind_param("iiiii", $sopdt, $stt_btd, $soban_chinhthuc, $soban_dutru, $soban_sudung);
    $stmt_dattiec->execute();
    $stmt_dattiec->close();

    // Insert các món ăn vào bảng bantiec_mon và datmon
    foreach ($selected_items as $item) {
    $ma_ma = intval($item['ma_ma']);
    $quantity = intval($item['quantity']);
    if ($ma_ma > 0 && $quantity > 0) {
        // Kiểm tra và chèn vào bảng bantiec_mon
        $sql_check_bantiec_mon = "SELECT COUNT(*) FROM bantiec_mon WHERE stt_btd = ? AND ma_ma = ?";
        $stmt_check_bantiec_mon = $conn->prepare($sql_check_bantiec_mon);
        $stmt_check_bantiec_mon->bind_param("ii", $stt_btd, $ma_ma);
        $stmt_check_bantiec_mon->execute();
        $stmt_check_bantiec_mon->bind_result($count_bantiec_mon);
        $stmt_check_bantiec_mon->fetch();
        $stmt_check_bantiec_mon->close();

        if ($count_bantiec_mon == 0) {
            $sql_bantiec_mon = "INSERT INTO bantiec_mon (stt_btd, ma_ma) VALUES (?, ?)";
            $stmt_bantiec_mon = $conn->prepare($sql_bantiec_mon);
            $stmt_bantiec_mon->bind_param("ii", $stt_btd, $ma_ma);
            $stmt_bantiec_mon->execute();
            $stmt_bantiec_mon->close();
        }

        // Kiểm tra và chèn vào bảng datmon
        $sql_check_datmon = "SELECT COUNT(*) FROM datmon WHERE MA_MA = ? AND SOPDT = ?";
        $stmt_check_datmon = $conn->prepare($sql_check_datmon);
        $stmt_check_datmon->bind_param("ii", $ma_ma, $sopdt);
        $stmt_check_datmon->execute();
        $stmt_check_datmon->bind_result($count_datmon);
        $stmt_check_datmon->fetch();
        $stmt_check_datmon->close();

        if ($count_datmon == 0) {
            $sql_datmon = "INSERT INTO datmon (MA_MA, SOPDT, SL_SUDUNG) VALUES (?, ?, ?)";
            $stmt_datmon = $conn->prepare($sql_datmon);
            $stmt_datmon->bind_param("iii", $ma_ma, $sopdt, $quantity);
            $stmt_datmon->execute();
            $stmt_datmon->close();
        }
    }
}

echo "
    <script>
    
        window.location.href = '../bandattiec/formdouong.php'; // Chuyển hướng
    </script>";
    

}
$conn->close();
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
            height: 70px;
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
            height: 80px;
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
            height: 35rem;
            margin-top: 1rem;
        }
        .item-quantity{
            width: 3rem;
            margin-left: 1rem;
        }
    </style>
</head>
<script>
    function handlePageChange(page) {
        window.location.href = `?page=${page}`;
    }
</script>
<header style="margin-left:-0.5rem">
    <nav class="navbar">
        <ul class="nav">
           <li class="menu-item "><a href="../halls/halls.php" >Sơ đồ sảnh</a></li>
           <li class="menu-item active" style="border: 2px solid #007BFF">
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
        <form action="formbandattiec.php" method="POST">
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
            $sopdt_value = intval($row['sopdt']); // Lấy giá trị sopdt
            $hoten = htmlspecialchars($row['hoten'], ENT_QUOTES, 'UTF-8'); // Bảo vệ dữ liệu hoten
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
            <option value="0">0 bàn</option>
        </select>

        <label for="gia_btd"></label>
              <div style="justify-items: center; display: flex;">
                <div class="left" style="width:75rem">
                    <div id="menu" class="menu">
                        <?php
                        // Cài đặt số món ăn mỗi trang
                        $items_per_page = 12;

                        // Lấy số trang hiện tại từ query string, nếu không có thì mặc định là trang 1
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

                        // Tính toán OFFSET
                        $offset = ($page - 1) * $items_per_page;

                        // Lấy danh mục nhóm món ăn
                        $group_query = "SELECT DISTINCT n.ten_nm, n.ma_nm, m.MaChayMan FROM nhommon n 
                                        JOIN monan m ON n.ma_nm = m.ma_nhommon
                                        JOIN chay_man c ON m.MaChayMan = c.machayman"; 
                        $group_result = $conn->query($group_query);

                        // Lấy danh sách tenchayman
                        $chayman_query = "SELECT * FROM chay_man";
                        $chayman_result = $conn->query($chayman_query);

                        // Hiển thị các nút tenchayman
                        echo '<div id="chayman-buttons" class="chayman-buttons">';
                        while ($chayman_row = $chayman_result->fetch_assoc()) {
                            $chayman_name = $chayman_row['tenchayman'];
                            $chayman_id = $chayman_row['machayman'];
                            echo '<button class="chayman-button" data-chayman="' . htmlspecialchars($chayman_id) . '">' . htmlspecialchars($chayman_name) . '</button>';
                        }
                        echo '</div>';

                        // Hiển thị nhóm món theo MaChayMan
                        echo '<div id="group-buttons" class="group-buttons">';
                        $group_result->data_seek(0);
                        while ($group_row = $group_result->fetch_assoc()) {
                            $group_name = $group_row['ten_nm'];
                            $group_id = $group_row['ma_nm'];
                            $group_chayman = $group_row['MaChayMan']; // Lấy MaChayMan từ bảng chay_man

                            // Chỉ hiển thị nhóm món thuộc MaChayMan được chọn
                            echo '<div class="group" id="group-' . htmlspecialchars($group_id) . '" data-chayman="' . htmlspecialchars($group_chayman) . '">';
                            echo '<h3>' . htmlspecialchars($group_name) . '</h3>';
                            echo '<ul class="group-items">';

                            // Lấy các món ăn trong nhóm và phân trang
                            $monan_query = "SELECT ma_ma, ten_ma, hinh_anh FROM monan WHERE ma_nhommon = '$group_id' AND MaChayMan = '$group_chayman' LIMIT $items_per_page OFFSET $offset";
                            $monan_result = $conn->query($monan_query);

                            if ($monan_result->num_rows > 0) {
                                $index = 0;
                                while ($row = $monan_result->fetch_assoc()) {
                                    $image_path = !empty($row['hinh_anh']) ? $row['hinh_anh'] : 'default.png';
                                    echo '<li class="monan-item" data-index="' . $index . '" data-id="' . $row['ma_ma'] . '">';
                                    echo '<img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($row['ten_ma']) . '" width="50">';
                                    echo '<span>' . htmlspecialchars($row['ten_ma']) . '</span>';
                                    echo '<input type="checkbox" name="monan[]" value="' . $row['ma_ma'] . '" class="select-item">';
                                    echo '</li>';
                                    $index++;
                                }
                            } else {
                                echo '<li>Không có món ăn trong nhóm này</li>';
                            }
                            echo '</ul>';

                            // Lấy tổng số món ăn trong nhóm
                            $pagination_query = "SELECT COUNT(*) AS total FROM monan WHERE ma_nhommon = '$group_id' AND MaChayMan = '$group_chayman'";
                            $pagination_result = $conn->query($pagination_query);
                            $total_items = $pagination_result->fetch_assoc()['total'];
                            $total_pages = ceil($total_items / $items_per_page);

                            // Chỉ hiển thị phân trang nếu số món ăn > 12
                            if ($total_items > $items_per_page) {
                                if ($total_pages > 1) {
                                    echo '<div class="pagination">';
                                    for ($i = 1; $i <= $total_pages; $i++) {
                                        // Cập nhật URL với tham số trang
                                        echo '<a href="?page=' . $i . '" class="page-link">' . $i . '</a>';
                                    }
                                    echo '</div>';
                                }
                            }

                            echo '</div>';
                        }
                        echo '</div>';
                        ?>
                 <!-- Nút "Đồ uống" bên trái -->
                
                        </div>

                   
                </div>



                    <div class="right" style="">
                        <ul id="selected-items" class="selected-items" style="margin-left: -1rem;">
                            <!-- Các món ăn đã được chọn sẽ thêm vào đây -->
                        </ul>
                    </div>

                </div>

                    <button type="submit" style="padding: 10px 20px; /* Tạo khoảng cách bên trong nút */font-size: 16px; /* Đặt kích thước chữ */background-color: #007BFF; /* Màu nền nút */color: white; /* Màu chữ */border: none; /* Bỏ đường viền */border-radius: 5px; /* Bo góc nút */cursor: pointer; /* Thêm con trỏ chuột khi di chuột qua */transition: background-color 0.3s; /* Hiệu ứng chuyển màu khi hover */ margin-left: 40rem; margin-top:2rem; margin-bottom:5rem">Chọn đồ uống</button>
            </div>
        </form>
    </div>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
    const groupButtons = document.querySelectorAll(".group-button");
    const groups = document.querySelectorAll(".group");
    const chaymanButtons = document.querySelectorAll(".chayman-button");
    const monAnItems = document.querySelectorAll(".monan-item");
    const selectedItemsList = document.getElementById("selected-items");  // Kiểm tra ID đúng

    // Mặc định ẩn tất cả nhóm món ăn và món ăn trong nhóm
    groups.forEach(group => {
        group.style.display = "none"; // Ẩn nhóm món ăn
        const items = group.querySelectorAll(".monan-item");
        items.forEach(item => item.style.display = "none"); // Ẩn món ăn trong nhóm
    });

    // Gán sự kiện click cho các nút chọn nhóm món
    groupButtons.forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault(); // Ngăn chặn hành động mặc định
            const groupId = button.dataset.group;

            // Ẩn tất cả nhóm món ăn và món ăn của các nhóm
            groups.forEach(group => {
                group.style.display = "none"; // Ẩn tất cả nhóm món
                const items = group.querySelectorAll(".monan-item");
                items.forEach(item => item.style.display = "none"); // Ẩn tất cả món ăn
            });

            // Hiển thị nhóm món ăn được chọn và các món trong nhóm đó
            const targetGroup = document.getElementById("group-" + groupId);
            if (targetGroup) {
                targetGroup.style.display = "block"; // Hiển thị nhóm
                const items = targetGroup.querySelectorAll(".monan-item");
                items.forEach(item => item.style.display = "block"); // Hiển thị món ăn trong nhóm
            }
        });
    });

    // Gán sự kiện click cho các nút chọn món chay/mặn
    chaymanButtons?.forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            const chaymanId = button.dataset.chayman;

            // Ẩn tất cả nhóm món ăn và món ăn trong nhóm
            groups.forEach(group => {
                group.style.display = "none";
                const items = group.querySelectorAll(".monan-item");
                items.forEach(item => item.style.display = "none");
            });

            // Hiển thị nhóm món chay/mặn đã chọn
            groups.forEach(group => {
                if (group.dataset.chayman === chaymanId) {
                    group.style.display = "block"; // Hiển thị nhóm
                    const items = group.querySelectorAll(".monan-item");
                    items.forEach(item => item.style.display = "block"); // Hiển thị món ăn trong nhóm
                }
            });
        });
    });

    // Gán sự kiện click cho các món ăn
    monAnItems.forEach(item => {
        item.addEventListener("click", function () {
            const maMon = item.querySelector("img")?.alt || item.dataset.id; // Lấy mã món
            const tenMon = item.querySelector("span").textContent;
            const imagePath = item.querySelector("img")?.src; // Lấy đường dẫn hình ảnh
            const checkbox = item.querySelector("input[type='checkbox']");

            // Nếu checkbox được chọn, thêm món vào danh sách bên phải
            if (checkbox?.checked) {
                const selectedItem = document.createElement("li");
                selectedItem.classList.add("selected-item");

                // Tạo phần tử hình ảnh và tên món
                const imgElement = document.createElement("img");
                imgElement.src = imagePath;
                imgElement.alt = tenMon;
                imgElement.width = 45; // Bạn có thể thay đổi kích thước nếu cần
                imgElement.height = 35;

                const textElement = document.createElement("span");
                textElement.textContent = `${tenMon} (${maMon})`;

                // Thêm hình ảnh và tên món vào phần tử list
                selectedItem.appendChild(imgElement);
                selectedItem.appendChild(textElement);

                // Thêm món vào danh sách đã chọn
                selectedItemsList.appendChild(selectedItem);
            } else {
                // Nếu checkbox không được chọn, xóa món khỏi danh sách đã chọn
                const itemsToRemove = selectedItemsList.querySelectorAll(`li[data-id="${maMon}"]`);
                itemsToRemove.forEach(item => item.remove());
            }
        });
    });

    // Hiển thị/ẩn danh sách đồ uống khi nhấn nút
    document.getElementById("show-do-uong").addEventListener("click", function() {
        var doUongList = document.getElementById("do-uong-list");
        if (doUongList.style.display === "none" || doUongList.style.display === "") {
            doUongList.style.display = "block";  // Hiển thị danh sách đồ uống
        } else {
            doUongList.style.display = "none";   // Ẩn danh sách đồ uống
        }
    });

    // Gán sự kiện click cho các món đồ uống
    document.querySelectorAll('.do-uong-item').forEach(item => {
        item.addEventListener('click', function(event) {
            event.preventDefault();  // Ngừng hành động mặc định (chuyển trang)
            
            // Lấy thông tin đồ uống
            var doUongId = this.dataset.id;
            var doUongName = this.querySelector("span").textContent;
            var imagePath = this.querySelector("img").src;  // Đảm bảo lấy đúng đường dẫn hình ảnh
            var checkbox = this.querySelector("input[type='checkbox']");

            // Tìm danh sách đã chọn (giả sử ID là 'selected-items-list')
            const selectedItemsList = document.getElementById("selected-items");
            
            // Nếu checkbox được chọn, thêm đồ uống vào danh sách bên phải
            if (checkbox && checkbox.checked) {
                const selectedItem = document.createElement("li");
                selectedItem.classList.add("selected-item");
                selectedItem.dataset.id = doUongId; // Gán data-id cho phần tử đã chọn

                // Tạo phần tử hình ảnh và tên món
                const imgElement = document.createElement("img");
                imgElement.src = imagePath;
                imgElement.alt = doUongName;
                imgElement.width = 45; // Thay đổi kích thước nếu cần
                imgElement.height = 35;

                const textElement = document.createElement("span");
                textElement.textContent = `${doUongName} (${doUongId})`;

                // Thêm hình ảnh và tên vào phần tử list
                selectedItem.appendChild(imgElement);
                selectedItem.appendChild(textElement);

                // Thêm đồ uống vào danh sách đã chọn
                selectedItemsList.appendChild(selectedItem);
            } else {
                // Nếu checkbox không được chọn, xóa món khỏi danh sách đã chọn
                const itemsToRemove = selectedItemsList.querySelectorAll(`li[data-id="${doUongId}"]`);
                itemsToRemove.forEach(item => item.remove());
            }
        });
    });
});
    function filterDoUong() {
        const madvt = document.getElementById('donvitinh').value;
        const urlParams = new URLSearchParams(window.location.search);
        
        if (madvt) {
            urlParams.set('madvt', madvt);
        } else {
            urlParams.delete('madvt');
        }
        
        // Reload the page with the updated URL
        window.location.search = urlParams.toString();
    }

    document.addEventListener("DOMContentLoaded", function () {
        const selectedItems = document.getElementById("selected-items");

        // Hàm thêm món ăn vào danh sách
        function addMenuItem(ma_ma, ten_monan) {
            // Kiểm tra xem món ăn đã có trong danh sách chưa
            if (document.querySelector(`li[data-ma-ma="${ma_ma}"]`)) {
                alert("Món ăn đã được thêm!");
                return;
            }

            // Tạo phần tử món ăn
            const listItem = document.createElement("li");
            listItem.classList.add("item");
            listItem.setAttribute("data-ma-ma", ma_ma);

            listItem.innerHTML = `
                <span class="item-name">${ten_monan}</span>
                <span class="item-price">Giá: <span id="price-${ma_ma}">0</span> VNĐ</span>
                <input type="number" id="quantity-${ma_ma}" class="item-quantity" min="1" value="1" placeholder="Số lượng">
                <button class="save-quantity" data-ma-ma="${ma_ma}">Lưu</button>
            `;

            selectedItems.appendChild(listItem);

            // Lấy giá từ API
            fetchPrice(ma_ma);

            // Thêm sự kiện lưu số lượng
            listItem.querySelector(".save-quantity").addEventListener("click", function () {
                saveQuantity(ma_ma);
            });
        }

        // Hàm lấy giá từ API
        function fetchPrice(ma_ma) {
            fetch(`http://localhost/get_price.php?ma_ma=${ma_ma}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`price-${ma_ma}`).textContent = data.price;
                    } else {
                        console.error("Không lấy được giá món ăn!");
                    }
                })
                .catch(error => console.error("Lỗi kết nối:", error));
        }

        // Hàm lưu số lượng vào bảng datmon
        function saveQuantity(ma_ma) {
            const sopdt = "Mã phiếu đặt tiệc"; // Thay bằng SOPDT thực tế
            const quantity = document.getElementById(`quantity-${ma_ma}`).value;

            if (quantity > 0) {
                fetch("http://localhost/save_quantity.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ ma_ma, sopdt, sl_sudung: quantity }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Lưu số lượng thành công!");
                        } else {
                            alert("Không thể lưu số lượng!");
                        }
                    })
                    .catch(error => console.error("Lỗi kết nối:", error));
            } else {
                alert("Số lượng phải lớn hơn 0!");
            }
        }

    });

    document.addEventListener("DOMContentLoaded", function () {
        // Xử lý khi chọn món ăn
        document.querySelectorAll(".select-item").forEach(item => {
            item.addEventListener("change", function () {
                if (this.checked) {
                    const id = this.value; // Lấy ma_ma
                    const type = "monan"; // Xác định loại
                    addSelectedItem(id, type);
                }
            });
        });

        // Xử lý khi chọn đồ uống
        document.querySelectorAll(".do-uong-checkbox").forEach(item => {
            item.addEventListener("change", function () {
                if (this.checked) {
                    const id = this.getAttribute("data-id"); // Lấy ma_du
                    const type = "douong"; // Xác định loại
                    addSelectedItem(id, type);
                }
            });
        });

        // Hàm thêm món ăn/đồ uống được chọn vào danh sách
    function addSelectedItem(id, type) {
        // Gửi AJAX lấy giá bán
        fetch(`get_price.php?id=${id}&type=${type}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Thêm vào danh sách bên phải
                    const selectedList = document.getElementById("selected-items");
                    const itemHtml = `
                        <li data-id="${id}" class="item">
                            <span class="item-price">Giá: <span class="price">${data.price}</span> VNĐ</span>
                            <input type="hidden" name="selected_items[${id}][ma_ma]" value="${id}">
                            <input type="number" name="selected_items[${id}][quantity]" class="item-quantity" min="1" value="1" placeholder="Số lượng">
                        </li>`;
                    selectedList.insertAdjacentHTML("beforeend", itemHtml);

                    // **Gắn sự kiện input để tính lại tổng tiền khi số lượng thay đổi**
                    const newItem = selectedList.querySelector(`li[data-id="${id}"]`);
                    newItem.querySelector(".item-quantity").addEventListener("input", calculateTotal);

                    // Tính lại tổng tiền khi thêm món mới
                    calculateTotal();
                } else {
                    alert("Không tìm thấy giá cho mục này!");
                }
            });
    }


        function calculateTotal() {
            let total = 0;

            // Duyệt qua tất cả các món trong danh sách
            document.querySelectorAll("#selected-items .item").forEach(item => {
                const price = parseFloat(item.querySelector(".price").textContent); // Giá tiền
                const quantity = parseInt(item.querySelector(".item-quantity").value); // Số lượng
                total += price * quantity;
            });

            // Hiển thị tổng tiền
            const totalDisplay = document.getElementById("total-display");
            if (!totalDisplay) {
                // Nếu chưa có, thêm thẻ hiển thị tổng tiền
                const selectedList = document.getElementById("selected-items");
                selectedList.insertAdjacentHTML(
                    "afterend",
                    `<div id="total-display" style="margin-top: 10px; font-weight: bold;">Tổng tiền: ${total.toLocaleString()} VNĐ</div>`
                );
            } else {
                // Nếu đã có, cập nhật nội dung
                totalDisplay.textContent = `Tổng tiền: ${total.toLocaleString()} VNĐ`;
            }
        }


        // Hàm lưu số lượng vào cơ sở dữ liệu
        function saveQuantity(id, quantity, type) {
            fetch("save_quantity.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ id, quantity, type })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Số lượng đã được lưu thành công!");
                    } else {
                        alert("Không thể lưu số lượng.");
                    }
                });
        }
    });
document.querySelectorAll('.chayman-button').forEach(function(button) {
    button.addEventListener('click', function() {
        // Đặt lại màu cho tất cả các nút trước
        document.querySelectorAll('.chayman-button').forEach(function(btn) {
            btn.style.backgroundColor = '';  // Đặt lại màu nền của các nút còn lại
            btn.style.color = '';  // Đặt lại màu chữ của các nút còn lại
        });

        // Thay đổi màu nền của nút hiện tại
        this.style.backgroundColor = '#FF99CC';  // Màu nền khi được chọn
        this.style.color = '#fff';  // Màu chữ khi được chọn

        // Gọi hàm filterDataByChayman với giá trị data-chayman
        filterDataByChayman(this.getAttribute('data-chayman'));
    });
});


</script>


</body>
</html>
