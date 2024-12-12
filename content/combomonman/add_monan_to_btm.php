<?php
// Kết nối cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "", "tochuctiec");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra xem có dữ liệu được gửi từ form hay không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $stt_btm = $_POST['stt_btm'];
    $ma_ma_array = isset($_POST['ma_ma']) ? $_POST['ma_ma'] : [];

    // Kiểm tra xem có món ăn nào được chọn hay không
    if (!empty($ma_ma_array)) {
        // Chuẩn bị câu lệnh SQL kiểm tra trước khi thêm món ăn vào btm_mon
        $check_query = "SELECT COUNT(*) FROM btm_mon WHERE stt_btm = ? AND ma_ma = ?";
        $check_stmt = $conn->prepare($check_query);

        foreach ($ma_ma_array as $ma_ma) {
            // Kiểm tra xem cặp stt_btm và ma_ma đã tồn tại chưa
            $check_stmt->bind_param("is", $stt_btm, $ma_ma);
            $check_stmt->execute();
            $check_stmt->bind_result($count);
            $check_stmt->fetch();

            // Đảm bảo rằng chúng ta đóng kết quả trước khi thực hiện câu lệnh tiếp theo
            $check_stmt->free_result(); // Giải phóng kết quả trước khi tiếp tục

            // Nếu chưa tồn tại, thêm vào bảng btm_mon
            if ($count == 0) {
                $stmt = $conn->prepare("INSERT INTO btm_mon (stt_btm, ma_ma) VALUES (?, ?)");
                $stmt->bind_param("is", $stt_btm, $ma_ma);
                $stmt->execute();
                $stmt->close(); // Đóng truy vấn ngay sau khi thực hiện
            }
        }

        $check_stmt->close(); // Đóng câu lệnh kiểm tra
        echo "Đã thêm món ăn thành công vào bàn tiệc mẫu!";
    } else {
        echo "Bạn chưa chọn món ăn nào.";
    }

    // Lưu món ăn vào bảng btm_mon từ form thứ hai
    $monan = isset($_POST['monan']) ? $_POST['monan'] : []; // Mảng chứa các mã món ăn đã chọn
    if (!empty($monan)) {
        foreach ($monan as $ma_ma) {
            // Thực hiện câu lệnh chèn vào bảng btm_mon
            $stmt = $conn->prepare("INSERT INTO btm_mon (stt_btm, ma_ma) VALUES (?, ?)");
            $stmt->bind_param("ii", $stt_btm, $ma_ma);
            $stmt->execute();
        }
        echo 'Món ăn đã được lưu thành công!';
    } else {
        echo 'Không có món ăn nào được chọn.';
    }
}

// Lấy danh sách bàn tiệc mẫu và các món ăn tương ứng
$btm_query = "
    SELECT btm.stt_btm, btm.ten_btm, ma.ma_ma, ma.ten_ma, ma.hinh_anh
    FROM bantiecmau btm
    LEFT JOIN btm_mon bm ON btm.stt_btm = bm.stt_btm
    LEFT JOIN monan ma ON bm.ma_ma = ma.ma_ma
    ORDER BY btm.stt_btm, ma.ten_ma
";

$btm_result = $conn->query($btm_query);
$ban_tiec_mau = [];

if ($btm_result->num_rows > 0) {
    while ($row = $btm_result->fetch_assoc()) {
        // Nhóm các món ăn theo bàn tiệc mẫu
        $ban_tiec_mau[$row['stt_btm']]['ten_btm'] = $row['ten_btm'];
        $ban_tiec_mau[$row['stt_btm']]['monan'][] = [
            'ma_ma' => $row['ma_ma'],
            'ten_ma' => $row['ten_ma'],
            'hinh_anh' => $row['hinh_anh'] ? $row['hinh_anh'] : 'default.png'
        ];
    }
} else {
    echo "Không có dữ liệu bàn tiệc mẫu.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Bàn Tiệc Mẫu</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h1 {
            text-align: center;
        }

        .ban-tiec {
            margin-bottom: 40px;
        }

        .mon-an-img {
            width: 50px;
            height: auto;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 12px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .pagination a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <h1>Danh sách Bàn Tiệc Mẫu</h1>

    <?php if (!empty($ban_tiec_mau)): ?>
        <?php foreach ($ban_tiec_mau as $stt_btm => $btm): ?>
            <div class="ban-tiec">
                <h2>Bàn Tiệc Mẫu: <?= htmlspecialchars($btm['ten_btm']) ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th>Mã Món</th>
                            <th>Tên Món</th>
                            <th>Hình Ảnh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($btm['monan'])): ?>
                            <?php foreach ($btm['monan'] as $monan): ?>
                                <tr>
                                    <td><?= htmlspecialchars($monan['ma_ma']) ?></td>
                                    <td><?= htmlspecialchars($monan['ten_ma']) ?></td>
                                    <td><img class="mon-an-img" src="<?= htmlspecialchars($monan['hinh_anh']) ?>" alt="<?= htmlspecialchars($monan['ten_ma']) ?>"></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">Không có món ăn nào trong bàn tiệc này.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Không có dữ liệu bàn tiệc mẫu.</p>
    <?php endif; ?>

</body>
</html>
