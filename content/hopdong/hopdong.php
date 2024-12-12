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
// Lấy số hợp đồng cao nhất hiện tại trong bảng hợp đồng
$result = $conn->query("SELECT MAX(sohd) AS max_sohd FROM hopdong");

if ($result) {
    $row = $result->fetch_assoc();
    // Nếu bảng hopdong trống (không có hợp đồng nào), đặt số hợp đồng đầu tiên là 1
    $next_sohd = $row['max_sohd'] ? $row['max_sohd'] + 1 : 1;
} else {
    // Nếu có lỗi khi truy vấn, thông báo và dừng mã
    echo "Lỗi truy vấn: " . $conn->error;
    exit;
}

// Kiểm tra giá trị của sopdt
$sopdt = isset($_POST['sopdt']) ? $_POST['sopdt'] : '';  // Lấy giá trị sopdt từ POST nếu có

if (empty($sopdt)) {
    echo "Không có mã số hợp đồng (sopdt).";
    exit;  // Nếu sopdt không có giá trị, dừng mã
}


// Truy vấn để tính tổng số bàn
$query = "SELECT (SUM(CAST(soban_dutru AS INT)) + SUM(CAST(soban_sudung AS INT))) AS total_ban FROM dattiec WHERE sopdt = '$sopdt'";
$result = $conn->query($query);

// Kiểm tra và in ra dữ liệu trả về
if ($result) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // In ra dữ liệu để kiểm tra
        echo '<pre>';
        print_r($row); // Xem cấu trúc dữ liệu trả về
        echo '</pre>';

        echo $row['total_ban'];  // Trả về tổng số bàn
    } else {
        echo "0";  // Nếu không tìm thấy, trả về 0
    }
} else {
    echo "Lỗi truy vấn: " . $conn->error;
}

// Kiểm tra nếu có dữ liệu từ form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sohd = $_POST['sohd'];
    $sopdt = $_POST['sopdt']; // Lấy sopdt từ form
    $manv = $_POST['manv'];
    $tenkh = $_POST['tenkh'];
    $diachikh = $_POST['diachikh'];
    $dienthoaikh = $_POST['dienthoaikh'];
    $diachinh = $_POST['diachinh'];
    $dienthoainh = $_POST['dienthoainh'];
    $tongsoban = $_POST['tongsoban'];
    $thucdon = $_POST['thucdon'];
    $tongtien = $_POST['tongtien'];
    $ngaylap = $_POST['ngaylap'];

    // Lấy số hợp đồng cao nhất hiện tại trong bảng hợp đồng
    $result = $conn->query("SELECT MAX(sohd) AS max_sohd FROM hopdong");
    $row = $result->fetch_assoc();

    // Tăng số hợp đồng lên 1
    $next_sohd = $row['max_sohd'] + 1;

    // Truy vấn để tính tổng số bàn
$query = "SELECT (SUM(CAST(soban_dutru AS INT)) + SUM(CAST(soban_sudung AS INT))) AS total_ban FROM dattiec WHERE sopdt = '$sopdt'";
$result = $conn->query($query);

