<?php
require '../includes/auth.php';
include_once '../includes/db.php';

if (!isSalesperson()) {
    echo "<script>alert('Access denied'); window.location.href='../users/login.php'</script>";
    exit();
}

$sql = "SELECT 
            o.*, 
            u.name AS customer_name, 
            u.phone_no
        FROM orders o
        JOIN users u ON o.customer_id = u.id
        ORDER BY o.created_at ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Salesperson Orders</title>
    <link rel="stylesheet" href="../assets/Css/Style.css">
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
        }
        .close {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }
        .modal input, .modal button {
            width: 100%;
            margin-top: 10px;
            padding: 8px;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h3 style="text-align:center;">Sales Panel</h3>
    <a href="../dashboard/salesperson.php">🏠 Dashboard</a>
    <a href="../Orders/salesperson_orders.php">📦 Orders</a>
    <a href="../users/logout.php">🚪 Logout</a>
</div>

<div class="main">
    <h2>All Orders</h2>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Total Price</th>
                <th>Delivery Person</th>
                <th>Sent At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['phone_no']) ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>₦<?= number_format($row['total_price'], 2) ?></td>
                    <td><?= $row['delivery_person'] ?? 'Not Assigned' ?></td>
                    <td><?= $row['sent_at'] ?? 'Not Sent' ?></td>
                    <td>
                        <button onclick="openTreatModal(
                            <?= $row['id'] ?>,
                            '<?= addslashes($row['customer_name']) ?>',
                            '<?= addslashes($row['phone_no']) ?>',
                            '<?= number_format($row['total_price'], 2) ?>'
                        )">Treat Order</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Modal -->
    <div id="treatModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeTreatModal()">&times;</span>
            <h3>Treat Order</h3>
            <p><b>Customer:</b> <span id="modalCustomer"></span></p>
            <p><b>Phone:</b> <span id="modalPhone"></span></p>
            <p><b>Total:</b> ₦<span id="modalTotal"></span></p>
            <input type="text" id="deliveryPerson" placeholder="Enter delivery person name">
            <button onclick="assignDelivery()">Assign Delivery</button>
        </div>
    </div>


</div>
    
    <script>
function assignDelivery(orderId) {
    const deliveryPerson = prompt("Enter Delivery Person Name:");
    if (!deliveryPerson) {
        alert("Delivery person name is required.");
        return;
    }

    fetch("../Orders/assign_delivery.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ order_id: orderId, delivery_person: deliveryPerson })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert(data.message);

            // Find the row of this order
            const row = document.querySelector(`#order-row-${orderId}`);
            if (row) {
                // Update delivery person cell
                row.querySelector(".delivery-person-cell").textContent = deliveryPerson;
                // Update status cell
                row.querySelector(".status-cell").textContent = "Sent";
                // Disable the treat button
                const btn = row.querySelector(".treat-btn");
                btn.textContent = "Assigned";
                btn.disabled = true;
                btn.style.backgroundColor = "#ccc";
                btn.style.cursor = "not-allowed";
            }
        } else {
            alert(data.message);
        }
    })
    .catch(err => console.error("Error:", err));
}
</script>

</body>
</html>
