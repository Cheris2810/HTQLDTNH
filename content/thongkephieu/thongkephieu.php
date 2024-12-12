
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css2/menu.css">
    <title>Quản lý đặt tiệc</title>
    <style>
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
        body {
            font-family: Comic Sans MS, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #333;
            font: Comic Sans MS ;
        }
    </style>
</head>
<header style="margin-left:-0.5rem">
    <nav class="navbar">
        <ul class="nav">
           <li class="menu-item"><a href="../halls/halls.php" >Sơ đồ sảnh</a></li>
           <li class="menu-item" style="border: 2px solid #007BFF">
             <a style="width: 7.1rem; " href="#">Thực đơn</a>
             <ul style="width: 9.5rem;margin-top: 3px;" class="sub-menu">
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 8rem;margin-left: -2rem;" href="../monman/monman.php">Món mặn</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 8rem;margin-left: -2rem;" href="../monchay/monchay.php">Món chay</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 8rem;margin-left: -2rem;" href="../douong/douong.php">Đồ uống</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 8rem;margin-left: -2rem;" href="../combomonman/combomonman.php">Bàn tiệc mẫu</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 9rem;margin-left: -2rem;" href="../bandattiec/formbandattiec.php">Bàn đặt tiệc</a></li>
             </ul>
           </li>
           <li class="menu-item" style="border: 2px solid #007BFF">
             <a style="width: 8.5rem;" href="#">Quản lý thông tin</a>
             <ul class="sub-menu" style="margin-top: 3px;">
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */" class="menu-item"><a style="margin-left: -1.5rem;" href="../customer/customer.php">Khách hàng</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */" class="menu-item"><a style="margin-left: -1.5rem;" href="#">Nhân viên</a></li>
             </ul>
           </li>
           <li class="menu-item  active" style="border: 2px solid #007BFF">
             <a style="width: 7.5rem;" href="#">Quản lý tài liệu</a>
             <ul style="width: 9.9rem;margin-top: 3px;" class="sub-menu">
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
    <div class="container" style="margin-top:3rem;">
         <div class="container" style="margin-top:3rem;">
        <div style="display: flex; align-items: center;">
            <!-- Nút Trở về -->
            <a href="javascript:history.back()" style="font-family: 'Comic Sans MS'; margin-right: 20px; text-decoration: none; background-color: #007BFF; color: white; padding: 10px 20px; border-radius: 5px; font-weight:bold;">Trở về</a>
            
            <!-- Tiêu đề -->
            <h1 style="font-family: Comic Sans MS; margin-left: 17rem;">Danh sách phiếu đặt tiệc</h1>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Mã phiếu</th>
                    <th>Sảnh</th>
                    <th>Buổi</th>
                    <th>Ngày đặt</th>
                    <th>Ngày tổ chức</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="phieudattiec-data">
         
                    <?php
// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tochuctiec";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Kiểm tra tham số trangthai trong URL
$trangthai = isset($_GET['trangthai']) ? $_GET['trangthai'] : '';

// Nếu trangthai là 'ChuaHoanChinh' thì lọc phiếu chưa hoàn chỉnh
if ($trangthai == 'ChuaHoanChinh') {
    $sql = "SELECT phieudattiec.sopdt, sanh.ten_sanh, buoi.TENBUOI, phieudattiec.ngaydat, phieudattiec.ngaytochuc, phieudattiec.trangthai 
            FROM phieudattiec
            JOIN sanh ON phieudattiec.stt_sanh = sanh.stt_sanh
            JOIN buoi ON phieudattiec.mabuoi = buoi.mabuoi
            WHERE phieudattiec.trangthai = 'Chưa hoàn chỉnh'";
} 
// Nếu trangthai là 'HoanChinh' thì lọc phiếu đã hoàn chỉnh
else if ($trangthai == 'HoanChinh') {
    $sql = "SELECT phieudattiec.sopdt, sanh.ten_sanh, buoi.TENBUOI, phieudattiec.ngaydat, phieudattiec.ngaytochuc, phieudattiec.trangthai 
            FROM phieudattiec
            JOIN sanh ON phieudattiec.stt_sanh = sanh.stt_sanh
            JOIN buoi ON phieudattiec.mabuoi = buoi.mabuoi
            WHERE phieudattiec.trangthai = 'Hoàn chỉnh'";
} 
// Nếu không có tham số trangthai thì lấy tất cả dữ liệu
else {
    $sql = "SELECT phieudattiec.sopdt, sanh.ten_sanh, buoi.TENBUOI, phieudattiec.ngaydat, phieudattiec.ngaytochuc, phieudattiec.trangthai 
            FROM phieudattiec
            JOIN sanh ON phieudattiec.stt_sanh = sanh.stt_sanh
            JOIN buoi ON phieudattiec.mabuoi = buoi.mabuoi";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Xuất dữ liệu vào bảng, chèn trực tiếp vào tbody
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['sopdt'] . "</td>";
        echo "<td>" . $row['ten_sanh'] . "</td>";
        echo "<td>" . $row['TENBUOI'] . "</td>";
        echo "<td>" . $row['ngaydat'] . "</td>";
        echo "<td>" . $row['ngaytochuc'] . "</td>";
        echo "<td>" . $row['trangthai'] . "</td>";
        
        // Tạo link cho cột "Thao tác"
        $trangthai = $row['trangthai'] == 'Chưa hoàn chỉnh' ? 'ChuaHoanChinh' : 'HoanChinh';
        echo "<td><a href='../thongkephieu/chi-tiet.php?sopdt=" . $row['sopdt'] . "&trangthai=" . $trangthai . "'>Xem Phiếu</a></td>";

        
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>Không có dữ liệu</td></tr>";
}

$conn->close();
?>


            
            </tbody>
        </table>
    </div>
</body>
</html>