// Kiểm tra và in ra dữ liệu trả về
if ($result) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // In ra dữ liệu để kiểm tra
        echo '<pre>';
        print_r($row); // Xem cấu trúc dữ liệu trả về
        echo '</pre>';

        echo $row['total_ban'];  // Trả về tổng số bàn
    } else {
        echo "0";  // Nếu không tìm thấy, trả về 0
    }
} else {
    echo "Lỗi truy vấn: " . $conn->error;
}

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO hopdong (sohd, sopdt, manv, tenkh, diachikh, dienthoaikh, diachinh, dienthoainh, tongsoban, thucdon, tongtien, ngaylap) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssssssdsd", $sohd, $sopdt, $manv, $tenkh, $diachikh, $dienthoaikh, $diachinh, $dienthoainh, $tongsoban, $thucdon, $tongtien, $ngaylap);
    
    if ($stmt->execute()) {
        echo "Hợp đồng đã được lưu.";
    } else {
        echo "Lỗi khi lưu hợp đồng: " . $conn->error;
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đặt tiệc</title>
    <link rel="stylesheet" href="css2/menu.css">       
    
</head>
<header>
    <nav class="navbar">
        <ul class="nav">
           <li class="menu-item"><a href="../halls/halls.php">Sơ đồ sảnh</a></li>
           <li class="menu-item active">
             <a style="width: 7.1rem;" href="#">Thực đơn</a>
             <ul style="width: 9.5rem;" class="sub-menu">
                <li><a style=" width: 8rem;margin-left: -2rem;" href="../monman/monman.php">Món mặn</a></li>
                <li><a style=" width: 8rem;margin-left: -2rem;" href="../monchay/monchay.php">Món chay</a></li>
                <li><a style=" width: 8rem;margin-left: -2rem;" href="../douong/douong.php">Đồ uống</a></li>
                <li><a style=" width: 8rem;margin-left: -2rem;" href="../combomonman/combomonman.php">Bàn tiệc mẫu</a></li>
                <li><a style=" width: 9rem;margin-left: -2rem;" href="../bandattiec/formbandattiec.php">Bàn đặt tiệc</a></li>
             </ul>
           </li>
           <li class="menu-item">
             <a style="width: 8.5rem;" href="#">Quản lý thông tin</a>
             <ul class="sub-menu ">
                <li class="menu-item"><a style="margin-left: -1.5rem;" href="../customer/customer.php">Khách hàng</a></li>
                <li class="menu-item"><a style="margin-left: -1.5rem;" href="#">Nhân viên</a></li>
             </ul>
           </li>
           <li class="menu-item">
             <a style="width: 7.5rem;" href="#">Quản lý tài liệu</a>
             <ul style="width: 9.9rem;" class="sub-menu">
                <li><a style=" width: 9rem;margin-left: -2rem;" href="../phieudattiec/dsphieudattiec.php">Phiếu đặt tiệc</a></li>
                <li><a style=" width: 9rem;margin-left: -2rem;" href="../hopdong/hopdong.php">Hợp đồng</a></li>
                <li><a style=" width: 9rem;margin-left: -2rem;" href="../thongke/thongke.php">Thống kê/Báo cáo</a></li>
             </ul>
           </li>
           <li class="menu-item"><a href="#">Đăng xuất</a></li>
        </ul>
    </nav>
</header>
<body style="margin-top: 2rem;">

<form action="submit_form.php" method="post" >
    <div class="form-container">
    <!-- Left Column -->
    <div class="left-column">
        <label for="sohd">Số hợp đồng:</label>
        <input type="text" id="sohd" name="sohd" value="<?php echo $next_sohd; ?>" readonly><br><br>
        <label for="sopdt">Số Phiếu Đặt Tiệc:</label>
    <select id="sopdt" name="sopdt" onchange="loadTongSoBan(); loadThucDon();">
        <?php
        // Kết nối đến cơ sở dữ liệu
        $conn = new mysqli('localhost', 'root', '', 'tochuctiec');

        // Kiểm tra kết nối
        if ($conn->connect_error) {
            die("Kết nối không thành công: " . $conn->connect_error);
        }

        // Truy vấn các sopdt có trạng thái 'Hoàn chỉnh'
        $query = "SELECT sopdt FROM phieudattiec WHERE trangthai = 'Hoàn chỉnh'";
        $result = $conn->query($query);

        // Kiểm tra kết quả và hiển thị các lựa chọn trong select
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['sopdt']}'>{$row['sopdt']}</option>";
            }
        } else {
            echo "<option value=''>Không có phiếu đặt tiệc nào hoàn chỉnh</option>";
        }

        // Đóng kết nối
        $conn->close();
        ?>
    </select><br><br>
        <label for="manv">Mã nhân viên:</label>
        <input type="number" id="manv" name="manv" required><br>

        <label for="tenkh">Họ tên khách hàng:</label>
        <input type="text" id="tenkh" name="tenkh" required><br><br>

        <div>
                <label>Tỉnh/Thành phố:</label>
                <select id="tinh_thanhpho" name="tinh_thanhpho" style="padding: 10px; border: 1px solid #ccc;border-radius: 4px; height:2.43rem" required>
                    <option value="" >-- Chọn Tỉnh/Thành phố --</option>
                    <!-- Tỉnh/Thành phố sẽ được load từ AJAX -->
                </select>
            </div>
            <div>
                <label>Quận/Huyện:</label>
                <select id="quan_huyen" name="quan_huyen" style="padding: 10px; border: 1px solid #ccc;border-radius: 4px; height:2.43rem" required>
                    <option value="" >-- Chọn Quận/Huyện --</option>
                </select>
            </div>
            <div>
                <label>Phường/Xã:</label>
                <select id="phuong_xa" name="phuong_xa" style="padding: 10px; border: 1px solid #ccc;border-radius: 4px; height:2.43rem" required>
                    <option value="" >-- Chọn Phường/Xã --</option>
                </select>
            </div>
    </div>

    <!-- Right Column -->
    <div class="right-column">

        <label for="dienthoaikh">Điện thoại khách hàng:</label>
        <input type="text" id="dienthoaikh" name="dienthoaikh" required><br><br>
        <label for="diachinh">Địa chỉ nhà hàng:</label>
        <input type="text" id="diachinh" name="diachinh" value="Đường Nguyễn Văn Cừ, Phường An Hòa, TP. Cần Thơ" readonly><br><br>

        <label for="dienthoainh">Điện thoại nhà hàng:</label>
        <input type="text" id="dienthoainh" name="dienthoainh" value="(+84) 9 4172 9910" readonly><br><br>

        <label for="tongsoban">Tổng số bàn:</label>
        <input type="number" id="tongsoban" name="tongsoban" readonly><br><br>

        <label for="thucdon">Thực Đơn:</label>
        <select id="thucdon" name="thucdon">
            <!-- Options will be loaded dynamically -->
        </select><br><br>

        <label for="tongtien">Tổng Tiền:</label>
        <input type="text" id="tongtien" name="tongtien" readonly><br><br>

        <label for="ngaylap">Ngày Lập:</label>
        <input type="date" id="ngaylap" name="ngaylap" required><br><br>
    </div>
