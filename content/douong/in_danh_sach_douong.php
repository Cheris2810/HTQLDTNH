<?php
// Kết nối cơ sở dữ liệu
$SERVER = "localhost";
$user = "root";
$pass = "";
$database = "tochuctiec";

$conn = new mysqli($SERVER, $user, $pass, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lọc theo mã đơn vị tính (filter_dvt)
$filter_dvt = isset($_GET['filter_dvt']) ? (int)$_GET['filter_dvt'] : '';

// Lấy tên đơn vị tính (tendvt) nếu có filter_dvt
$tendvt = '';
if ($filter_dvt !== '') {
    $dvt_sql = "SELECT tendvt FROM donvitinh WHERE madvt = $filter_dvt";
    $dvt_result = $conn->query($dvt_sql);
    
    if ($dvt_result->num_rows > 0) {
        $dvt_row = $dvt_result->fetch_assoc();
        $tendvt = $dvt_row['tendvt'];
    }
}

// Điều kiện lọc theo mã đơn vị tính (madvt)
$filter_condition = '';
if ($filter_dvt !== '') {
    $filter_condition = "WHERE giadouong.madvt = $filter_dvt";
}

// Truy vấn lấy dữ liệu từ bảng douong kết hợp với giadouong
$sql = "
    SELECT douong.ma_du, douong.ten_du, douong.hinh_anh_du, giadouong.giadouong
    FROM douong
    LEFT JOIN giadouong ON douong.ma_du = giadouong.ma_du
    $filter_condition
";

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
    <title>In danh sách đồ uống</title>
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
        .image-column img {
            max-width: 50px;
            height: auto;
            display: block;
            margin-left: 4.5rem;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div style="display: flex; justify-content: center;">
            <div>
                <h1>Danh sách đồ uống: <?php 
                    if ($filter_dvt !== '') {
                        echo   htmlspecialchars($tendvt);
                    } else {
                        echo ' - Tất cả';
                    }
                    ?></h1>
            </div>
            <div style="margin-left:10rem; margin-top: 1rem;">
                <button class="btn-print" onclick="window.print()">In danh sách</button>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th class="name-column">Tên đồ uống</th>
                    <th class="image-column">Hình ảnh</th>
                    <th class="giadu-column">Giá đồ uống</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stt = 1; // Khởi tạo số thứ tự
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>
                                <td>' . $stt++ . '</td>
                                <td class="name-column">' . htmlspecialchars($row['ten_du']) . '</td>
                                <td class="image-column"><img src="' . htmlspecialchars($row['hinh_anh_du']) . '" alt="Hình ảnh đồ uống"></td>
                                <td>' . number_format($row['giadouong'], 0, ',', '.') . ' VND</td>
                            </tr>';
                    }
                } else {
                    echo '<tr><td colspan="4">Không có đồ uống nào.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
