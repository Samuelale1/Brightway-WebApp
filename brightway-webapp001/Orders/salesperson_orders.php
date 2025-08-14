<?php
require '../includes/auth.php';
include_once '../includes/db.php';

if (!isSalesperson()) {
    echo "<script>alert('Access denied'); window.location.href='../users/login.php'</script>";
    exit();
}

$sql = "SELECT o.*, u.name AS customer_name, u.address, u.phone_no 
        FROM orders o 
        JOIN users u ON o.customer_id = u.id
        WHERE o.status IN ('pending', 'paid', 'sent')
        ORDER BY o.created_at ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Salesperson Orders</title>
    <link rel="stylesheet" href="../assets/Css/Style.css">
    <style>
        /* Sidebar */
        .sidebar {
            width: 220px;
            height: 100vh;
            background: #222;
            color: white;
            position: fixed;
            top: 0; left: 0;
            padding: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 20px 0;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #444;
        }
        .main {
            margin-left: 220px;
            padding: 20px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #FAF5ED;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            position: relative;
        }
        .modal-content h3 {
            margin-top: 0;
        }
        .close {
            position: absolute;
            right: 15px;
            top: 10px;
            cursor: pointer;
            font-size: 18px;
        }
        input, button {
            width: 100%;
            padding: 8px;
            margin-top: 8px;
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
                <th>Address</th>
                <th>Phone</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Delivery Person</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while($order = mysqli_fetch_assoc($result)) { ?>
            <tr id="order-row-<?= $order['id'] ?>">
                <td><?= $order['id'] ?></td>
                <td><?= $order['customer_name'] ?></td>
                <td><?= $order['address'] ?></td>
                <td><?= $order['phone_no'] ?></td>
                <td><?= $order['total_price'] ?></td>
                <td class="status-cell"><?= ucfirst($order['status']) ?></td>
                <td class="delivery-person-cell"><?= $order['delivery_person_name'] ?? 'Not Assigned' ?></td>
                <td>
                    <button 
                        class="treat-btn" 
                        onclick="openModal(<?= $order['id'] ?>, '<?= addslashes($order['address']) ?>', '<?= addslashes($order['total_price']) ?>')"
                        <?= in_array($order['status'], ['sent','delivered']) ? 'disabled style="background:#ccc;cursor:not-allowed;"' : '' ?>>
                        <?= in_array($order['status'], ['sent','delivered']) ? 'Assigned' : 'Treat Order' ?>
                    </button>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal" id="assignModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">×</span>
        <h3>Assign Delivery</h3>
        <p><b>Order ID:</b> <span id="modal-order-id"></span></p>
        <p><b>Address:</b> <span id="modal-address"></span></p>
        <p><b>Total Price:</b> ₦<span id="modal-total"></span></p>
        <input type="text" id="delivery-name" placeholder="Delivery Person Name" required>
        <input type="tel" id="delivery-phone" placeholder="Delivery Person Phone" required pattern="[0-9]{11}">
        <button onclick="assignDelivery()">Assign Delivery</button>
    </div>
</div>

<script>
let currentOrderId = null;

function openModal(orderId, address, total) {
    currentOrderId = orderId;
    document.getElementById("modal-order-id").textContent = orderId;
    document.getElementById("modal-address").textContent = address;
    document.getElementById("modal-total").textContent = total;
    document.getElementById("assignModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("assignModal").style.display = "none";
}

function assignDelivery() {
    const name = document.getElementById('delivery-name').value.trim();
    const phone = document.getElementById('delivery-phone').value.trim();

    if (!name || !phone) {
        alert("Please fill in all fields.");
        return;
    }

    fetch('../Orders/assign_delivery.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            order_id: currentOrderId,
            delivery_person_name: name,
            delivery_person_phone: phone
        })
    })
    .then(res => res.text())
    .then(text => {
        console.log("Raw response:", text); // DEBUG
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error("Invalid JSON: " + text);
        }

        if (data.status === "success") {
            const row = document.getElementById(`order-row-${currentOrderId}`);
            if (row) {
                row.querySelector(".delivery-person-cell").textContent = name;
                row.querySelector(".status-cell").textContent = "Sent";
                const btn = row.querySelector(".treat-btn");
                btn.textContent = "Assigned";
                btn.disabled = true;
                btn.style.backgroundColor = "#ccc";
                btn.style.cursor = "not-allowed";
            }
            closeModal();
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert("An error occurred: " + err.message);
        closeModal();
    });
}

// Close modal if clicked outside
window.onclick = function(e) {
    const modal = document.getElementById("assignModal");
    if (e.target === modal) closeModal();
}
</script>
</body>
</html>
