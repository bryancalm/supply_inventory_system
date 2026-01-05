<?php
session_start();
include '../database.php';

// Security check
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    exit("Access denied");
}

$filter_cat = $_GET['category'] ?? "";
$inv_sql = "SELECT items.*, categories.name AS cat_name
            FROM items
            JOIN categories ON items.category_id = categories.id
            WHERE 1";
if ($filter_cat != "") {
    $inv_sql .= " AND category_id = '$filter_cat'";
}

// Set headers for download
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=inventory_report_" . date('Ymd') . ".csv");

$out = fopen("php://output", "w");
// Add BOM for Excel UTF-8 support
fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($out, ["Item Name", "Category", "Quantity", "Unit", "Supplier", "Price"]);

$result = $conn->query($inv_sql);
while ($r = $result->fetch_assoc()) {
    fputcsv($out, [
        $r['name'],
        $r['cat_name'],
        $r['quantity'],
        $r['unit'],
        $r['supplier'],
        number_format($r['price'], 2)
    ]);
}
fclose($out);
exit;