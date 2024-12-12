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
$limit = 10; // Số món ăn hiển thị mỗi trang
$offset = ($page - 1) * $limit; // Tính toán vị trí bắt đầu lấy dữ liệu

// Lấy giá trị tìm kiếm từ GET và tránh lỗi SQL Injection
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = $conn->real_escape_string($search_query);

// Lọc loại món
$filter_loaimon = isset($_GET['filter_nhommon']) ? $_GET['filter_nhommon'] : '';
$filter_loaimon = $conn->real_escape_string($filter_loaimon); // Escape giá trị lọc loại món

// Lọc món chay/mặn
$ma_chayman = isset($_GET['ma_chayman']) ? (int)$_GET['ma_chayman'] : 1; // Giá trị mặc định là 1 nếu không chọn

// Xử lý điều kiện tìm kiếm và lọc
$search_condition = '';
if (!empty($search_query)) {
    $search_condition = " AND monan.ten_ma LIKE '%$search_query%'";
}

$filter_condition = '';
if (!empty($filter_loaimon)) {
    $filter_condition = " AND loaimon.ma_lm = '$filter_loaimon'";
}

// Truy vấn lấy dữ liệu từ bảng monan với điều kiện tìm kiếm, lọc loại món và MaChayMan
$sql = "SELECT monan.ma_ma, monan.ten_ma, monan.hinh_anh, loaimon.ten_lm 
        FROM monan 
        LEFT JOIN loaimon ON monan.ma_loaimon = loaimon.ma_lm 
        WHERE monan.MaChayMan = $ma_chayman $search_condition $filter_condition 
        LIMIT $limit OFFSET $offset";

// Thực thi truy vấn
$result = $conn->query($sql);

if (!$result) {
    die("Truy vấn lỗi: " . $conn->error);
}

// Xử lý kết quả
$monan_data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $monan_data[] = $row;  // Lưu dữ liệu vào mảng để sử dụng sau
    }
}

// Truy vấn tổng số món ăn với MaChayMan
$total_sql = "SELECT COUNT(*) AS total FROM monan WHERE MaChayMan = $ma_chayman $search_condition";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_monan = $total_row['total']; // Tổng số món ăn trong cơ sở dữ liệu
$total_pages = ceil($total_monan / $limit); // Tính số trang

// Truy vấn lấy dữ liệu từ bảng loaimon
$loaimon_sql = "SELECT ma_lm, ten_lm FROM loaimon";
$loaimon_result = $conn->query($loaimon_sql);

// Truy vấn lấy dữ liệu từ bảng nhommon
$nhommon_sql = "SELECT ma_nm, ten_nm FROM nhommon";
$nhommon_result = $conn->query($nhommon_sql);

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

    </style>
