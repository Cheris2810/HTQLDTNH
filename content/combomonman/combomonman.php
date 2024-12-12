<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css2/customer.css">
    <link rel="stylesheet" href="css2/menu.css">
    <title>Quản lý đặt tiệc</title>
    <style>
        /* Định dạng chung cho container của nhóm món ăn */
        .group {
            display: none; /* Ẩn tất cả các nhóm món ăn */
        }

        .group.active {
            display: block; /* Hiển thị nhóm món ăn được kích hoạt */
        }

        .group-buttons {
            margin-bottom: 20px;
            text-align: center;
            margin-left: -0.3rem;
        }

        .group-button {
            padding: 10px 20px;
            margin: 5px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: small;
            font-weight: bolder;

        }

        .group-button:hover {
            background-color: #0056b3;
        }

        /* Định dạng tiêu đề của nhóm món ăn */
        .group h3 {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        /* Định dạng danh sách các món ăn */
        .group-items {
            display: flex; /* Căn ngang */
            flex-wrap: wrap; /* Để các món tự động xuống dòng nếu không đủ chỗ */
            gap: 15px; /* Khoảng cách giữa các món */
            justify-content: center; /* Căn giữa các món */
            list-style: none;
            padding: 0;
            margin: 0;
        }

        /* Định dạng từng món ăn */
        

        .monan-item:hover {
            transform: translateY(-5px); /* Hiệu ứng hover */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Định dạng hình ảnh trong mỗi món ăn */
        .monan-item {
            display: flex; /* Sử dụng Flexbox */
            flex-direction: column; /* Hiển thị các phần tử theo cột: ảnh trên, tên dưới */
            justify-content: center; /* Căn giữa theo chiều dọc */
            align-items: center; /* Căn giữa theo chiều ngang */
            text-align: center; /* Căn giữa nội dung văn bản */
            width: 150px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .monan-item img {
            margin-bottom: 5px; /* Khoảng cách giữa ảnh và tên món */
            width: 4rem; /* Đảm bảo ảnh không vượt quá chiều rộng của phần tử cha */
            height: 3rem;
            border: 1px solid;
            margin-left: rem;
            border-radius: 2rem; /* Tạo góc bo cho ảnh */
            display: block; /* Đảm bảo ảnh là khối độc lập */
        }

        .monan-item span {
            font-size: 11px;
            font-weight: bold;
            color: #333;
            display: block; /* Căn giữa văn bản trong Flexbox */
        }


        .pagination {
            margin-top: 10px;
            text-align: center;
        }

        .pagination button {
            margin: 0 5px;
            padding: 5px 10px;
            cursor: pointer;
            border: 1px solid #ccc;
            border-radius: 3px;
            background-color: #f9f9f9;
        }

        .pagination button.active {
            background-color: #007bff;
            color: white;
        }


        /* Phần bên phải - danh sách món ăn đã chọn */
        .right {
            margin-top: 30px;
            padding: 10px;
/*            border: 1px solid #ccc;*/
/*            max-width: 300px;*/
        }

        .selected-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .selected-item img {
            margin-right: 10px;
        }

        body {
            font-family: Arial, sans-serif;
            margin-top: 3rem;
            background: white;
        }

        .container {
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        
        .right {
            width: 50%;
/*            padding: 20px;*/
            margin-left: 5rem;
            justify-content: center;
/*            justify-items: center;*/

        }

        .left {
            margin-right: 0rem;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        .btn {
            padding: 10px 20px;
            background:#0000FF;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #696969;
        }

        .selected-items {
            list-style-type: none;
            padding: 0;
        }

        .selected-items li {
            margin-bottom: 10px;
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }

        .selected-items img {
            margin-right: 10px;
            width: 50px;
            height: auto;
        }

        .search-bar {
            margin-bottom: 20px;
            padding: 10px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .pagination a,
        .pagination strong {
            margin: 0 5px;
            padding: 8px 12px;
            background:#0000FF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .pagination a:hover {
            background-color:#696969;
        }

        .pagination strong {
            background-color:#696969;
        }

        .pagination .arrow {
            font-weight: bold;
            font-size: 18px;
            color: #28a745;
            padding: 4px 8px;
        }

        .pagination .arrow:hover {
            color: #218838;
        }

        h1,
        h2 {
            color: #343a40;
        }

        /* Phong cách riêng cho bảng món ăn */
        .monan-table {
            background: white;
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
<script>
    function handlePageChange(page) {
        window.location.href = `?page=${page}`;
    }
</script>
<header style="margin-left:-0.5rem">
    <nav class="navbar">
        <ul class="nav">
           <li class="menu-item "><a href="../halls/halls.php" >Sơ đồ sảnh</a></li>
           <li class="menu-item active" style="border: 2px solid #007BFF">
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
    <div class="container">
        <div class="left" style="width:75rem">
            <form id="btmForm" method="POST" action="add_monan_to_btm.php">
                <select name="stt_btm" id="stt_btm" required>
                    <option value="">-- Chọn bàn tiệc mẫu --</option>
                    <?php
                        $conn = new mysqli("localhost", "root", "", "tochuctiec");
                        if ($conn->connect_error) {
                            die("Kết nối thất bại: " . $conn->connect_error);
                        }

                        // Lấy danh sách bàn tiệc mẫu
                        $btm_query = "SELECT stt_btm, ten_btm FROM bantiecmau";
                        $btm_result = $conn->query($btm_query);

                        if ($btm_result->num_rows > 0) {
                            while ($row = $btm_result->fetch_assoc()) {
                                echo '<option value="' . $row['stt_btm'] . '">' . $row['ten_btm'] . '</option>';
                            }
                        } else {
                            echo '<option value="">Không có bàn tiệc mẫu</option>';
                        }
                    ?>
                </select>

                <div id="menu" class="menu">
                <?php
                    // Lấy danh mục nhóm món ăn
                    $group_query = "SELECT DISTINCT n.ten_nm, n.ma_nm FROM nhommon n JOIN monan m ON n.ma_nm = m.ma_nhommon";
                    $group_result = $conn->query($group_query);

                    // Hiển thị các nhóm món
                    echo '<div id="group-buttons" class="group-buttons">';
                    while ($group_row = $group_result->fetch_assoc()) {
                        $group_name = $group_row['ten_nm'];
                        $group_id = $group_row['ma_nm'];
                        echo '<button class="group-button" data-group="' . htmlspecialchars($group_id) . '">' . htmlspecialchars($group_name) . '</button>';
                    }
                    echo '</div>';

                    // Hiển thị nhóm món
                    $group_result->data_seek(0);
                    while ($group_row = $group_result->fetch_assoc()) {
                        $group_id = $group_row['ma_nm']; // Mã nhóm món
                        echo '<div class="group" id="group-' . htmlspecialchars($group_id) . '">'; // ID cho từng nhóm món
                        echo '<h3>' . htmlspecialchars($group_row['ten_nm']) . '</h3>';
                        echo '<ul class="group-items">';

                        // Lấy các món ăn trong nhóm
                            $monan_query = "SELECT ma_ma, ten_ma, hinh_anh FROM monan WHERE ma_nhommon = '$group_id'";
                            $monan_result = $conn->query($monan_query);

                            if ($monan_result->num_rows > 0) {
                                $index = 0; // Để theo dõi số thứ tự món ăn
                                while ($row = $monan_result->fetch_assoc()) {
                                    $image_path = !empty($row['hinh_anh']) ? $row['hinh_anh'] : 'default.png';
                                    echo '<li class="monan-item" data-index="' . $index . '" data-id="' . $row['ma_ma'] . '">'; // Thêm data-id để lưu mã món
                                    echo '<img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($row['ten_ma']) . '" width="50">';
                                    echo '<span>' . htmlspecialchars($row['ten_ma']) . '</span>';
                                    echo '<input type="checkbox" name="monan[]" value="' . $row['ma_ma'] . '" class="select-item">'; // Checkbox để chọn món ăn
                                    echo '</li>';
                                    $index++;
                                }
                            } else {
                                echo '<li>Không có món ăn trong nhóm này</li>';
                            }

                                        echo '</ul>';
                                        echo '<div class="pagination"></div>'; // Vùng phân trang cho JS xử lý
                                        echo '</div>';
                                    }
                                ?>
                            </div>

                                <button type="submit" class="btn">Lưu món ăn đã chọn</button>
                </form>
            </div>


            <div class="right" style="width:55rem;"> 
                <div class="container" style=" display: inline-table;">
                   <?php
                        // Kết nối cơ sở dữ liệu
                        $conn = new mysqli("localhost", "root", "", "tochuctiec");
                        if ($conn->connect_error) {
                            die("Kết nối thất bại: " . $conn->connect_error);
                        }

                        // Xác định số bàn tiệc mẫu hiện tại
                        $stt_btm = isset($_GET['stt_btm']) ? (int)$_GET['stt_btm'] : 1;

                        // Lấy tổng số bàn tiệc mẫu
                        $total_query = "SELECT COUNT(DISTINCT stt_btm) as total_items FROM btm_mon";
                        $total_result = $conn->query($total_query);
                        if ($total_result && $total_result->num_rows > 0) {
                            $total_row = $total_result->fetch_assoc();
                            $total_items = (int)$total_row['total_items'];
                        } else {
                            $total_items = 0; // Mặc định là 0 nếu không có dữ liệu
                        }

                        // Lấy danh sách các món ăn cho bàn tiệc mẫu hiện tại
                        $btm_query = "
                            SELECT ma.ma_ma, ma.ten_ma, ma.hinh_anh
                            FROM btm_mon bm
                            LEFT JOIN monan ma ON bm.ma_ma = ma.ma_ma
                            WHERE bm.stt_btm = $stt_btm
                        ";
                        $btm_result = $conn->query($btm_query);

                        // Vùng hiển thị thông báo
                        echo '<div id="message" style="color: red; font-weight: bold;"></div>';

                        if ($btm_result->num_rows > 0) {
                            echo '<h3 style="color: #333; font-size: 24px;">Bàn Tiệc Mẫu ' . htmlspecialchars($stt_btm) . '</h3>';
                            echo '<table style="width: 100%; border-collapse: collapse; margin-top: 15px;">';
                            echo '<thead>';
                            echo '<tr style="background-color: #f2f2f2;">';
                            echo '<th style="border: 1px solid #ddd; padding: 8px;">Mã Món</th>';
                            echo '<th style="border: 1px solid #ddd; padding: 8px;">Tên Món</th>';
                            echo '<th style="border: 1px solid #ddd; padding: 8px;">Hình Ảnh</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';

                            while ($row = $btm_result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($row['ma_ma']) . '</td>';
                                echo '<td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($row['ten_ma']) . '</td>';
                                echo '<td style="border: 1px solid #ddd; padding: 8px;"><img src="' . htmlspecialchars($row['hinh_anh']) . '" alt="' . htmlspecialchars($row['ten_ma']) . '" width="50" style="border-radius: 5px;"></td>';
                                echo '</tr>';
                            }

                            echo '</tbody>';
                            echo '</table>';
                        } else {
                            echo '<p style="color: #555;">Không có món ăn cho bàn tiệc mẫu này.</p>';
                        }

                        // Hiển thị nút "Trở lại" và "Tiếp tục"
                        echo '<div class="navigation-buttons" style="margin-top: 20px;">';
                        if ($stt_btm > 1) {
                            echo '<a class="button" href="?stt_btm=' . ($stt_btm - 1) . '" style="padding: 10px 15px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px; margin-right: 10px;">Trở lại</a>';
                        }
                        if ($stt_btm < $total_items) {
                            echo '<a class="button" href="?stt_btm=' . ($stt_btm + 1) . '" style="padding: 10px 15px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">Tiếp tục</a>';
                        }
                        echo '</div>';

                        // Đóng kết nối
                        $conn->close();
                        ?>

                </div>
            </div>
        </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    const selectedItems = [];

    // Khi DOM được tải xong
    $(document).ready(function() {
        // Tương tác với các nút nhóm món ăn
        $('.group-button').on('click', function() {
            var groupId = $(this).data('group');
            $('#group-' + groupId).toggle(); // Hiển thị/ẩn nhóm món ăn
        });

        // Khi click vào món ăn
        $(".monan-item").on("click", function () {
            const checkbox = $(this).find(".select-item");
            const maMon = $(this).data("id");
            const tenMon = $(this).find("span").text();

            // Chuyển đổi trạng thái chọn/bỏ chọn
            if (selectedItems.some(item => item.value === maMon)) {
                selectedItems = selectedItems.filter(item => item.value !== maMon);
                checkbox.prop("checked", false);
                $(this).removeClass("selected");
            } else {
                selectedItems.push({ value: maMon, name: tenMon });
                checkbox.prop("checked", true);
                $(this).addClass("selected");
            }
        });

        // Gửi form với các món ăn đã chọn
        $('#btmForm').on('submit', function(e) {
            e.preventDefault(); // Ngừng gửi form mặc định

            // Lấy tất cả các món ăn đã chọn
            var selectedItemsValues = [];
            $('.select-item:checked').each(function() {
                selectedItemsValues.push($(this).val());
            });

            // Kiểm tra nếu có món ăn được chọn
            if (selectedItemsValues.length > 0) {
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: {
                        stt_btm: $('#stt_btm').val(),
                        monan: selectedItemsValues // Gửi danh sách mã món ăn đã chọn
                    },
                    success: function(response) {
                        // Thông báo thành công
                        $('#message').text('Món ăn đã được lưu thành công!');
                    },
                    error: function() {
                        // Thông báo lỗi
                        $('#message').text('Đã xảy ra lỗi. Vui lòng thử lại!');
                    }
                });
            } else {
                alert('Vui lòng chọn món ăn trước khi lưu!');
            }
        });

        // Gán sự kiện toggle ẩn/hiện món ăn trong từng nhóm
        $('.group h3').on("click", function () {
            const groupItems = $(this).parent().find('.group-items');
            groupItems.toggle(); // Hiển thị hoặc ẩn món ăn trong nhóm
        });

        // Phân trang cho món ăn trong các nhóm
        const groups = $('.group');
        groups.each(function() {
            const items = $(this).find('.monan-item');
            const pagination = $(this).find('.pagination');
            const itemsPerPage = 9;
            const totalPages = Math.ceil(items.length / itemsPerPage);
            const visiblePages = 5; // Số trang hiển thị tối đa

            let currentPage = 1;

            function showPage(page) {
                // Ẩn tất cả các món ăn
                items.hide();

                // Hiển thị món ăn của trang hiện tại
                const start = (page - 1) * itemsPerPage;
                const end = page * itemsPerPage;
                items.slice(start, end).show();

                // Cập nhật giao diện phân trang
                updatePagination(page);
            }

            function createPagination() {
                // Xóa nội dung cũ của phân trang
                pagination.empty();

                // Tạo nút "Trước"
                const prevButton = $('<button>').text('«').prop('disabled', currentPage === 1);
                prevButton.on('click', function() {
                    if (currentPage > 1) showPage(--currentPage);
                });
                pagination.append(prevButton);

                // Hiển thị các nút trang trong phạm vi cần thiết
                const startPage = Math.max(1, currentPage - Math.floor(visiblePages / 2));
                const endPage = Math.min(totalPages, startPage + visiblePages - 1);

                for (let i = startPage; i <= endPage; i++) {
                    const button = $('<button>').text(i).addClass(i === currentPage ? 'active' : '');
                    button.on('click', function() {
                        currentPage = i;
                        showPage(currentPage);
                    });
                    pagination.append(button);
                }

                // Tạo nút "Tiếp"
                const nextButton = $('<button>').text('»').prop('disabled', currentPage === totalPages);
                nextButton.on('click', function() {
                    if (currentPage < totalPages) showPage(++currentPage);
                });
                pagination.append(nextButton);
            }

            function updatePagination(page) {
                currentPage = page;
                createPagination();
            }

            // Hiển thị trang đầu tiên mặc định
            showPage(1);
        });
    });
</script>


</html>
