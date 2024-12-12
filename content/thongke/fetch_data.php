<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tochuctiec";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = array();

// Thống kê phiếu
$sql_phieudattiec = "SELECT trangthai, COUNT(*) AS so_luong 
                     FROM phieudattiec 
                     GROUP BY trangthai";
$result_phieudattiec = $conn->query($sql_phieudattiec);
if ($result_phieudattiec) {
    $phieudattiec_data = array();
    while ($row = $result_phieudattiec->fetch_assoc()) {
        $phieudattiec_data[] = $row;
    }
    $response['phieudattiec'] = $phieudattiec_data;
}

// Thống kê doanh thu
$sql_doanhthu = "SELECT MONTH(STR_TO_DATE(phieudattiec.ngaydat, '%d-%m-%Y')) AS thang, 
                        (SUM(dattiec.soban_sudung) / COUNT(DISTINCT phieudattiec.sopdt)) * 100 AS phan_tram_soban
                 FROM phieudattiec 
                 INNER JOIN dattiec ON phieudattiec.sopdt = dattiec.sopdt
                 GROUP BY MONTH(STR_TO_DATE(phieudattiec.ngaydat, '%d-%m-%Y'))";
$result_doanhthu = $conn->query($sql_doanhthu);
if ($result_doanhthu) {
    $doanhthu_data = array();
    while ($row = $result_doanhthu->fetch_assoc()) {
        $doanhthu_data[] = $row;
    }
    $response['doanhthu'] = $doanhthu_data;
}

// Thống kê món ăn
$sql_monan = "SELECT datmon.ma_ma, monan.ten_ma, SUM(datmon.sl_sudung) AS sl_sudung_thang 
              FROM datmon 
              INNER JOIN monan ON datmon.ma_ma = monan.ma_ma
              GROUP BY datmon.ma_ma";
$result_monan = $conn->query($sql_monan);
if ($result_monan) {
    $monan_data = array();
    while ($row = $result_monan->fetch_assoc()) {
        $monan_data[] = $row;
    }
    $response['monan'] = $monan_data;
}

// Trả về JSON
echo json_encode($response);
$conn->close();
?>

