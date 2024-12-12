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

// Lấy giá trị filter_nhommon từ URL
$filter_nhommon = isset($_GET['filter_nhommon']) ? $_GET['filter_nhommon'] : '';
$ten_loai_mon = ''; // Biến để chứa tên loại món

if (!empty($filter_nhommon)) {
    // Truy vấn cơ sở dữ liệu để lấy tên loại món
    $sql_ten_loai_mon = "SELECT ten_lm FROM loaimon WHERE ma_lm = '$filter_nhommon'";
    $result_ten_loai_mon = $conn->query($sql_ten_loai_mon);

    if ($result_ten_loai_mon->num_rows > 0) {
        $row_ten_loai_mon = $result_ten_loai_mon->fetch_assoc();
        $ten_loai_mon = $row_ten_loai_mon['ten_lm']; // Lấy tên loại món
    }
}

// Điều kiện lọc loại món
$filter_condition = '';
if (!empty($filter_nhommon)) {
    $filter_condition = " AND loaimon.ma_lm = '$filter_nhommon'";
}

// Truy vấn lấy dữ liệu món ăn đã lọc, bao gồm giá món từ bảng giabanle
$sql = "SELECT monan.ma_ma, monan.ten_ma, monan.hinh_anh, loaimon.ten_lm, giabanle.giabanle 
        FROM monan 
        LEFT JOIN loaimon ON monan.ma_loaimon = loaimon.ma_lm 
        LEFT JOIN giabanle ON monan.ma_ma = giabanle.ma_ma 
        WHERE 1 $filter_condition";

$result = $conn->query($sql);

if (!$result) {
    die("Truy vấn lỗi: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đặt tiệc</title>
    <style>
        body {
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            margin: 0 auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: center;
            border: none;
            font-size: large;
        }
        th {
            
        }
        td img {
            width: 45px;
            height: auto;
            object-fit: cover;
            margin: 0 auto;
            display: block;
        }
        h1 {
            color: black;
            text-align: center;
            font-size: x-large;
        }
        .btn-print {
            background: #007BFF;
            color: white;
            border: 1px #87CEFF solid;
            height: 1.7rem;
            border-radius: 0.5rem;
            cursor: pointer;
        }
        .btn-print:hover {
            background: #0056b3;
        }
        .name-column {
            text-align: left;
        }
        .image-column {
            transform: translateX(-2.5rem); /* Di chuyển sang trái 2.5rem */
        }

        /* Đảm bảo hình ảnh hiển thị đẹp */
        .image-column img {
            max-width: 100px; /* Giới hạn chiều rộng ảnh */
            height: auto;
            display: block;
        }
        .giamon-colum {
            transform: translateX(-2.5rem); /* Di chuyển sang trái 2.5rem */
        }
</style>

</head>
<body>
    <div class="invoice-container">
        <div style="display: flex; justify-content: center;">
            <div>
                <h1>Danh sách: <?php echo htmlspecialchars($ten_loai_mon); ?></h1>
            </div>
            <div style="margin-left:10rem; margin-top: 1rem;">
                <button class="btn-print" onclick="window.print()">In danh sách</button>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th class="name-column">Tên món</th>
                    <th class="image-column">Hình ảnh</th>
                    <th class="giamon-column">Giá món</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stt = 1; // Khởi tạo số thứ tự
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>
                                <td>' . $stt++ . '</td> <!-- Số thứ tự tự động tăng -->
                                <td class="name-column">' . $row['ten_ma'] . '</td>
                                <td class="image-column"><img src="' . $row['hinh_anh'] . '" alt="Hình ảnh món ăn"></td>
                                <td class="giamon-column">' . number_format($row['giabanle'], 0, ',', '.') . ' VND</td> <!-- Hiển thị giá món định dạng VND -->
                            </tr>';
                    }
                } else {
                    echo '<tr><td colspan="4">Không có món ăn nào.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
