<?php
header('Content-Type: application/json');

// Kết nối database
$servername = "localhost";
$username = "root";
$password = "";
$database = "tochuctiec";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Kết nối database thất bại']));
}


// Lấy tháng từ request
$month = $_GET['month'] ?? '';
if (!$month) {
    echo json_encode(['error' => 'Tháng không hợp lệ']);
    exit;
}

// Xử lý tháng thành dạng `01-YYYY`
list($year, $month) = explode('-', $month);
$monthStart = "$year-$month-01";
$monthEnd = date("Y-m-t", strtotime($monthStart));

// Truy vấn thống kê
$sql = "
    SELECT COUNT(*) AS totalInvoices, SUM(sanh.tong_ban) AS totalHalls
    FROM phieudattiec
    JOIN sanh ON phieudattiec.stt_sanh = sanh.stt_sanh
    WHERE phieudattiec.trangthai = 'Hoàn chỉnh'
      AND STR_TO_DATE(phieudattiec.ngaydat, '%d-%m-%Y') BETWEEN ? AND ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $monthStart, $monthEnd);
$stmt->execute();
$result = $stmt->get_result();
$statistics = $result->fetch_assoc();

// Truy vấn dữ liệu cho biểu đồ
$sqlChart = "
    SELECT sanh.ten_sanh, COUNT(*) AS count
    FROM phieudattiec
    JOIN sanh ON phieudattiec.stt_sanh = sanh.stt_sanh
    WHERE phieudattiec.trangthai = 'Hoàn chỉnh'
      AND STR_TO_DATE(phieudattiec.ngaydat, '%d-%m-%Y') BETWEEN ? AND ?
    GROUP BY sanh.ten_sanh
";

$stmtChart = $conn->prepare($sqlChart);
$stmtChart->bind_param('ss', $monthStart, $monthEnd);
$stmtChart->execute();
$resultChart = $stmtChart->get_result();

$hallData = [];
while ($row = $resultChart->fetch_assoc()) {
    $hallData[] = [
        'name' => $row['ten_sanh'],
        'count' => $row['count'],
    ];
}

// Kết quả
echo json_encode([
    'totalInvoices' => $statistics['totalInvoices'] ?? 0,
    'totalHalls' => $statistics['totalHalls'] ?? 0,
    'hallData' => $hallData,
]);

$conn->close();
