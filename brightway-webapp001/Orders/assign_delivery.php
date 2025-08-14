<?php
ob_start();
header('Content-Type: application/json');
session_start();
include '../includes/db.php';
include '../includes/auth.php';

// Ensure logged-in salesperson
if (!isSalesperson()) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

// Get JSON data from fetch()
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['order_id'], $data['delivery_person_name'], $data['delivery_person_phone'])) {
    echo json_encode(["status" => "error", "message" => "Missing required fields."]);
    exit();
}

$order_id = intval($data['order_id']);
$delivery_person_name = trim($data['delivery_person_name']);
$delivery_person_phone = trim($data['delivery_person_phone']);
$salesperson_id = $_SESSION['user_id'] ?? null;

if (!$salesperson_id) {
    echo json_encode(["status" => "error", "message" => "No salesperson session found."]);
    exit();
}

$sql = "UPDATE orders 
        SET delivery_person_name = ?, 
            delivery_person_phone = ?, 
            salesperson_id = ?, 
            sent_out_at = NOW(), 
            status = 'sent'
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $delivery_person_name, $delivery_person_phone, $salesperson_id, $order_id);

if ($stmt->execute()) {
    ob_clean();
    echo json_encode(["status" => "success", "message" => "Order assigned successfully."]);
} else {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Failed to assign delivery."]);
}

$stmt->close();
$conn->close();
exit();
