<?php
require '../includes/auth.php';
include_once '../includes/db.php';

// Only admins
if (!isAdmin()) {
    die("Access Denied");
}

$type = $_GET['type'] ?? 'csv';

// Get treated orders
$sql = "SELECT o.id, u.name AS customer_name, u.address, u.phone_no,
               o.total_price, o.delivery_person_name, o.delivery_person_phone,
               sp.name AS salesperson_name, o.sent_out_at
        FROM orders o
        JOIN users u ON o.customer_id = u.id
        LEFT JOIN users sp ON o.salesperson_id = sp.id
        WHERE o.status = 'sent'
        ORDER BY o.sent_out_at DESC";
$result = $conn->query($sql);
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// CSV Export
if ($type === 'csv') {
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=treated_orders.csv");
    $out = fopen("php://output", "w");
    fputcsv($out, array_keys($orders[0]));
    foreach ($orders as $o) { fputcsv($out, $o); }
    fclose($out);
    exit();
}

// Excel Export
if ($type === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=treated_orders.xls");
    echo "<table border='1'><tr>";
    foreach (array_keys($orders[0]) as $col) echo "<th>$col</th>";
    echo "</tr>";
    foreach ($orders as $o) {
        echo "<tr>";
        foreach ($o as $val) echo "<td>".htmlspecialchars($val)."</td>";
        echo "</tr>";
    }
    echo "</table>";
    exit();
}

// PDF Export
if ($type === 'pdf') {
    // ✅ require vendor autoload and import Dompdf at the top of the block
    require_once("../vendor/autoload.php");
    // You MUST declare use at top of file, so let's do it inline:
    $dompdf = new \Dompdf\Dompdf();

    $html = "<h2>Treated Orders Report</h2><table border='1' cellspacing='0' cellpadding='5'><tr>";
    foreach (array_keys($orders[0]) as $col) $html .= "<th>$col</th>";
    $html .= "</tr>";
    foreach ($orders as $o) {
        $html .= "<tr>";
        foreach ($o as $val) $html .= "<td>".htmlspecialchars($val)."</td>";
        $html .= "</tr>";
    }
    $html .= "</table>";

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("treated_orders.pdf", ["Attachment" => 1]);
    exit();

}

echo "Invalid export type.";
