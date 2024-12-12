<?php
// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tochuctiec";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Nhận sopdt từ URL
$sopdt = isset($_GET['sopdt']) ? $_GET['sopdt'] : null;

if ($sopdt) {
    // Truy vấn dữ liệu từ phieudattiec và khachhang
    $sql = "SELECT 
                p.*, 
                k.hoten, k.sodt, k.so_nha, k.email, k.socccd, px.tenpx, qh.tenqh, tt.ten_tinhtp,b.TENBUOI
            FROM phieudattiec p
            LEFT JOIN khachhang k ON p.makh = k.makh
            LEFT JOIN phuong_xa px ON k.phuong_xa = px.mapx
            LEFT JOIN quan_huyen qh ON k.quan_huyen = qh.maqh
            LEFT JOIN tinh_thanhpho tt ON k.tinh_thanhpho = tt.matinhtp
            LEFT JOIN buoi b ON p.mabuoi = b.MABUOI
            WHERE p.sopdt = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $sopdt);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
} else {
    echo "Không tìm thấy mã phiếu!";
    exit;
}

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
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        

        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .invoice-header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .invoice-header p {
            margin: 5px 0;
            color: #555;
        }

        .invoice-details {
            margin-bottom: 20px;
        }

        .invoice-details th {
            text-align: left;
            padding-bottom: 10px;
            padding-right: 10px; /* Giảm khoảng cách sang bên phải */
        }

        .invoice-details td {
            padding-bottom: 10px;
            padding-left: 10px; /* Giảm khoảng cách sang bên trái */
        }

        .invoice-items th {
            text-align: left;
            padding-bottom: 10px;
            padding-right: 10px; /* Giảm khoảng cách sang bên phải */
        }

        .invoice-items td  {
            padding-bottom: 10px;
            padding-left: 10px; /* Giảm khoảng cách sang bên trái */
        }

        .total {
            font-weight: bold;
            margin-top: 20px;
        }

        .invoice-footer {
            text-align: center;
            margin-top: 20px;
        }

        .button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            margin: 5px;
            border: none;
            border-radius: 5px;
            background-color: #0000FF;
            color: white;
            width: 5rem;
        }

        .button:hover {
            background-color: #45a049;
        }
        
        .back-button {
            background-color: #0000FF; /* Red color for "Trở về" button */
            width: 5.5rem;
        }
        
        .back-button:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header-content" style="display: flex;justify-content: space-between; align-items: center;">
            <div>
            <?php if ($data): ?>
                <tr>
                    <th>Ngày đặt: </th>
                    <td><?= htmlspecialchars(date('d-m-Y', strtotime($data['ngaydat']))) ?></td>
                </tr>
            </div>
            <div>
                <!-- Logo -->
                <img src="img/logo.png" style="width: 105px; height:105px" alt="Restaurant Logo">
            </div>
        </div>
        <div class="invoice-header" style="margin-top: 1rem;">
            <p>Restaurant Cheris - Địa chỉ: Đường Nguyễn Văn Cừ, Phường An Hòa, TP. Cần Thơ</p>
            <p>Hotline: (+84) 9 4172 9910 | Email: nltnhu@gmail.com</p>
            <h2>Phiếu đặt tiệc</h2>
            <tr>
                <th>PDT</th>
                <td><?= htmlspecialchars($data['sopdt']) ?></td>
            </tr>
            <?php else: ?>
                <p>Không tìm thấy thông tin phiếu.</p>
            <?php endif; ?>
        </div>

        <div class="invoice-details">
            <?php if ($data): ?>
                <table>
                    <tr>
                        <th>Khách hàng</th>
                        <td><?= htmlspecialchars($data['hoten']) ?></td>
                    </tr>
                    <tr>
                        <th>Điện thoại</th>
                        <td><?= htmlspecialchars($data['sodt']) ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= htmlspecialchars($data['email']) ?></td>
                    </tr>
                    <tr>
                        <th>Số CCCD</th>
                        <td><?= htmlspecialchars($data['socccd']) ?></td>
                    </tr>
                    <tr>
                        <th>Địa chỉ</th>
                        <td>
                            <?= htmlspecialchars($data['so_nha']) ?>, 
                            <?= htmlspecialchars($data['tenpx']) ?>, 
                            <?= htmlspecialchars($data['tenqh']) ?>, 
                            <?= htmlspecialchars($data['ten_tinhtp']) ?>
                        </td>
                    </tr>
                </table>
        </div>
        <hr style="border: 1px dashed #ddd; margin: 20px 0;">

        <div class="invoice-items">
            <table>
                <tr>
                    <th>Sảnh tổ chức</th>
                    <td><?= htmlspecialchars($data['stt_sanh']) ?></td>
                </tr>
                <tr>
                    <th>Buổi tổ chức</th>
                    <td><?= htmlspecialchars($data['TENBUOI']) ?></td>
                </tr>
                <tr>
                    <th>Ngày tổ chức</th>
                    <td><?= htmlspecialchars(date('d-m-Y', strtotime($data['ngaytochuc']))) ?></td>
                </tr>
                <tr>
                    <th>Trạng thái</th>
                    <td><?= htmlspecialchars($data['trangthai']) ?></td>
                </tr>
            </table>

            <hr style="border: 1px dashed #ddd; margin: 20px 0;">
            <div class="invoice-items">
            <div class="invoice-section">
