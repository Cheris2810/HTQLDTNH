<?php
// Kết nối đến cơ sở dữ liệu
$SERVER = "localhost";
$user = "root"; // Tên đăng nhập MySQL
$pass = ""; // Mật khẩu MySQL
$database = "tochuctiec"; // Tên cơ sở dữ liệu

$conn = new mysqli($SERVER, $user, $pass, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xác định trang hiện tại
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Số đồ uống hiển thị mỗi trang
$offset = ($page - 1) * $limit; // Tính toán vị trí bắt đầu lấy dữ liệu

// Lấy giá trị tìm kiếm từ GET và tránh lỗi SQL Injection
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = $conn->real_escape_string($search_query);

// Lấy từ khóa tìm kiếm từ URL
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
$search_query = $conn->real_escape_string($search_query); // Escape giá trị tìm kiếm

// Xác định lọc theo mã đơn vị tính (madvt)
$madvt = isset($_GET['madvt']) ? (int)$_GET['madvt'] : '';
$madvt_condition = '';
if ($madvt) {
    $madvt_condition = " AND douong.madvt = $madvt"; // Lọc theo madvt
}

// Điều kiện tìm kiếm kết hợp với lọc theo tên đơn vị tính (tendvt)
$search_condition = '';
if (!empty($search_query)) {
    $search_condition = " AND douong.ten_du LIKE '%$search_query%'"; // Tìm kiếm theo tên đồ uống
}

// Truy vấn lấy các đơn vị tính
$dvt_sql = "SELECT madvt, tendvt FROM donvitinh"; // Thêm bảng `dvt` nếu nó có trong cơ sở dữ liệu
$dvt_result = $conn->query($dvt_sql);

// Kết hợp tất cả điều kiện vào truy vấn
$sql = "SELECT douong.ma_du, douong.ten_du, douong.hinh_anh_du 
        FROM douong 
        WHERE 1=1 $search_condition $madvt_condition"; // Thêm điều kiện lọc madvt vào

$result = $conn->query($sql);

if (!$result) {
    die("Truy vấn lỗi: " . $conn->error);
}

// Truy vấn lấy dữ liệu từ bảng douong với điều kiện tìm kiếm
$sql = "SELECT douong.ma_du, douong.ten_du, douong.hinh_anh_du 
        FROM douong 
        WHERE 1=1 $search_condition";

$result = $conn->query($sql);

if (!$result) {
    die("Truy vấn lỗi: " . $conn->error);
}

// Xử lý kết quả
$douong_data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $douong_data[] = $row; // Lưu dữ liệu vào mảng để sử dụng sau
    }
}


// Truy vấn tổng số đồ uống
$total_sql = "SELECT COUNT(*) AS total FROM douong WHERE 1=1 $search_condition";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_douong = $total_row['total']; // Tổng số đồ uống trong cơ sở dữ liệu
$total_pages = ceil($total_douong / $limit); // Tính số trang

// Truy vấn lấy dữ liệu từ bảng loaimon
$loaimon_sql = "SELECT ma_lm, ten_lm FROM loaimon";
$loaimon_result = $conn->query($loaimon_sql);

