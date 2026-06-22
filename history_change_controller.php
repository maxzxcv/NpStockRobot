<?php
include('connection.php');
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['permission'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];
$permission = $_SESSION['permission'];

$result = mysqli_query($con, "SELECT * FROM Employee WHERE username = '$username'");
$user = mysqli_fetch_assoc($result);

// ดึงข้อมูลจาก Stock_Spare_history
$query = "SELECT * FROM Stock_Spare_history ORDER BY Date DESC";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Error: " . mysqli_error($con));
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการเปลี่ยนแปลงสินค้า</title>

    <style>
        .table-import {
            background-color: #d4edda;
        }

        .table-export {
            background-color: #f8d7da;
        }

        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('your-image.jpg');
            background-size: cover;
            background-position: center;
            z-index: -1;
            opacity: 0.5;
        }
    </style>
</head>

<body>

    <?php include 'dataTableLink_Rel.php'; ?>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2 class="text-center">ประวัติการเปลี่ยนแปลงสินค้า</h2>
        <a href="stock_controller.php" class="btn btn-secondary mt-3">ย้อนกลับ</a>
        <div class="table-container">
            <table id="historyTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>ชื่อคนเปลี่ยน</th>
                        <th>ชื่อสินค้า</th>
                        <th>Serial Number</th>
                        <th>สถานะ</th>
                        <th>สถานที่จัดเก็บ</th>
                        <th>วันที่</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['ItemName']); ?></td>
                            <td><?php echo htmlspecialchars($row['SerialNumber']); ?></td>
                            <?php
// ตรวจสอบว่าฟังก์ชันถูกประกาศไปแล้วหรือไม่
if (!function_exists('getStatusLabel')) {
    function getStatusLabel($status) {
        switch ($status) {
            case 'active':
                return 'ปกติ ✅';
            case 'wait_test':
                return 'กำลังเทส 🔵';
            case 'not_active':
                return 'เสีย ❌';
            case 'repairing':
                return 'กำลังซ่อม 🟤';
            default:
                return htmlspecialchars($status); // กรณีไม่มีในเงื่อนไข
        }
    }
}
?>

<td>
    <?php 
        $status = htmlspecialchars($row['status']);
        $statusParts = explode(" → ", $status); // แยกค่าเก่าและใหม่

        // ตรวจสอบว่ามีการเปลี่ยนแปลงค่าหรือไม่
        if (count($statusParts) == 2) {
            echo getStatusLabel($statusParts[0]) . " → " . getStatusLabel($statusParts[1]);
        } else {
            echo getStatusLabel($status); // กรณีที่ไม่มีการเปลี่ยนแปลง
        }
    ?>
</td>



                            <td><?php echo htmlspecialchars($row['whereItem']); ?></td>
                            <td><?php echo date("d-m-Y H:i", strtotime($row['Date'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#historyTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/th.json'
                },
                order: [
                    [5, 'desc']
                ] // เรียงลำดับจากวันที่ล่าสุด
            });
        });
    </script>

</body>

</html>