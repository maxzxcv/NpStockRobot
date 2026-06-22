<?php
include('connection.php'); // เชื่อมต่อกับฐานข้อมูล
session_start(); // ใช้ session สำหรับตรวจสอบผู้ใช้ที่ล็อกอิน

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['username']) || !isset($_SESSION['permission'])) {
    header("Location: index.php"); // หากไม่ได้ล็อกอิน ให้ไปหน้า login
    exit;
}

// รับค่าจาก session
$username = $_SESSION['username'];
$permission = $_SESSION['permission'];
$result = mysqli_query($con, "SELECT * FROM Employee WHERE username = '$username'");
$user = mysqli_fetch_assoc($result);
// เปิดการแสดงข้อผิดพลาด
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// คำสั่ง SQL ดึงข้อมูลอุปกรณ์
$query = "SELECT id, 
                 NumberItem AS ลำดับ, 
                 ItemName AS ชื่ออุปกรณ์, 
                 Amount AS จำนวน, 
                 CASE 
                     WHEN status IS NULL OR status = '' THEN 'ไม่ระบุ ❔' 
                     WHEN status = 'active' THEN 'ปกติ ✅'
                     WHEN status = 'active_notgood' THEN 'ปกติแต่ไม่ดี 🟨'
                     WHEN status = 'wait_test' THEN 'กำลังเทส 🔵'
                     WHEN status = 'not_active' THEN 'เสีย ❌'
                     WHEN status = 'wait' THEN 'รอซ่อม 🟡'
                     WHEN status = 'repairing' THEN 'กำลังซ่อม 🟤' 
                     ELSE status 
                 END AS สถานะ,
                 note,
                 image AS รูปภาพ
        FROM Stock_Tools";

// ดึงข้อมูลจากฐานข้อมูล
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดูอุปกรณ์ในห้องเครื่องมือ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
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
<?php include 'dataTableLink_Rel.php'; ?>
<?php include 'navbar.php'; ?>

<body>
    <div class="background-image"></div>
    <div class="container mt-4">
        <h2 class="text-center">ดูอุปกรณ์ในห้องเครื่องมือ</h2>

        <a href="mainsystem.php" class="btn btn-primary mt-3">ย้อนกลับ</a>

        <!-- แสดงตาราง -->
        <div class="table-responsive">
            <table class="table table-striped nowrap" style="width:100%" id="stock_controller_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ลำดับ</th>
                        <th>ชื่ออุปกรณ์</th>
                        <th>จำนวน</th>
                        <th>สถานะ</th>
                        <th>รูปภาพ</th>
                        <th>หมายเหตุ</th>
                        
                        
                        <?php if ($permission === 'admin') {
                            echo '<th>แก้ไข</th>';
                        } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['ลำดับ']); ?></td>
                            <td><?php echo htmlspecialchars($row['ชื่ออุปกรณ์']); ?></td>
                            <td><?php echo htmlspecialchars($row['จำนวน']); ?></td>
                            <td><?php echo htmlspecialchars($row['สถานะ']); ?></td>
                            <td>
                                <?php
                                if (!empty($row['รูปภาพ'])) {
                                    $fileName = htmlspecialchars($row['รูปภาพ']);
                                    $imagePath = "tools/" . $fileName;

                                    // ตรวจสอบว่านามสกุลไฟล์คืออะไร
                                    $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                                    $found = false;

                                    foreach ($validExtensions as $ext) {
                                        if (file_exists("tools/" . $fileName . "." . $ext)) {
                                            $imagePath .= "." . $ext;
                                            $found = true;
                                            break;
                                        }
                                    }

                                    // ถ้าไฟล์ไม่มีในโฟลเดอร์เลย แสดงข้อความผิดพลาด
                                    if (!$found) {
                                        $imagePath = "tools/no-image.png"; // กำหนดภาพเริ่มต้นถ้าไม่มีรูป
                                    }
                                ?>
                                    <button type='button' class='btn btn-success btn-sm' onclick='showImage("<?php echo $imagePath; ?>")'>ดูรูป</button>
                                <?php } else { ?>
                                    ไม่มีรูปภาพ
                                <?php } ?>
                            </td>
                            <td><?php echo htmlspecialchars(string: $row['note']); ?></td>
                            
                            <?php if ($permission === 'admin') { ?>
                                <td>
                                    <a href="editItem_tools.php?id=<?php echo urlencode($row['id']); ?>&table=Stock_Tools" class="btn btn-warning btn-sm">แก้ไข</a>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            if (!$.fn.dataTable.isDataTable('#stock_controller_table')) {
                new DataTable('#stock_controller_table', {
                    responsive: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/th.json'
                    }
                });
            }
        });

        function showImage(imageUrl) {
            Swal.fire({
                imageUrl: imageUrl,
                imageAlt: "รูปภาพ",
                imageWidth: 'auto',
                imageHeight: 'auto',
                maxWidth: '90vw', // กำหนดให้ขนาดสูงสุดไม่เกิน 90% ของหน้าจอ
                maxHeight: '80vh' // กำหนดให้ขนาดสูงสุดไม่เกิน 80% ของความสูงหน้าจอ
            });
        }
    </script>
</body>

</html>