// Truy vấn lấy dữ liệu từ bảng nhomdouong
$nhomdouong_sql = "SELECT ma_nm, ten_nm FROM nhommon"; // Sửa thành nhomdouong nếu có bảng tương ứng
$nhomdouong_result = $conn->query($nhomdouong_sql);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đặt tiệc</title>
    <link rel="stylesheet" href="css2/menu.css">       
    <link rel="stylesheet" href="css2/customer.css">
    <style>
        body {
            margin-bottom: 8.7rem;
/*            margin-top:-1rem;*/
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
            display: flex;
            height: 175vh;
            margin: 0;
/*            margin-top: 2.1rem;*/
/*            background: linear-gradient(to bottom, #FFB6C1, #FFE4E1);*/
            background: white;
        }
        .container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            background: white;
        }

        .left, .right {
            flex: 1;
            padding: 20px;
            background: white;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .left {
            max-width: 17rem;
            margin-right: 1.5rem;
            max-height: 35rem;
        }



        h1 {
            text-align: center;
        }

        /* Form Thêm Món Ăn */
        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #007BFF;
            font-weight: bold;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color:#0000FF;
            color:white;
            font-weight: bold;
        }

        /* Bảng Danh Sách Món Ăn */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
            font-size: large;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        td img {
            width: 5rem;
            height: 3rem;
            object-fit: cover;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .pagination button {
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            background-color: #0000FF;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }

        .pagination button.disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        /* CSS cho cột Trạng thái */
        .status {
            color: black; /* Chữ màu đen */
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            color: orange;
        }

</style>
</head>
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
<body style="margin-top: 4rem;">

    <!-- Form thêm đồ uống -->
    <div class="left" style="margin-left:1rem">
        <form action="connectdouong.php" method="POST" enctype="multipart/form-data" id="douongForm">
            <h1 style="font-size: x-large; color:black; margin-top:-1rem">Thêm đồ uống mới</h1>
            <label for="ten_du" style="color: black">Tên đồ uống:</label>
            <input style="margin-top: -0.5rem;" type="text" id="ten_du" name="ten_du" required>

            <!-- Dropdown cho đơn vị tính -->
            <select id="madvt" name="madvt" style="padding: 7.9px; width: 15.6rem; border: 1px solid #ccc; border-radius: 4px; margin-top: -1.3rem;" required>
                <option value="">-- Chọn đơn vị tính --</option>
                <?php
                // Truy vấn lấy dữ liệu từ bảng donvitinh
                $donvitinh_sql = "SELECT madvt, tendvt FROM donvitinh";
                $donvitinh_result = $conn->query($donvitinh_sql);

                if ($donvitinh_result->num_rows > 0) {
                    while ($row = $donvitinh_result->fetch_assoc()) {
                        echo '<option value="' . $row['madvt'] . '">' . $row['tendvt'] . '</option>';
                    }
                }
                ?>
            </select>

            <label for="hinh_anh_du" style="color:black">Hình ảnh:</label>
            <input type="file" id="hinh_anh_du" name="hinh_anh_du" accept="image/*" required>

            <img id="previewImg" src="" alt="Xem trước hình ảnh" style="display:none; width: 100%; margin-top: 10px; border-radius: 4px;"/>

            <input type="submit" value="Thêm đồ uống">
        </form>

            </div>
            <!-- Danh sách đồ uống -->
            <div class="right">
                <div style="display: flex;">
                <div class="searchcss" style="display:flex">
                    <!-- Form lọc đơn vị tính -->
        <form method="get" action="" style="width: 10rem; margin-left: 0rem;">
    <div>
        <div style="display: flex;">
            <!-- Phần input lọc -->
        </div>
        <div>
            <select name="filter_dvt" id="filter_dvt" onchange="this.form.submit()">
                <option value="">Tất cả</option>
                <?php
                // Kiểm tra giá trị đã chọn từ GET
                $filter_dvt = isset($_GET['filter_dvt']) ? (int)$_GET['filter_dvt'] : '';

                // Lấy danh sách đơn vị tính (tendvt) từ bảng donvitinh
                $dvt_sql = "SELECT madvt, tendvt FROM donvitinh";
                $dvt_result = $conn->query($dvt_sql);

                if ($dvt_result->num_rows > 0) {
                    while ($dvt_row = $dvt_result->fetch_assoc()) {
                        // Giữ lựa chọn đơn vị tính đã chọn
                        $selected = ($dvt_row['madvt'] == $filter_dvt) ? 'selected' : '';
                        echo '<option value="' . $dvt_row['madvt'] . '" ' . $selected . '>' . $dvt_row['tendvt'] . '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>
</form>

<!-- Thêm nút In danh sách -->
<form method="get" action="in_danh_sach_douong.php">
    <!-- Giữ giá trị filter_dvt khi bấm nút In danh sách -->
    <input type="hidden" name="filter_dvt" value="<?php echo htmlspecialchars($filter_dvt); ?>">
    <input type="submit" value="In danh sách đồ uống" style="width: 13rem; height: 2rem; margin-top: -0.3rem; margin-left: -4rem;">
</form>


        </div>
            <div class="searchcss" style="display:flex; margin-left: 5rem; margin-top: -0.3rem;">
                    <form method="get" action="" style="max-width: 15rem; margin-left: 15rem;">
                                <input type="text" name="search" placeholder="Tìm kiếm đồ uống..." value="<?php echo htmlspecialchars($search_query); ?>">
                                <input type="submit" value="Tìm kiếm" style="max-width:5rem;height: 2rem; margin-top: -3.9rem; margin-left: 17.5rem;">
                    </form>
            </div>
        </div>

        <h1 style="color:black; margin-top: -0.5rem;">Danh sách đồ uống</h1>
        <table>
            <thead>
                <tr>
                    <th>Mã đồ uống</th>
                    <th>Tên đồ uống</th>
                    <th>Hình ảnh</th>
                    <th>Trạng thái</th> <!-- Thêm cột Trạng thái -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Lấy giá trị tìm kiếm từ GET
                $search_query = isset($_GET['search']) ? $_GET['search'] : '';
                $search_condition = '';
                if (!empty($search_query)) {
                    $search_condition = "WHERE douong.ten_du LIKE '%$search_query%'";
                }

                // Lấy giá trị filter_dvt từ GET (để lọc theo madvt)
                $filter_dvt = isset($_GET['filter_dvt']) ? (int)$_GET['filter_dvt'] : null;

                // Thêm điều kiện lọc theo madvt vào truy vấn nếu có filter_dvt
                if ($filter_dvt !== null) {
                    if ($search_condition !== '') {
                        $search_condition .= " AND giadouong.madvt = $filter_dvt";
                    } else {
                        $search_condition = "WHERE giadouong.madvt = $filter_dvt";
                    }
                }

                // Truy vấn lấy danh sách đồ uống
                $sql = "
                    SELECT douong.ma_du, douong.ten_du, douong.hinh_anh_du, giadouong.trangthai
                    FROM douong
                    LEFT JOIN giadouong ON douong.ma_du = giadouong.ma_du
                    $search_condition
                ";

                $result = $conn->query($sql);

                if (!$result) {
                    die("Truy vấn lỗi: " . $conn->error);
                }

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>
                            <td>' . $row['ma_du'] . '</td>
                            <td>' . $row['ten_du'] . '</td>
                            <td><img src="' . $row['hinh_anh_du'] . '" alt="Hình ảnh đồ uống" style="width:55px;height:auto;"></td>
                            <td class="status">' . $row['trangthai'] . '</td> <!-- Hiển thị trạng thái -->
                        </tr>';
                    }
                } else {
                    echo '<tr><td colspan="4">Không có đồ uống nào.</td></tr>';
                }
                ?>
            </tbody>
        </table>

    </div>

    <script>
        // Hiển thị hình ảnh xem trước
        document.getElementById('hinh_anh_du').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImg = document.getElementById('previewImg');
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                const previewImg = document.getElementById('previewImg');
                previewImg.src = '';
                previewImg.style.display = 'none';
            }
        });

        // Xử lý gửi form
        document.getElementById('douongForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Ngăn chặn hành động mặc định của form

            const formData = new FormData(this); // Tạo FormData từ form

            fetch('connectdouong.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Thêm đồ uống mới vào danh sách
                    const newRow = `<tr>
                        <td>${data.ma_du}</td>
                        <td>${data.ten_du}</td>
                        <td><img src="${data.hinh_anh_du}" alt="Hình ảnh đồ uống"></td>
                    </tr>`;
                    document.querySelector('tbody').insertAdjacentHTML('beforeend', newRow);
                    
                    // Reset form
                    this.reset();
                    document.getElementById('previewImg').style.display = 'none'; // Ẩn hình xem trước
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                alert('Có lỗi xảy ra khi thêm đồ uống.');
            });
        });

    </script>
</body>
</html>