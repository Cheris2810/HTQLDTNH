

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đặt tiệc</title>
    <link rel="stylesheet" href="css2/menu.css">  
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style type="text/css">
        <style>
        body {
            margin-bottom: 8.7rem;
            margin-top:10rem;
            margin-left: 0rem;
        }
        th{
            background: #007BFF;
            color: white;
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
        h1, h2 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .container {
    width: 90%;
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

h1, h2 {
    text-align: center;
    color: #333;
}

.filter {
    display: flex;
    justify-content: center;
    margin: 20px 0;
    gap: 10px;
}

#statistics {
    text-align: center;
    margin: 20px 0;
}

#chart {
    margin-top: 30px;
}


    </style>
</head>
<header style="margin-left:-0.5rem">
    <nav class="navbar">
        <ul class="nav">
           <li class="menu-item"><a href="../halls/halls.php" >Sơ đồ sảnh</a></li>
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
           <li class="menu-item  active" style="border: 2px solid #007BFF">
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
    <div class="container">
        <h1>Thống kê doanh thu dự kiến</h1>
        <div class="filter">
            <label for="monthPicker">Chọn tháng:</label>
            <input type="month" id="monthPicker" />
            <button style="background:#007BFF; color: white; width: 7rem; height: 2rem; border-radius: 0.5rem; font-weight: bold; " id="filterButton">Xem thống kê</button>
        </div>
        <div id="statistics">
            <h2>Thống kê chi tiết</h2>
            <p>Tổng số phiếu hoàn chỉnh: <span id="totalInvoices">0</span></p>
            <p>Tổng số sảnh: <span id="totalHalls">0</span></p>
        </div>
        <div id="chart">
            <h2>Sơ đồ doanh thu theo sảnh</h2>
            <canvas id="revenueChart" width="400" height="200"></canvas>
        </div>
    </div>

    <script>
      document.getElementById("filterButton").addEventListener("click", () => {
    const month = document.getElementById("monthPicker").value;
    if (!month) {
        alert("Vui lòng chọn tháng!");
        return;
    }

    // Gửi yêu cầu đến server để lấy dữ liệu thống kê
    fetch(`get_statistics.php?month=${month}`)
        .then((response) => {
            if (!response.ok) {
                throw new Error(`Lỗi từ máy chủ: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            console.log(data);  // Kiểm tra dữ liệu trả về
            document.getElementById("totalInvoices").innerText = data.totalInvoices || 0;
            document.getElementById("totalHalls").innerText = data.totalHalls || 0;
            updateChart(data.hallData || []);
        })
        .catch((error) => {
            console.error("Lỗi khi lấy dữ liệu thống kê:", error);
        });
});

// Cập nhật biểu đồ doanh thu
function updateChart(hallData) {
    const ctx = document.getElementById("revenueChart").getContext("2d");

    // Nếu không có dữ liệu, không vẽ biểu đồ
    if (hallData.length === 0) {
        alert("Không có dữ liệu để hiển thị!");
        return;
    }

    const labels = hallData.map((hall) => hall.name);
    const values = hallData.map((hall) => hall.count);

    // Kiểm tra nếu biểu đồ cũ tồn tại thì hủy nó
    if (window.revenueChart instanceof Chart) {
        window.revenueChart.destroy();
    }

    // Tạo mới biểu đồ
    window.revenueChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Số lượng phiếu",
                    data: values,
                    backgroundColor: "rgba(75, 192, 192, 0.5)",
                    borderColor: "rgba(75, 192, 192, 1)",
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true },
                tooltip: { enabled: true },
            },
            scales: {
                x: { beginAtZero: true },
                y: { beginAtZero: true },
            },
        },
    });
}

    </script>
</body>
</html>