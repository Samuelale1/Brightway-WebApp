<?php
require '../includes/auth.php';
require '../includes/db.php';

header('Content-Type: application/json');

// Only logged-in salesperson can access
if (!isSalesperson()) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['order_id']) || !isset($data['delivery_person'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$order_id = intval($data['order_id']);
$delivery_person = trim($data['delivery_person']);
$salesperson_id = $_SESSION['user_id']; // logged in salesperson

if ($delivery_person === '') {
    echo json_encode(["status" => "error", "message" => "Delivery person name is required"]);
    exit;
}

// Update the orders table
$sql = "UPDATE orders 
        SET salesperson_id = ?, delivery_person = ?, sent_at = NOW(), status = 'sent'
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isi", $salesperson_id, $delivery_person, $order_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Delivery assigned successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to assign delivery"]);
}

$stmt->close();
$conn->close();
