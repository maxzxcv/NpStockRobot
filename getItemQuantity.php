<?php
include('connection.php');

$category = $_GET['category'] ?? '';

$allowedTables = [
    'Stock_Main',
    'Stock_Main2',
    'Stock_Main2_inroom',
    'Stock_Main2_Study',
    'Stock_Main4_VR',
    'Stock_Tools'
];

if ($category === 'all') {

    $queries = [];
    foreach ($allowedTables as $table) {
        $queries[] = "SELECT id, ItemName FROM `$table`";
    }
    $query = implode(" UNION ", $queries);

} elseif (in_array($category, $allowedTables, true)) {

    if ($category === 'Stock_Main2_Study') {
        $query = "SELECT id, ItemName, list FROM `$category`";
    } else {
        $query = "SELECT id, ItemName FROM `$category`";
    }

} else {
    echo json_encode(['error' => 'Invalid category']);
    exit;
}

$result = mysqli_query($con, $query);

$items = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($items);
