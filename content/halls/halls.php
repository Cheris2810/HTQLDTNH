<?php
$SERVER = "localhost";
$user = "root";
$pass = "";
$database = "tochuctiec";

// Kết nối tới cơ sở dữ liệu
$conn = new mysqli($SERVER, $user, $pass, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start(); // Khởi tạo session

// Xử lý lọc
$ngaydat = isset($_GET['formattedDate']) ? $_GET['formattedDate'] : '';
$session = isset($_GET['mabuoi']) ? $_GET['mabuoi'] : '';

// Chuyển đổi định dạng ngày từ dd-mm-yyyy sang yyyy-mm-dd
function convertDateFormat($date) {
    $dateArray = explode('-', $date);
    return count($dateArray) == 3 ? $dateArray[2] . '-' . $dateArray[1] . '-' . $dateArray[0] : null;
}


if (!empty($ngaydat)) {
    $ngaydat = convertDateFormat($ngaydat);
}

// Lấy dữ liệu từ bảng phieudattiec nếu có dữ liệu lọc
$sanh_data = [];
if (!empty($ngaydat) && !empty($session)) {
    $sql = "SELECT s.stt_sanh, s.ten_tang 
            FROM phieudattiec p 
            JOIN sanh s ON s.stt_sanh = p.stt_sanh 
            WHERE STR_TO_DATE(p.ngaydat, '%d-%m-%Y') = ? AND p.mabuoi = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $ngaydat, $session);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sanh_data[$row['ten_tang']][] = $row['stt_sanh']; // Nhóm theo tầng
        }
    }
    $stmt->close();
}

// Lấy tất cả dữ liệu từ bảng sanh
$sanh_all_data = [];
$sanh_all_sql = "SELECT s.stt_sanh, s.ten_tang FROM sanh s";
$sanh_all_result = $conn->query($sanh_all_sql);
while ($row = $sanh_all_result->fetch_assoc()) {
    $sanh_all_data[$row['ten_tang']][] = $row['stt_sanh']; // Nhóm theo tầng
}

// Lấy thông tin tầng
$tang_sql = "SELECT DISTINCT ten_tang FROM sanh";
$tang_result = $conn->query($tang_sql);
$tang_data = [];
while ($row = $tang_result->fetch_assoc()) {
    $tang_data[] = $row['ten_tang'];
}

// Lấy dữ liệu từ bảng buoi để hiển thị trong dropdown
$buoi_result = $conn->query("SELECT * FROM buoi");
$buoi_options = [];
while ($buoi_row = $buoi_result->fetch_assoc()) {
    $buoi_options[] = $buoi_row;
}

