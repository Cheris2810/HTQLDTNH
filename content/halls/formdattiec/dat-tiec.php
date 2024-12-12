<?php
$SERVER = "localhost";
$user = "root";
$pass = "";
$database = "tochuctiec";

// Kết nối đến cơ sở dữ liệu
$conn = new mysqli($SERVER, $user, $pass, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Debug: Kiểm tra dữ liệu trong $_POST
echo "Dữ liệu POST: ";
var_dump($_POST);

// Debug: Kiểm tra dữ liệu trong $_GET
echo "Dữ liệu GET: ";
echo "GET: " . var_dump($_GET);

// Lấy mã sảnh từ URL
$stt_sanh = isset($_GET['sanh']) ? intval($_GET['sanh']) : 0;

// Kiểm tra mã sảnh hợp lệ
if ($stt_sanh <= 0) {
    echo "Vui lòng truyền mã sảnh hợp lệ qua URL. Ví dụ: ?sanh=3";
    exit;
}
echo "Mã sảnh là: " . $stt_sanh;

// Truy vấn số hợp đồng mới nhất
$query = "SELECT MAX(sopdt) AS sopdt FROM phieudattiec";
$result = $conn->query($query);
$row = $result->fetch_assoc();

// Tăng số hợp đồng lên 1 (bắt đầu từ 2 nếu chưa có)
$new_sopdt = isset($row['sopdt']) && $row['sopdt'] >= 2 ? $row['sopdt'] + 1 : 2;

// Truy vấn số hợp đồng mới nhất
$query = "SELECT MAX(sohd) AS sohd FROM phieudattiec";
$result = $conn->query($query);
$row = $result->fetch_assoc();

// Tăng số hợp đồng lên 1 (bắt đầu từ 2 nếu chưa có)
$new_sohd = isset($row['sohd']) && $row['sohd'] >= 2 ? $row['sohd'] + 1 : 2;

// Lấy mã khách hàng (makh) lớn nhất
$query = "SELECT MAX(makh) AS max_makh FROM khachhang";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$new_makh = isset($row['max_makh']) && $row['max_makh'] >= 27 ? $row['max_makh'] + 1 : 27;

// Kiểm tra nếu dữ liệu từ form đã được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $makh = $_POST['makh'];
    $hoten = $_POST['hoten'];
    $sodt = $_POST['sodt'];
    $email = $_POST['email'];
    $socccd = $_POST['socccd'];
    $ngaycap = $_POST['ngaycap'];
    $noicap = $_POST['noicap'];
    $so_nha = $_POST['so_nha'];
    $tinh_thanhpho = $_POST['tinh_thanhpho'];
    $quan_huyen = $_POST['quan_huyen'];
    $phuong_xa = $_POST['phuong_xa'];

    // Kiểm tra nếu makh đã tồn tại trong bảng khachhang
    $queryMAKH = "SELECT makh FROM khachhang WHERE makh = '$makh'";
    $resultMAKH = mysqli_query($conn, $queryMAKH);
    
    if (mysqli_num_rows($resultMAKH) == 0) {
        // Nếu khách hàng chưa tồn tại, thực hiện insert
        $insertKHQuery = "INSERT INTO khachhang (makh, hoten, sodt, email, socccd, ngaycap, noicap, so_nha, tinh_thanhpho, quan_huyen, phuong_xa) 
        VALUES ('$makh', '$hoten', '$sodt', '$email', '$socccd', '$ngaycap', '$noicap', '$so_nha', '$tinh_thanhpho', '$quan_huyen', '$phuong_xa')";
        
        if (mysqli_query($conn, $insertKHQuery)) {
            echo "Khách hàng đã được thêm thành công!";
        } else {
            echo "Lỗi khi thêm khách hàng: " . mysqli_error($conn);
            exit;
        }
    } else {
        echo "Khách hàng đã tồn tại!";
    }

 // Tiếp tục insert vào bảng phieudattiec
    // Giả sử bạn đã lấy giá trị cho các trường cần thiết từ form
    // Lấy dữ liệu từ form
    $makh = $_POST['makh'];
    $sopdt = $_POST['sopdt'];

    $stt_sanh = $_POST['stt_sanh'];
    $mabuoi = $_POST['MABUOI'];
    $malt = $_POST['malt'];
    $ma_ctkm = $_POST['MA_CTKM'];
    $ngaydat = $_POST['ngaydat'];
    $ngaytochuc = $_POST['ngaytochuc'];
    $trangthai = isset($_POST['trangthai']) ? 'Hoàn chỉnh' : 'Chưa hoàn chỉnh'; // Kiểm tra trạng thái checkbox
    $tiencoc = $_POST['tiencoc'];
    $tiengiamgia = $_POST['tiengiamgia'];

    // Chuyển đổi ngày từ định dạng yyyy-mm-dd sang dd-mm-yyyy
    $ngaydat = DateTime::createFromFormat('Y-m-d', $ngaydat)->format('d-m-Y');
    $ngaytochuc = DateTime::createFromFormat('Y-m-d', $ngaytochuc)->format('d-m-Y');

    // In ra kết quả kiểm tra (Nếu cần)
    echo "Ngày đặt tiệc: " . $ngaydat;
    echo "Ngày tổ chức tiệc: " . $ngaytochuc;

    // Kiểm tra MABUOI
    if (isset($_POST['MABUOI'])) {
        $mabuoi = $_POST['MABUOI'];  // Lấy giá trị MABUOI từ form
        $queryMABUOI = "SELECT MABUOI FROM buoi WHERE MABUOI = '$mabuoi'";
        $resultMABUOI = mysqli_query($conn, $queryMABUOI);
        if ($resultMABUOI && mysqli_num_rows($resultMABUOI) > 0) {
            $rowMABUOI = mysqli_fetch_assoc($resultMABUOI);
            $MABUOI = $rowMABUOI['MABUOI'];
        } else {
            echo "Không tìm thấy MABUOI tương ứng.";
            exit;
        }
    }

    // Kiểm tra MALT
    if (isset($_POST['malt'])) {
        $malt = $_POST['malt'];  // Lấy giá trị malt từ form
        $queryMALT = "SELECT malt FROM loaitiec WHERE malt = '$malt'";
        $resultMALT = mysqli_query($conn, $queryMALT);
        if ($resultMALT && mysqli_num_rows($resultMALT) > 0) {
            $rowMALT = mysqli_fetch_assoc($resultMALT);
            $MALT = $rowMALT['malt'];
        } else {
            echo "Không tìm thấy MALT tương ứng.";
            exit;
        }
    }

    // Kiểm tra makh có tồn tại trong bảng khachhang không
    $queryMAKH = "SELECT makh FROM khachhang WHERE makh = '$makh'";
    $resultMAKH = mysqli_query($conn, $queryMAKH);
    if (!$resultMAKH || mysqli_num_rows($resultMAKH) == 0) {
        echo "Khách hàng không tồn tại!";
        exit;
    }

    // Trong câu lệnh INSERT, sử dụng các giá trị mới thay vì giá trị được truyền vào từ form
    $insertQuery = "INSERT INTO phieudattiec 
        (sopdt, makh, stt_sanh, mabuoi, malt, ma_ctkm, ngaydat, ngaytochuc, trangthai, tiencoc, tiengiamgia)
        VALUES
        ('$new_sopdt', '$makh', '$stt_sanh', '$MABUOI', '$MALT', '$ma_ctkm', '$ngaydat', '$ngaytochuc', '$trangthai', '$tiencoc', '$tiengiamgia')";

    // Thực thi câu lệnh INSERT
    session_start(); // Khởi tạo session

    if (mysqli_query($conn, $insertQuery)) {
        echo "Đặt tiệc thành công!";
        
        // Lưu dữ liệu vào session
        $_SESSION['message'] = "Đặt tiệc thành công!";
        $_SESSION['filtered_data'] = $filtered_data; // Lưu dữ liệu lọc, ví dụ từ câu truy vấn vừa thực hiện

        // Chuyển hướng sang trang ../../halls/halls.php
        header("Location: ../../bandattiec/formbandattiec.php");
        exit(); // Dừng quá trình thực thi mã sau khi chuyển hướng
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Cả hai menu.css + customer.css đều phải có trong mỗi trang php VÌ đây là css của thanh Header -->
    <link rel="stylesheet" href="css2/customer.css">
    <link rel="stylesheet" href="css2/menu.css">
    <title>Quản lý đặt tiệc</title>
    <style>
        /* Định dạng chung cho container của nhóm món ăn */
        .group {
            display: none; /* Ẩn tất cả các nhóm món ăn */
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
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        /* Định dạng danh sách các món ăn */
        .group-items {
            display: flex; /* Căn ngang */
            flex-wrap: wrap; /* Để các món tự động xuống dòng nếu không đủ chỗ */
            gap: 15px; /* Khoảng cách giữa các món */
            justify-content: center; /* Căn giữa các món */
            list-style: none;
            padding: 0;
            margin: 0;
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
            width: 150px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .monan-item img {
            margin-bottom: 5px; /* Khoảng cách giữa ảnh và tên món */
            width: 4rem; /* Đảm bảo ảnh không vượt quá chiều rộng của phần tử cha */
            height: 3rem;
            border: 1px solid;
            margin-left: rem;
            margin-left: 2.5rem;
            border-radius: 2rem; /* Tạo góc bo cho ảnh */
            display: block; /* Đảm bảo ảnh là khối độc lập */
        }

        .monan-item span {
            font-size: 11px;
            font-weight: bold;
            color: #333;
            display: block; /* Căn giữa văn bản trong Flexbox */
        }


        .pagination {
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
        /* Style cho form */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        /* display: flex;*/
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin-top: -1.9rem;
            background: white;
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
            background-image: linear-gradient(to bottom right ,#FF99CC,#33FFFF);
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
            width: 55rem;
/*            padding: 20px;*/
            margin-left: 3rem;
            justify-content: center;
/*            border: 1px solid*/
/*            justify-items: center;*/
        }

        .left {
            margin-left: -1rem;
            max-width: 55rem;
/*            border: 1px solid;*/
            justify-items: center;

        }
    </style>
</head>
<header>
    <nav class="navbar">
        <ul class="nav">
           <li class="menu-item"><a href="../../halls/halls.php">Sơ đồ sảnh</a></li>
           <li class="menu-item" style="border: 2px solid #007BFF">
             <a style="width: 7.1rem;" href="#">Thực đơn</a>
             <ul style="width: 9.5rem;margin-top: 0.1px;" class="sub-menu">
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 8rem;margin-left: -2rem;" href="../../monman/monman.php">Món mặn</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 8rem;margin-left: -2rem;" href="../../monchay/monchay.php">Món chay</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 8rem;margin-left: -2rem;" href="../../douong/douong.php">Đồ uống</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 8rem;margin-left: -2rem;" href="../../combomonman/combomonman.php">Bàn tiệc mẫu</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 9rem;margin-left: -2rem;" href="../../bandattiec/formbandattiec.php">Bàn đặt tiệc</a></li>
             </ul>
           </li>
           <li class="menu-item" style="border: 2px solid #007BFF">
             <a style="width: 8.5rem;" href="#">Quản lý thông tin</a>
             <ul class="sub-menu" style="margin-top: 0.1px;">
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */" class="menu-item"><a style="margin-left: -1.5rem;" href="../../customer/customer.php">Khách hàng</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */" class="menu-item"><a style="margin-left: -1.5rem;" href="#">Nhân viên</a></li>
             </ul>
           </li>
           <li class="menu-item" style="border: 2px solid #007BFF">
             <a style="width: 7.5rem;" href="#">Quản lý tài liệu</a>
             <ul style="width: 9.9rem;margin-top: 0.1px;" class="sub-menu">
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 9rem;margin-left: -2rem;" href="../../phieudattiec/dsphieudattiec.php">Phiếu đặt tiệc</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 9rem;margin-left: -2rem;" href="../../hopdong/hopdong.php">Hợp đồng</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */"><a style=" width: 9rem;margin-left: -2rem;" href="../../thongke/thongke.php">Thống kê/Báo cáo</a></li>
             </ul>
           </li>
           <li class="menu-item"><a href="#">Đăng xuất</a></li>
        </ul>
    </nav>
</header>

<body>

    <!-- Form Đặt Tiệc -->
    <form action="dat-tiec.php?sanh=<?php echo $_GET['sanh']; ?>" method="POST" id="booking-form">
        <div class="form-columns" style="margin-top:-3rem">
            <div class="form-column">
                <!--KHÁCH HÀNG-->
            <div>
                <label for="sohd">Họ tên khách:</label>
                <input type="text" name="hoten" placeholder="Họ và tên" required>
            </div>
            <div>
                <label for="ma_ctkm">Số điện thoại:</label>
                <input type="text" name="sodt" placeholder="Số điện thoại" required>
            </div>
            <div>
                <label for="sohd">Email:</label>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div>
                <label for="ma_ctkm">Số CCCD:</label>
                <input type="text" name="socccd" placeholder="Số CCCD" required>
            </div>
            <div>
                <label for="sohd">Ngày cấp:</label>
                <input type="date" name="ngaycap" placeholder="Ngày cấp" required>
            </div>
            <div>
                <label for="ma_ctkm">Nơi cấp:</label>
                <input type="text" name="noicap" placeholder="Nơi cấp" required>
            </div>
            <div>
                <label for="sohd">Số nhà:</label>
                <input type="text" name="so_nha" placeholder="Số nhà, đường" required>
            </div>
            <div>
                <label>Tỉnh/Thành phố:</label>
                <select id="tinh_thanhpho" name="tinh_thanhpho" style="padding: 10px; border: 1px solid #ccc;border-radius: 4px; height:2.43rem" required>
                    <option value="" >-- Chọn Tỉnh/Thành phố --</option>
                    <!-- Tỉnh/Thành phố sẽ được load từ AJAX -->
                </select>
            </div>
            <div>
                <label>Quận/Huyện:</label>
                <select id="quan_huyen" name="quan_huyen" style="padding: 10px; border: 1px solid #ccc;border-radius: 4px; height:2.43rem" required>
                    <option value="" >-- Chọn Quận/Huyện --</option>
                </select>
            </div>
            <div>
                <label>Phường/Xã:</label>
                <select id="phuong_xa" name="phuong_xa" style="padding: 10px; border: 1px solid #ccc;border-radius: 4px; height:2.43rem" required>
                    <option value="" >-- Chọn Phường/Xã --</option>
                </select>
            </div>
            <!--BÀN TIỆC ĐẶT-->
            
            <!--ĐẶT ĐỒ UỐNG-->
                
            </div>

            <div class="form-column" style="margin-left: 1.7rem;">
                <div style="width:11rem">
                    <label for="makh">KH:</label>
                    <input style="justify-items: center" type="text" name="makh" id="makh" 
               value="<?php echo isset($new_makh) && $new_makh != '' ? $new_makh : '28'; ?>" required>
                </div>
                <!--PHIẾU ĐẶT TIỆC-->
                <div style="width:11rem">
                    <label for="sopdt">Số PDT:</label>
                    <input style="justify-items: center" type="text" name="sopdt" id="sopdt" 
                           value="<?php echo $new_sopdt; ?>" readonly required>
                </div>
                <!-- HỢP ĐỒNG-->
                

                <!--SẢNH-->
                <div style="width:11rem">
                    <label for="stt_sanh">Mã Sảnh:</label>
                    <input 
                        style="justify-items: center" type="text" name="stt_sanh" id="stt_sanh" value="<?php echo htmlspecialchars($stt_sanh); ?>" 
                        readonly>
                </div>
                <!--BUỔI-->
                <div>
                    <label for="MABUOI">Buổi:</label>
                    <select name="MABUOI" id="MABUOI">
                        <!-- Options sẽ được lấy từ bảng 'buoi' -->
                        <?php
                        $BuoiQuery = "SELECT MABUOI, TENBUOI FROM buoi"; // Cập nhật truy vấn để lấy thông tin từ bảng 'buoi'
                        $resultBuoi = mysqli_query($conn, $BuoiQuery);
                        while ($rowBuoi = mysqli_fetch_assoc($resultBuoi)) {
                            echo "<option value='{$rowBuoi['MABUOI']}'>{$rowBuoi['TENBUOI']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <!--LOẠI TIỆC-->
                <div>
                    <label for="malt">Loại tiệc:</label>
                    <select name="malt" id="malt">
                        <!-- Options sẽ được lấy từ bảng 'buoi' -->
                        <?php
                        $LoaitiecQuery = "SELECT malt, tenlt FROM loaitiec"; // Cập nhật truy vấn để lấy thông tin từ bảng 'buoi'
                        $resultmalt = mysqli_query($conn, $LoaitiecQuery);
                        while ($rowmalt = mysqli_fetch_assoc($resultmalt)) {
                            echo "<option value='{$rowmalt['malt']}'>{$rowmalt['tenlt']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <!--NGÀY ĐẶT-->
                <div>
                    <label for="ngaydat">Ngày đặt:</label>
                    <input type="date" name="ngaydat" id="ngaydat" required>
                </div>
                <!--NGÀY TỔ CHỨC-->
                <div>
                    <label for="ngaytochuc">Ngày tổ Chức:</label>
                    <input type="date" name="ngaytochuc" id="ngaytochuc" required>
                </div>
                <!--CTKM-->
                <div>
                    <label for="MA_CTKM">CTKM:</label>
                    <select name="MA_CTKM" id="MA_CTKM">
                        <!-- Options sẽ được lấy từ bảng 'ctkm' -->
                        <?php
                        $CtkmQuery = "SELECT MA_CTKM, TEN_CTKM FROM ctkm"; // Cập nhật truy vấn để lấy thông tin từ bảng 'ctkm'
                        $resultCtkm = mysqli_query($conn, $CtkmQuery);
                        while ($rowCtkm = mysqli_fetch_assoc($resultCtkm)) {
                            echo "<option value='{$rowCtkm['MA_CTKM']}'>{$rowCtkm['TEN_CTKM']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <!--TRẠNG THÁI-->
                <div>
                    <label for="trangthai">Trạng thái:</label>
                    <input style="width: 1rem;" type="checkbox" name="trangthai" id="trangthai" value="Hoàn chỉnh"> Hoàn chỉnh
                </div>
                <!--TIỀN ĐẶT CỌC-->
                <div>
                    <label for="tiencoc">Tiền đặt cọc:</label>
                    <input type="number" name="tiencoc" id="tiencoc">
                </div>
                <!--TIỀN GIẢM GIÁ-->
                <div>
                    <label for="tiengiamgia">Tiền Giảm Giá:</label>
                    <input type="number" name="tiengiamgia" id="tiengiamgia">
                </div>
            </div>
        </div>
        

        <button type="submit">Chọn bàn tiệc đặt</button>
    </form>
    <script>
        // JavaScript để lấy thông tin khách hàng
            function fetchCustomerDetails(makh) {
            const customerSelect = document.getElementById("makh");
            const selectedOption = customerSelect.options[customerSelect.selectedIndex];
            const sdt = selectedOption ? selectedOption.getAttribute('data-sdt') : "";

            // Cập nhật số điện thoại vào input
            document.getElementById("sdt").value = sdt;

            // Nếu cần thiết, bạn có thể tiếp tục thực hiện fetch để lấy thông tin khác từ máy chủ
            if (makh !== "") {
                fetch(`fetch_customers.php?makh=${makh}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log(data); // Hiển thị dữ liệu khách hàng để kiểm tra
                        // Bạn có thể cập nhật các trường khác nếu cần
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        // Lấy tất cả các phần tử menu
                const menuItems = document.querySelectorAll('.menu-item');
                const sections = document.querySelectorAll('section');

                // Thêm sự kiện click cho từng mục trong menu
                menuItems.forEach((item) => {
                    item.addEventListener('click', function() {
                        // Xóa class active khỏi tất cả menu
                        menuItems.forEach((menuItem) => menuItem.classList.remove('active'));
                        // Thêm class active cho mục được chọn
                        this.classList.add('active');

                        // Ẩn tất cả các section
                        sections.forEach((section) => section.style.display = 'none');

                        // Hiển thị đúng section tương ứng với menu được chọn
                        const targetSection = this.id.replace('menu-', '');
                        document.getElementById(targetSection).style.display = 'block';
                    });
                });

            document.addEventListener('DOMContentLoaded', function() {
            // Lấy tỉnh/thành phố khi trang load
            fetch('get_tinh_thanhpho.php')
                .then(response => response.json())
                .then(data => {
                    const tinhThanhphoSelect = document.getElementById('tinh_thanhpho');
                    data.forEach(tinh => {
                        const option = document.createElement('option');
                        option.value = tinh.matinhtp;
                        option.textContent = tinh.ten_tinhtp;
                        tinhThanhphoSelect.appendChild(option);
                    });
                });

            // Khi chọn tỉnh/thành phố, lấy quận/huyện tương ứng
            document.getElementById('tinh_thanhpho').addEventListener('change', function() {
                const matp = this.value;
                fetch(`get_quan_huyen.php?ma_tinhtp=${matp}`)
                    .then(response => response.json())
                    .then(data => {
                        const quanHuyenSelect = document.getElementById('quan_huyen');
                        quanHuyenSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                        data.forEach(quan => {
                            const option = document.createElement('option');
                            option.value = quan.maqh;
                            option.textContent = quan.tenqh;
                            quanHuyenSelect.appendChild(option);
                        });
                    });
            });

            // Khi chọn quận/huyện, lấy phường/xã tương ứng
            document.getElementById('quan_huyen').addEventListener('change', function() {
                const maqh = this.value;
                fetch(`get_phuong_xa.php?ma_qh=${maqh}`)
                    .then(response => response.json())
                    .then(data => {
                        const phuongXaSelect = document.getElementById('phuong_xa');
                        phuongXaSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                        data.forEach(phuong => {
                            const option = document.createElement('option');
                            option.value = phuong.mapx;
                            option.textContent = phuong.tenpx;
                            phuongXaSelect.appendChild(option);
                        });
                    });
                });
            });

    </script>

</body>
</html>

<?php
$conn->close(); // Đóng kết nối ở cuối cùng
?>