<?php
// Truy vấn bảng dattiec để lấy tất cả các stt_btd cho sopdt
$sqlDatTiec = "
    SELECT DISTINCT stt_btd 
    FROM dattiec 
    WHERE sopdt = ?";
$stmtDatTiec = $conn->prepare($sqlDatTiec);
$stmtDatTiec->bind_param('s', $sopdt); // 's' vì sopdt có thể là kiểu chuỗi
$stmtDatTiec->execute();
$resultDatTiec = $stmtDatTiec->get_result();

// Kiểm tra nếu có dữ liệu từ bảng dattiec
if ($resultDatTiec->num_rows > 0) {
    // Lặp qua các stt_btd
    while ($dataDatTiec = $resultDatTiec->fetch_assoc()) {
        // Truy vấn bảng bantiecdat để lấy tên bàn tiệc theo stt_btd
        $sqlBanTiecDat = "
            SELECT ten_btd 
            FROM bantiecdat 
            WHERE stt_btd = ?";
        $stmtBanTiecDat = $conn->prepare($sqlBanTiecDat);
        $stmtBanTiecDat->bind_param('i', $dataDatTiec['stt_btd']); // 'i' vì stt_btd là kiểu integer
        $stmtBanTiecDat->execute();
        $resultBanTiecDat = $stmtBanTiecDat->get_result();
        $dataBanTiecDat = $resultBanTiecDat->fetch_assoc();

        // Truy vấn bảng dattiec để lấy thông tin số bàn theo stt_btd
        $sqlDatTiecInfo = "
            SELECT soban_chinhthuc, soban_dutru, soban_sudung 
            FROM dattiec 
            WHERE sopdt = ? AND stt_btd = ?";
        $stmtDatTiecInfo = $conn->prepare($sqlDatTiecInfo);
        $stmtDatTiecInfo->bind_param('si', $sopdt, $dataDatTiec['stt_btd']);
        $stmtDatTiecInfo->execute();
        $resultDatTiecInfo = $stmtDatTiecInfo->get_result();

        ?>

        <div class="invoice-section">
            <!-- Hiển thị tên bàn tiệc -->
            <p style="margin-top:2rem"><strong> <?= htmlspecialchars($dataBanTiecDat['ten_btd']) ?>:</strong></p>

            

        <!-- Bảng 2: Hiển thị thông tin món ăn chỉ với tên món -->
        <table border="1" style=" width: 55%;
            margin: 0 auto; /* Căn giữa bảng theo chiều ngang */
            margin-top: 0px; /* Khoảng cách từ phía trên */
            border-collapse: collapse; 
            justify-items: center;">
            <tr>
                <th style="width: 19rem; text-align: center;">Tên món</th>
            </tr>
            <?php 
            // Truy vấn bảng datmon để lấy thông tin món ăn theo sopdt và stt_btd
            $sqlDatMon = "
                SELECT DISTINCT monan.ten_ma
                FROM datmon
                JOIN monan ON datmon.MA_MA = monan.ma_ma
                JOIN bantiec_mon ON bantiec_mon.MA_MA = datmon.MA_MA  -- Kết nối giữa bantiec_mon và datmon qua MA_MA
                JOIN bantiecdat ON bantiecdat.stt_btd = bantiec_mon.STT_BTD  -- Kết nối bantiec_mon và bantiecdat qua STT_BTD
                WHERE bantiecdat.stt_btd = ?";

            $stmtDatMon = $conn->prepare($sqlDatMon);
            $stmtDatMon->bind_param('i', $dataDatTiec['stt_btd']); // 'i' vì stt_btd là kiểu integer
            $stmtDatMon->execute();
            $resultDatMon = $stmtDatMon->get_result();

            if ($resultDatMon->num_rows > 0) {
                while ($row = $resultDatMon->fetch_assoc()) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row['ten_ma']) . "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='1'>Không có dữ liệu món ăn</td></tr>";
            }
            ?>
        </table>


       <!-- Bảng 3: Hiển thị thông tin đồ uống -->
<table border="1" style="width: 55%;
    margin: 0 auto; /* Căn giữa bảng theo chiều ngang */
    margin-top: 20px; /* Khoảng cách từ bảng trên */
    border-collapse: collapse; 
    justify-items: center;">
    <tr>
        <th style="width: 19rem; text-align: center;">Tên đồ uống</th>
    </tr>
    <?php
    // Truy vấn bảng để lấy thông tin ten_du
    $sqlDatDouong = "
        SELECT DISTINCT douong.ten_du
        FROM datdouong
        JOIN douong ON datdouong.ma_du = douong.ma_du -- Kết nối với bảng douong qua ma_du
        JOIN dattiec ON datdouong.sopdt = dattiec.sopdt -- Kết nối datdouong và dattiec qua sopdt
        JOIN bantiecdat ON dattiec.stt_btd = bantiecdat.stt_btd -- Kết nối dattiec và bantiecdat qua stt_btd
        WHERE bantiecdat.stt_btd = ?"; // Lọc theo stt_btd trong bảng bantiecdat

    $stmtDatDouong = $conn->prepare($sqlDatDouong);
    $stmtDatDouong->bind_param('i', $dataDatTiec['stt_btd']); // 'i' vì stt_btd là kiểu integer
    $stmtDatDouong->execute();
    $resultDatDouong = $stmtDatDouong->get_result();

    if ($resultDatDouong->num_rows > 0) {
        while ($row = $resultDatDouong->fetch_assoc()) {
            echo "<tr>
                <td>" . htmlspecialchars($row['ten_du']) . "</td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='1'>Không có dữ liệu đồ uống</td></tr>";
    }
    ?>
</table>




        <div class="invoice-details" style="text-align: left; margin-top: 20px;">
            <?php 
            if ($resultDatTiecInfo->num_rows > 0) {
                while ($row = $resultDatTiecInfo->fetch_assoc()) {
                    echo "
                    <div style='margin-bottom: 10px; margin-left: 1.7rem'>
                        <p style='position: relative;'>
                            <span style='position: absolute; left: -1.5rem;'>•</span>
                            <strong style='font-weight: bold;'>Số bàn chính thức:</strong> " . htmlspecialchars($row['soban_chinhthuc']) . "
                        </p>
                        <p style='position: relative;'>
                            <span style='position: absolute; left: -1.5rem;'>•</span>
                            <strong style='font-weight: bold;'>Số bàn dự trù:</strong> " . htmlspecialchars($row['soban_dutru'] ?? 'N/A') . "
                        </p>
                    </div>";
                }
            } else {
                echo "<p>Không có dữ liệu bàn tiệc</p>";
            }
            ?>
        </div>




        <!-- Tổng tiền -->
        <div style="text-align: left; margin-top: 20px; font-weight: bold;">
            <?php 
            // Truy vấn để lấy gia_btd từ bảng bantiecdat dựa trên stt_btd
            $sqlGiaBTD = "SELECT gia_btd FROM bantiecdat WHERE stt_btd = ?";
            $stmtGiaBTD = $conn->prepare($sqlGiaBTD);
            $stmtGiaBTD->bind_param('i', $dataDatTiec['stt_btd']); // 'i' vì stt_btd là kiểu integer
            $stmtGiaBTD->execute();
            $resultGiaBTD = $stmtGiaBTD->get_result();

            if ($resultGiaBTD->num_rows > 0) {
                $rowGiaBTD = $resultGiaBTD->fetch_assoc();
                $giaBTD = $rowGiaBTD['gia_btd'];
            } else {
                $giaBTD = 0; // Nếu không có dữ liệu, đặt giá trị mặc định là 0
            }

            // Truy vấn để lấy số bàn từ bảng dattiec dựa trên stt_btd
            $sqlSoBan = "SELECT soban_chinhthuc, soban_dutru FROM dattiec WHERE stt_btd = ?";
            $stmtSoBan = $conn->prepare($sqlSoBan);
            $stmtSoBan->bind_param('i', $dataDatTiec['stt_btd']); // 'i' vì stt_btd là kiểu integer
            $stmtSoBan->execute();
            $resultSoBan = $stmtSoBan->get_result();

            if ($resultSoBan->num_rows > 0) {
                $rowSoBan = $resultSoBan->fetch_assoc();
                $sobanChinhThuc = $rowSoBan['soban_chinhthuc'];
                $sobanDuTru = $rowSoBan['soban_dutru'];
            } else {
                $sobanChinhThuc = 0; // Giá trị mặc định nếu không có dữ liệu
                $sobanDuTru = 0;    // Giá trị mặc định nếu không có dữ liệu
            }

            // Tính tổng tiền
            $tongSoBan = $sobanChinhThuc + $sobanDuTru;
            $tongTien = $giaBTD * $tongSoBan;
            ?>
            <p style='position: relative; margin-left: 1.7rem;'>
                            <span style='position: absolute; left: -1.5rem;'>•</span>Tổng tiền: <?php echo number_format($tongTien, 0, ',', '.'); ?> VND</p>
        </div>



          
        </div>

    <?php
    } // Kết thúc vòng lặp while
} else {
    echo "Không có dữ liệu bàn tiệc cho SOPDT này.";
}
?>


  </div>
        <?php else: ?>
            <p>Không tìm thấy thông tin phiếu.</p>
        <?php endif; ?>

        <!-- Footer with buttons -->
        <!-- Phần Footer: Căn chỉnh "Nhân viên QL Đặt tiệc" và "Khách hàng" -->
        <div class="invoice-footer2" style="display:flex; justify-content: space-between; margin-bottom: 7rem; ">
            <p style="text-align: left; margin-left: 7rem;">Nhân viên QL Đặt tiệc</p>
            <p style="text-align: right; margin-right: 7rem;">Khách hàng</p>
        </div>
        <div class="invoice-footer">
            <button class="button" onclick="window.print();">In</button>
            <button class="button back-button" onclick="window.history.back();">Trở về</button>
        </div>

    </div>
</body>
</html>
<?php
// Đóng kết nối
$conn->close();
?>