$tenbuoi = '';
if (!empty($session)) {
    $stmt_buoi = $conn->prepare("SELECT TENBUOI FROM buoi WHERE MABUOI = ?");
    $stmt_buoi->bind_param("i", $session);
    $stmt_buoi->execute();
    $result_buoi = $stmt_buoi->get_result();

    if ($result_buoi->num_rows > 0) {
        $row_buoi = $result_buoi->fetch_assoc();
        $tenbuoi = $row_buoi['TENBUOI'];
    }
    $stmt_buoi->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css2/menu.css">
    <title>Quản lý đặt tiệc</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container {
            display: flex;
            margin-top: 0rem; /* Khoảng cách từ trên */
        }
        .left-panel, .right-panel {
            flex: 1; /* Mỗi bên chiếm 1 phần bằng nhau */
            padding: 20px;
        }
        .right-panel {
            border-right: none; /* Không cần đường kẻ bên phải */
        }
        .row {
            display: flex; /* Căn chỉnh các tầng theo chiều ngang */
            justify-content: space-around; /* Tạo khoảng cách đều giữa các tầng */
            margin-bottom: 0rem; /* Thêm khoảng cách giữa các hàng */
        }
        .tang {
            flex: 1; /* Mỗi tầng chiếm 1 phần bằng nhau trong hàng */
            text-align: center; /* Căn giữa nội dung trong từng tầng */
        }
        .square {
            display: inline-block;
            width: 250px;
            height: 110px;
            text-align: center;
            line-height: 100px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            font-weight: bold;
        }
        .square:hover {
            transform: translateY(-5px); /* Nhảy nhẹ lên */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); /* Tạo bóng khi hover */
        }
        

        button.booked:hover {
            color: #fff; /* Màu chữ vẫn trắng */
        }

        button.available {
            background: transparent; /* Trong suốt để lộ nền của .related */
            color: white; /* Màu chữ trắng */
            border: none; /* Loại bỏ viền */
            cursor: pointer; /* Hiển thị biểu tượng tay khi hover */
            font-size: inherit; /* Kế thừa kích thước chữ từ .related */
            position: relative; /* Để căn chỉnh theo ngữ cảnh */
            padding: 10px; /* Khoảng cách bên trong nút */
        }

        .related {
            background-image: linear-gradient(to bottom right ,#FF99CC,#33FFFF); /* Nền gradient */
            color: white; /* Màu chữ */
        }
        .unrelated { 
            background-color: #007BFF; /* Màu nền xanh biển */
            color: white; /* Màu chữ trắng */
        }

        button.booked {
            background: transparent; /* Trong suốt để lộ nền của .related */
            color: white; /* Màu chữ trắng */
            border: none; /* Loại bỏ viền */
            cursor: pointer; /* Hiển thị biểu tượng tay khi hover */
            font-size: inherit; /* Kế thừa kích thước chữ từ .related */
            position: relative; /* Để căn chỉnh theo ngữ cảnh */
            padding: 10px; /* Khoảng cách bên trong nút */
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
        
    </style>
</head>
<header style="margin-left:-0.5rem">
    <nav class="navbar">
        <ul class="nav">
           <li class="menu-item active"><a href="../halls/halls.php" >Sơ đồ sảnh</a></li>
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
    <form style="margin-top:3rem" method="GET" action="">
        <label for="ngaydat">Ngày:</label>
    <input type="date" id="ngaydat" name="ngaydat" required onchange="convertDateToVarchar()">
    <input type="text" id="formattedDate" name="formattedDate" readonly style="border: none; background: transparent; display: none;"> <!-- Hiển thị giá trị dd-mm-yyyy -->
        <label for="mabuoi">Buổi:</label>
        <select name="mabuoi" id="mabuoi">
            <option value="">Chọn buổi</option>
            <?php foreach ($buoi_options as $buoi): ?>
                <option value="<?= $buoi['MABUOI'] ?>"><?= $buoi['TENBUOI'] ?></option>
            <?php endforeach; ?>
        </select>
        <input style="background-color: #007BFF; color:white; border:1px solid #007bff; border-radius: 0.3rem;" type="submit" value="Kiểm tra">
    </form>

    <div class="container">
    <div class="left-panel" style="margin-top:-0.5rem">
        <?php if (!empty($ngaydat) && !empty($session)): ?>
            <?php
                $ngaydat_formatted = '';
                if ($ngaydat) {
                    $dateObject = DateTime::createFromFormat('Y-m-d', $ngaydat);
                    if ($dateObject) {
                        $ngaydat_formatted = $dateObject->format('d-m-Y');
                    } else {
                        // Xử lý trường hợp định dạng không hợp lệ
                        $ngaydat_formatted = htmlspecialchars($ngaydat); // Hoặc giữ nguyên giá trị không định dạng
                    }
                }
                ?>

            <p><strong>Ngày:</strong> <?= htmlspecialchars($ngaydat_formatted) ?> <strong>; buổi:</strong> <?= htmlspecialchars($tenbuoi) ?></p>
            <hr>
        <?php endif; ?>

        <?php if (!empty($sanh_all_data)): ?>
        <?php 
        $tang_count = 0;
        foreach ($sanh_all_data as $ten_tang => $stt_sanh_array): ?>
            <?php if ($tang_count % 2 == 0): ?>
                <div class="row">
            <?php endif; ?>

            <div class="tang">
                <h3><?php echo $ten_tang; ?></h3>
                <?php foreach ($stt_sanh_array as $stt_sanh): ?>
                <?php 
                    $trangthai = (in_array($stt_sanh, $sanh_data[$ten_tang] ?? [])) ? 'related' : 'unrelated'; 
                ?>
                <div class="square <?php echo $trangthai; ?>">
                    <button class="<?php echo $trangthai == 'related' ? 'available' : 'booked'; ?>" 
                            onclick="handleClick('<?php echo $stt_sanh; ?>', '<?php echo $trangthai == 'related' ? 'available' : 'booked'; ?>')">
                        <?php echo "Sảnh $stt_sanh"; ?>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>

            <?php 
            if ($tang_count % 2 == 1): ?>
                    </div> <!-- Kết thúc hàng nếu là tầng thứ 2 -->
            <?php endif; ?>

            <?php $tang_count++; // Tăng biến đếm số tầng ?>
        <?php endforeach; ?>

        <?php if ($tang_count % 2 == 1): ?>
            </div> <!-- Kết thúc hàng cuối nếu tầng không chẵn -->
        <?php endif; ?>

        <?php else: ?>
            <p>Không có dữ liệu để hiển thị.</p>
        <?php endif; ?>


        </div>
    </div>

<script>
function handleClick(sttSanh, trangThai) {
    if (trangThai === 'available') {
        alert('Sảnh đã được đặt');
    } else {
        window.location.href = 'formdattiec/dat-tiec.php?sanh=' + encodeURIComponent(sttSanh);
    }
}
function convertDateToVarchar() {
    const dateInput = document.getElementById("ngaydat");
    const formattedDateInput = document.getElementById("formattedDate");

    if (dateInput.value) {
        const [year, month, day] = dateInput.value.split("-");
        const formattedDate = `${day}-${month}-${year}`; // Chuyển đổi sang dd-mm-yyyy
        formattedDateInput.value = formattedDate; // Gán giá trị vào input hiển thị
    }
}
</script>
</body>
</html>