</head>
<header style="margin-left:-0.5rem">
    <nav class="navbar">
        <ul class="nav">
           <li class="menu-item"><a href="../halls/halls.php" >Sơ đồ sảnh</a></li>
           <li class="menu-item  active" style="border: 2px solid #007BFF">
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

    <!-- Form thêm món ăn -->
    <div class="left" style="margin-left:1rem">
        <form action="connectmonman.php" method="POST" enctype="multipart/form-data" id="monanForm">
            <h1 style="font-size: x-large; color:black; margin-top:-1rem">Thêm món ăn mới</h1>
            <label for="ten_ma" style="color: black">Tên món ăn:</label>
            <input style="margin-top: -0.5rem;" type="text" id="ten_ma" name="ten_ma" required>
            
            <!-- Dropdown cho loại món -->
            <select id="ma_loaimon" name="ma_loaimon" style="padding: 7.9px; width: 15.6rem; border: 1px solid #ccc;border-radius: 4px; margin-top: -1.3rem;" required>
                <option value="">-- Chọn loại món --</option>
                <?php
                if ($loaimon_result->num_rows > 0) {
                    while ($row = $loaimon_result->fetch_assoc()) {
                        echo '<option value="' . $row['ma_lm'] . '">' . $row['ten_lm'] . '</option>';
                    }
                }
                ?>
            </select>

            <!-- Dropdown cho nhóm món -->
            <select id="ma_nhommon" name="ma_nhommon" style="padding: 7.9px; width: 15.6rem; border: 1px solid #ccc;border-radius: 4px; margin-top: rem;" required>
                <option value="">-- Chọn nhóm món --</option>
                <?php
                if ($nhommon_result->num_rows > 0) {
                    while ($row = $nhommon_result->fetch_assoc()) {
                        echo '<option value="' . $row['ma_nm'] . '">' . $row['ten_nm'] . '</option>';
                    }
                }
                ?>
            </select>
            
            <label for="hinh_anh" style="color:black">Hình ảnh:</label>
            <input type="file" id="hinh_anh" name="hinh_anh" accept="image/*" required>
            
            <!-- Thêm thẻ img để hiển thị hình ảnh xem trước -->
            <img id="previewImg" src="" alt="Xem trước hình ảnh" style="display:none; width: 100%; margin-top: 10px; border-radius: 4px;"/>

            <!-- Thêm lựa chọn món chay/mặn -->
            <div style="display:flex; margin-top: -1rem;">
        <div>
            <input style="margin-left: 4rem" type="radio" id="monchay" name="ma_chayman" value="1" <?php echo (isset($_GET['ma_chayman']) && $_GET['ma_chayman'] == '1') ? 'checked' : ''; ?> required>
            <label for="monchay" style="margin-left: 2rem">Món chay</label>
        </div>
        <div>
            <input style="margin-left: 4rem" type="radio" id="monman" name="ma_chayman" value="2" <?php echo (isset($_GET['ma_chayman']) && $_GET['ma_chayman'] == '2') ? 'checked' : ''; ?> required>
            <label style="margin-left: 2rem" for="monman">Món mặn</label>
        </div>
    </div>
            <input type="submit" value="Thêm món ăn">
        </form>
    </div>


    <!-- Danh sách món ăn -->
    <div class="right">
        <div style="display: flex;">
            <div class="searchcss" style="display:flex">
                <!-- Form tìm kiếm -->
               <label for="filter_loaimon" style="margin-top:1.3rem">Lọc theo loại món:</label>
            </div>

            <!-- Form lọc nhóm món -->
            <form method="get" action="" style="width: 10rem; margin-left: 0rem;">
                <div>
                    <div style="display: flex;">
                        
                    </div>
                    <div>
                        <select name="filter_nhommon" id="filter_loaimon" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            <?php
                            // Lấy danh sách loại món từ cơ sở dữ liệu chỉ với MaChayMan = 1
                            $loaimon_sql = "SELECT ma_lm, ten_lm FROM loaimon WHERE MaChayMan = 1";
                            $loaimon_result = $conn->query($loaimon_sql);

                            if ($loaimon_result->num_rows > 0) {
                                while ($loaimon_row = $loaimon_result->fetch_assoc()) {
                                    // Giữ lựa chọn loại món đã chọn
                                    $selected = ($loaimon_row['ma_lm'] == $filter_nhommon) ? 'selected' : '';
                                    echo '<option value="' . $loaimon_row['ma_lm'] . '" ' . $selected . '>' . $loaimon_row['ten_lm'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </form>

            <!-- Thêm nút In danh sách -->
            <form method="get" action="in_danh_sach.php" >
                <input type="hidden" name="filter_nhommon" value="<?php echo htmlspecialchars($filter_loaimon); ?>">
                <input type="submit" value="In danh sách các món đã lọc" style="width: 13rem; height: 2rem; margin-top: -0.3rem;">
            </form>
            <div class="searchcss" style="display:flex; margin-left: 5rem; margin-top: -0.3rem;">
                <form method="get" action="" style="max-width: 15rem;">
                    <input type="text" name="search"  placeholder="Tìm kiếm loại món..." value="<?php echo htmlspecialchars($search_query); ?>">
                    <input type="submit" value="Tìm kiếm" style="max-width:5rem;height: 2rem; margin-top: -3.9rem; margin-left: 17.5rem;">
                </form>
            </div>
        </div>
    <h1 style="color:black; margin-top: -0.5rem;">Danh sách món chay</h1>
    <table>
    <thead>
        <tr>
            <th>Mã món</th>
            <th>Tên món</th>
            <th>Hình ảnh</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Lọc theo tên món
        $search_query = isset($_GET['search']) ? $_GET['search'] : '';
        $search_condition = '';
        if (!empty($search_query)) {
            $search_condition = " AND monan.ten_ma LIKE '%$search_query%'";
        }

        // Lọc theo loại món
        $filter_nhommon = isset($_GET['filter_nhommon']) ? $_GET['filter_nhommon'] : '';
        $filter_condition = '';
        if (!empty($filter_nhommon)) {
            $filter_condition = " AND loaimon.ma_lm = '$filter_nhommon'";
        }

        // Truy vấn lấy dữ liệu từ bảng monan với điều kiện lọc và MaChayMan = 1
        $sql = "SELECT monan.ma_ma, monan.ten_ma, monan.hinh_anh, loaimon.ten_lm 
                FROM monan 
                LEFT JOIN loaimon ON monan.ma_loaimon = loaimon.ma_lm 
                WHERE monan.MaChayMan = 1 $search_condition $filter_condition";
        $result = $conn->query($sql);

        if (!$result) {
            die("Truy vấn lỗi: " . $conn->error);
        }

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>
                    <td>' . $row['ma_ma'] . '</td>
                    <td>' . $row['ten_ma'] . '</td>
                    <td><img src="' . $row['hinh_anh'] . '" alt="Hình ảnh món ăn" style="width:50px;height:auto;"></td>
                </tr>';
            }
        } else {
            echo '<tr><td colspan="3">Không có món ăn nào.</td></tr>';
        }
        ?>
    </tbody>
    </table>

            <!-- Phân trang -->
                <div class="pagination">
                    <button onclick="changePage(1)" class="<?php echo $page === 1 ? 'disabled' : ''; ?>">Đầu</button>
                    <button onclick="changePage(<?php echo $page - 1; ?>)" class="<?php echo $page === 1 ? 'disabled' : ''; ?>">Trước</button>
                    <button onclick="changePage(<?php echo $page + 1; ?>)" class="<?php echo $page === $total_pages ? 'disabled' : ''; ?>">Tiếp theo</button>
                    <button onclick="changePage(<?php echo $total_pages; ?>)" class="<?php echo $page === $total_pages ? 'disabled' : ''; ?>">Cuối</button>
                </div>
            </div>
            </div>
        </div>

    <script>
        // Hiển thị hình ảnh xem trước
        document.getElementById('hinh_anh').addEventListener('change', function(event) {
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

        // Chức năng phân trang
        function changePage(page) {
            if (page < 1 || page > <?php echo $total_pages; ?>) return;
            window.location.href = '?page=' + page; // Điều hướng đến trang mới
        }

        // Xử lý gửi form
        document.getElementById('monanForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Ngăn chặn hành động mặc định của form

            const formData = new FormData(this); // Tạo FormData từ form

            fetch('connectmonman.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Thêm món ăn mới vào danh sách
                    const newRow = `<tr>
                        <td>${data.ma_ma}</td>
                        <td>${data.ten_ma}</td>
                        <td><img src="${data.hinh_anh}" alt="Hình ảnh món ăn"></td>
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
                alert('Có lỗi xảy ra khi thêm món ăn.');
            });
        });
    </script>
</body>
</html>
