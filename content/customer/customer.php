<?php
// Kết nối đến cơ sở dữ liệu
$SERVER = "localhost";
$user = "root";
$pass = "";
$database = "tochuctiec";

$conn = new mysqli($SERVER, $user, $pass, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý thêm khách hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
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


    $sql = "INSERT INTO khachhang (hoten, sodt, email, socccd, ngaycap, noicap, so_nha, tinh_thanhpho, quan_huyen, phuong_xa) 
            VALUES ('$hoten', '$sodt', '$email', '$socccd', '$ngaycap', '$noicap', '$so_nha', '$tinh_thanhpho', '$quan_huyen', '$phuong_xa')";
    $conn->query($sql);
}

// Xử lý xóa khách hàng
if (isset($_GET['delete'])) {
    $makh = (int)$_GET['delete'];
    $conn->query("DELETE FROM khachhang WHERE makh = $makh");
}

// Lấy danh sách khách hàng
$result = $conn->query("SELECT * FROM khachhang");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý TCT</title>
    <link rel="stylesheet" href="css2/customer.css">
    <link rel="stylesheet" href="css2/menu.css">
</head>
<style>
        body {
            background: white;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

         th {
            background-color: #007BFF;
            color: white;
        }

        form button {
            background: #007BFF;
            color: white;
            font-weight: bold;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #696969;
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
        body {
            font-family: Comic Sans MS, sans-serif;
        }
</style>
<header style="margin-left:-0.5rem">
    <nav class="navbar">
        <ul class="nav">
           <li class="menu-item "><a href="../halls/halls.php" >Sơ đồ sảnh</a></li>
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
           <li class="menu-item active" style="border: 2px solid #007BFF">
             <a style="width: 8.5rem;" href="#">Quản lý thông tin</a>
             <ul class="sub-menu" style="margin-top: 3px;">
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */" class="menu-item"><a style="margin-left: -1.5rem;" href="../customer/customer.php">Khách hàng</a></li>
                <li style="background: transparent; /* Trong suốt để lộ nền của .related */" class="menu-item"><a style="margin-left: -1.5rem;" href="#">Nhân viên</a></li>
             </ul>
           </li>
           <li class="menu-item" style="border: 2px solid #007BFF">
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
    <div class="container">
        <h1 style="margin-top: -1rem; color:black;">Quản lý thông tin khách hàng</h1>
        
        <table style="font-size: x-small; justify-content: center; margin-top: -1rem;">
            
            <?php while($row = $result->fetch_assoc()): ?>
            <?php 
                // Truy vấn dữ liệu khách hàng và thông tin địa chỉ
            $sql = "SELECT kh.makh, kh.hoten, kh.so_nha, kh.sodt, kh.email, kh.socccd, kh.ngaycap, kh.noicap,
                           tp.ten_tinhtp, qh.tenqh, px.tenpx
                    FROM khachhang kh
                    JOIN tinh_thanhpho tp ON kh.tinh_thanhpho = tp.matinhtp
                    JOIN quan_huyen qh ON kh.quan_huyen = qh.maqh
                    JOIN phuong_xa px ON kh.phuong_xa = px.mapx";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Hiển thị dữ liệu trong bảng
                echo "<table border='1'>";
                echo "<tr>
                        <th>Mã KH</th>
                        <th>Họ và tên</th>
                        <th>Số nhà</th>
                        <th>Phường/Xã</th>
                        <th>Quận/Huyện</th>
                        <th>Tỉnh/Thành phố</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Số CCCD</th>
                        <th>Ngày cấp</th>
                        <th>Nơi cấp</th>
                        <th>Thao tác</th>
                      </tr>";
                      
                // Duyệt qua từng hàng và hiển thị dữ liệu
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['makh'] . "</td>";
                    echo "<td>" . $row['hoten'] . "</td>";
                    echo "<td>" . $row['so_nha'] . "</td>";
                    echo "<td>" . $row['tenpx'] . "</td>";  // Tên phường/xã
                    echo "<td>" . $row['tenqh'] . "</td>";  // Tên quận/huyện
                    echo "<td>" . $row['ten_tinhtp'] . "</td>";  // Tên tỉnh/thành phố
                    echo "<td>" . $row['sodt'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['socccd'] . "</td>";
                    echo "<td>" . $row['ngaycap'] . "</td>";
                    echo "<td>" . $row['noicap'] . "</td>";
                    echo "<td><a href='?delete=" . $row['makh'] . "' onclick=\"return confirm('Bạn có chắc chắn muốn xóa?');\">Xóa</a></td>";

                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "Không có dữ liệu";
            }

            $conn->close();
            ?>
            <?php endwhile; ?>
        </table>
    </div>
    <script>
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
<!-- <footer style="background: #FF6A6A; font-weight: bold; color:white;">
     &copy Quản lý tổ chức tiệc
</footer> -->
</html>