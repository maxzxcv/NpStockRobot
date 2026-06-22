<?php
include('connection.php');
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['permission'])) {
    header("Location: index.php");
    exit;
}
$permission = $_SESSION['permission'];
$search = isset($_POST['search']) ? mysqli_real_escape_string($con, $_POST['search']) : '';
$table = isset($_POST['table']) ? $_POST['table'] : 'all';

$query = '';
if ($table === 'all') {
    $query = "SELECT id, 
                     ItemName AS ชื่ออุปกรณ์, 
                     SerialNumber, 
                     type AS ประเภท, 
                     whereitem AS เก็บไว้ที่, 
                     CASE 
                         WHEN status IS NULL OR status = '' THEN 'ไม่ระบุ ❔' 
                         WHEN status = 'active' THEN 'ปกติ ✅'
                         WHEN status = 'active_notgood' THEN 'ปกติแต่ไม่ดี 🟨'
                         WHEN status = 'wait_test' THEN 'กำลังเทส 🔵'
                         WHEN status = 'not_active' THEN 'เสีย ❌'
                         WHEN status = 'wait' THEN 'รอซ่อม 🟡'
                         WHEN status = 'repairing' THEN 'กำลังซ่อม 🟤' 
                         ELSE status 
                     END AS สถานะ 
                    FROM Stock_Main2_KPS 
                    WHERE ItemName LIKE '%$search%'
                        OR SerialNumber LIKE '%$search%'
                        OR type LIKE '%$search%'
                        OR whereitem LIKE '%$search%'";
             
} else {
    $query = "SELECT id, 
                     ItemName AS ชื่ออุปกรณ์, 
                     SerialNumber, 
                     type AS ประเภท, 
                     whereitem AS เก็บไว้ที่, 
                     CASE 
                         WHEN status IS NULL OR status = '' THEN 'ไม่ระบุ ❔' 
                         WHEN status = 'active' THEN 'ปกติ ✅'
                         WHEN status = 'active_notgood' THEN 'ปกติแต่ไม่ดี 🟨'
                         WHEN status = 'wait_test' THEN 'กำลังเทส 🔵'
                         WHEN status = 'not_active' THEN 'เสีย ❌'
                         WHEN status = 'wait' THEN 'รอซ่อม 🟡'
                         WHEN status = 'repairing' THEN 'กำลังซ่อม 🟤' 
                         ELSE status 
                     END AS สถานะ 
                        FROM Stock_Main2_KPS 
                        WHERE SerialNumber LIKE '%$search%' 
                            AND type = '$table'";
}


if ($query) {
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {

        echo '<div class="table-responsive">';
        echo '<table class="table table-striped nowrap" style="width:100%" id="stock_controller_table">';
        
        // ตรวจสอบสิทธิ์เพื่อแสดงส่วนหัว 'แก้ไข'
        echo '<thead><tr><th>ID</th><th>ชื่ออุปกรณ์</th><th>Serial Number</th><th>ประเภท</th><th>สถานะ</th><th>เก็บไว้ที่</th>';
        if ($permission === 'admin') {
            echo '<th>แก้ไข</th>';
        }
        echo '</tr></thead>';
        
        echo '<tbody>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['ชื่ออุปกรณ์']) . '</td>';
            echo '<td>' . htmlspecialchars($row['SerialNumber']) . '</td>';
            echo '<td>' . htmlspecialchars($row['ประเภท']) . '</td>';
            echo '<td>' . htmlspecialchars($row['สถานะ']) . '</td>';
            echo '<td>' . htmlspecialchars($row['เก็บไว้ที่']) . '</td>';
            if ($permission === 'admin') {
                echo '<td><a href="editItem_controller.php?id=' . urlencode($row['id']) . '&table=Stock_Main2_KPS" class="btn btn-warning btn-sm">แก้ไข</a></td>';
            }
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';

        echo "<script>
    $(document).ready(function() {
    // Check if the DataTable is already initialized
    if (!$.fn.dataTable.isDataTable('#stock_controller_table')) {
        new DataTable('#stock_controller_table', {
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/th.json'
            }
        });
    }
});
</script>";
    } else {
        echo '<p>ไม่พบข้อมูลที่ค้นหา</p>';
    }
} else {
    echo '<p>ไม่มีข้อมูลในตารางนี้</p>';
}
?>
