<?php
include('connection.php');
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['permission'])) {
    header("Location: index.php");
    exit;
}

$search = isset($_POST['search']) ? mysqli_real_escape_string($con, $_POST['search']) : '';
$table = isset($_POST['table']) ? $_POST['table'] : 'all';

$query = '';
if ($table === 'all' || $table === 'Stock_Main2_KPS') {
    $query = "SELECT
                 id,
                 firstname, 
                 surname, 
                 nickname
          FROM nameTable 
          WHERE firstname LIKE '%$search%' 
             OR surname LIKE '%$search%' 
             OR nickname LIKE '%$search%'";
}

if ($query) {
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        echo '<div class="table-responsive">';
        echo '<table class="table table-striped nowrap" style="width:100%" id="table_manageUser">';
        echo '<thead><tr>
        <th>id</th>
        <th>ชื่อจริง - นามสกุล</th>
        <th>ชื่อเล่น</th>
        <th>แก้ไข</th>
        </tr></thead>';
        echo '<tbody>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['firstname']) . ' ' . htmlspecialchars($row['surname']) . '</td>';
            echo '<td>' . htmlspecialchars($row['nickname']) . '</td>';
            echo '<td><a href="editUser.php?id=' . urlencode($row['id']) . '&table=nameTable" class="btn btn-warning btn-sm">แก้ไข</a></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo "<script>
    $(document).ready(function() {
    // Check if the DataTable is already initialized
    if (!$.fn.dataTable.isDataTable('#table_manageUser')) {
        new DataTable('#table_manageUser', {
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
