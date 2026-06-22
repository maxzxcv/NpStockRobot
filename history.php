<?php
include('connection.php');
session_start();

if (!isset($_SESSION['username']) && !isset($_SESSION['permission'])) {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];
$permission = $_SESSION['permission'];
$result = mysqli_query($con, "SELECT * FROM Employee WHERE username = '$username'");
$user = mysqli_fetch_assoc($result);

$category = isset($_POST['category']) ? $_POST['category'] : 'overview';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$query = "";
$search = isset($_POST['search']) ? mysqli_real_escape_string($con, $_POST['search']) : '';

if ($category == "import") {
    $query = "SELECT 'import' AS type, si.id, si.username, si.user, si.ItemName, si.Amount, si.Date AS date, e.firstname, e.surname, si.Image
              FROM Stock_Import si 
              JOIN Employee e ON si.username = e.username 
              WHERE si.ItemName LIKE '%$search%' OR si.user LIKE '%$search%'
              ORDER BY si.Date DESC";
} elseif ($category == "export") {
    $query = "SELECT 'export' AS type, se.id, se.username, se.user, se.ItemName, se.Amount, se.note, se.Date AS date, e.firstname, e.surname, se.Image 
              FROM Stock_Export se 
              JOIN Employee e ON se.username = e.username 
              WHERE se.ItemName LIKE '%$search%' OR se.user LIKE '%$search%'
              ORDER BY se.Date DESC";
} elseif ($category == "overview") {
    $query = "(SELECT 'import' AS type, si.id, si.username, si.user, si.ItemName, si.Amount, '' AS note, si.Date AS date, e.firstname, e.surname, si.Image
               FROM Stock_Import si 
               JOIN Employee e ON si.username = e.username
               WHERE si.ItemName LIKE '%$search%' OR si.user LIKE '%$search%')
              UNION ALL
              (SELECT 'export' AS type, se.id, se.username, se.user, se.ItemName, se.Amount, se.note, se.Date AS date, e.firstname, e.surname, se.Image
               FROM Stock_Export se 
               JOIN Employee e ON se.username = e.username
               WHERE se.ItemName LIKE '%$search%' OR se.user LIKE '%$search%')
              ORDER BY date DESC";
}

$result = mysqli_query($con, $query);
if (!$result) {
    echo "Error: " . mysqli_error($con);
}

if (!$result) {
    echo "Error in Query: " . mysqli_error($con);
    error_log("Query Error: " . mysqli_error($con)); // บันทึกใน log ไฟล์
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการนำเข้า/ออกสินค้า</title>
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
<?php include 'dataTableLink_Rel.php'; ?>
<?php include 'navbar.php'; ?>

<body>
    <div class="background-image"></div>

    <div class="container mt-4">
        <h2 class="text-center">ประวัติการนำเข้า/ออกสินค้า</h2>
        <form method="POST" class="mb-4">
            <div class="form-group">
                <label for="category" class="form-label">เลือกหมวดหมู่</label>
                <select name="category" id="category" class="form-control" required>
                    <option value="overview" <?php echo ($category == 'overview') ? 'selected' : ''; ?>>ภาพรวมประวัติ</option>
                    <option value="import" <?php echo ($category == 'import') ? 'selected' : ''; ?>>นำเข้า</option>
                    <option value="export" <?php echo ($category == 'export') ? 'selected' : ''; ?>>นำออก</option>
                </select>
            </div>
            <div class="form-group">
    </div>
    <button type="submit" class="btn btn-primary mt-3">ค้นหา</button>
    <a href="mainsystem.php" class="btn btn-secondary mt-3">ย้อนกลับ</a>

            
        </form>

        <?php
        if (!$result) {
            echo "Error in Query: " . mysqli_error($con);
            error_log("Query Error: " . mysqli_error($con)); // บันทึกใน log ไฟล์
        }
        ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (mysqli_num_rows($result) > 0) {
    echo '<div class="table-responsive">';
    echo "<table class='table table-striped nowrap' style='width:100%' id='table_id_history'>";
    echo '<thead><tr><th>ชื่อพนักงาน</th><th>ชื่อสินค้า</th><th>จำนวน</th><th>วันที่</th><th>หมวดหมู่</th><th>นำไปใช้</th><th>ดูรูป</th></tr></thead>';
    echo '<tbody>';

    while ($row = mysqli_fetch_assoc($result)) {
        $datetime = date("d-m-Y H:i", strtotime($row['date']));
        $imagePath = $row['Image'];
        $bgColor = ($row['type'] == 'import') ? 'style="background-color: #d4edda;"' : 'style="background-color: #f8d7da;"';

        echo "<tr $bgColor>";
        echo "<td>" . $row['user'] . "</td>";
        echo "<td>" . $row['ItemName'] . "</td>";
        echo "<td>" . $row['Amount'] . "</td>";
        echo "<td>" . $datetime . "</td>";
        echo "<td>" . ($row['type'] == 'import' ? 'นำเข้า' : 'นำออก') . "</td>";
        echo "<td>" . ($row['type'] == 'import' ? '-' : $row['note']) . "</td>";
        $buttonClass = ($row['type'] == 'import') ? 'btn-success' : 'btn-danger';
        echo "<td><button type='button' class='btn $buttonClass btn-sm' onclick='showImage(\"$imagePath\")'>ดูรูป</button></td>";
        echo "</tr>";
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';

    echo "<script>
    $(document).ready(function() {
    // Check if the DataTable is already initialized
    if (!$.fn.dataTable.isDataTable('#table_id_history')) {
        new DataTable('#table_id_history', {
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/th.json'
            },
            order: [[6, 'asc']]  // ตั้งค่าลำดับการแสดงผลจากมากไปน้อยในคอลัมน์วันที่ (คอลัมน์ที่ 1)
        });
    }
});
</script>";

} else {
    echo "<p>ไม่พบข้อมูลที่ค้นหา</p>";
}

        ?>

    </div>

    <script>
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