</div>


    <button type="submit">Lưu Hợp Đồng</button>
</form>

<script>
function loadThucDon() {
    let sopdt = document.getElementById('sopdt').value;
    fetch(`get_thucdon.php?sopdt=${sopdt}`)
        .then(response => response.json())
        .then(data => {
            let thucdonSelect = document.getElementById('thucdon');
            thucdonSelect.innerHTML = ''; // Clear previous options
            data.forEach(item => {
                let option = document.createElement('option');
                option.value = item.ten_btd;
                option.innerText = `${item.ten_btd} - Giá: ${item.gia_btd}`;
                thucdonSelect.appendChild(option);
            });
        })
        .catch(error => console.error("Error loading thucdon:", error));
}

function calculateTotal() {
    let total = 0;
    let thucdonSelect = document.getElementById('thucdon');
    let selectedItems = thucdonSelect.selectedOptions;
    Array.from(selectedItems).forEach(item => {
        total += parseFloat(item.getAttribute('data-price'));
    });
    document.getElementById('tongtien').value = total.toFixed(2);
}
// Hàm gọi API để lấy tổng số bàn khi sopdt thay đổi
    function loadTongSoBan() {
    var sopdt = document.getElementById('sopdt').value;

    // Gửi yêu cầu AJAX đến file PHP để tính tổng số bàn
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_total_ban.php?sopdt=' + sopdt, true);
    xhr.onload = function() {
        xhr.onload = function() {
            if (xhr.status == 200) {
                console.log('Total Ban:', xhr.responseText);  // In ra kết quả trong console
                document.getElementById('tongsoban').value = xhr.responseText;
            } else {
                console.error('Error fetching data');
            }
        };

    xhr.send();
}
</script>
</body>
</html>
