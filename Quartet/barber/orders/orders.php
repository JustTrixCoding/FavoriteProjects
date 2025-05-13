<!--
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 03/32/2025
Revisions:
    03/31/2025 -- Alexandra Stratton -- created orders.php
    4/11/2025 -- Alexandra Stratton -- Fixed bug
    04/26/2025 -- Alexandra Stratton -- Refactoring
 Purpose: Allow the manager/barber to see all the orders

 -->
 <?php
//Connects to the database
session_start();
require 'db_connection.php';
require 'login_check.php';
require 'role_check.php';



$sql = "SELECT * FROM Orders";
$result = $conn->query($sql);
$orders = [];
if ($result->num_rows > 0) {
    while ($order = $result->fetch_assoc()) {
        $client_id = $order['Client_ID'];
        $clients= [];
        $stmt = $conn->prepare("SELECT First_Name, Last_Name FROM Client WHERE Client_ID = ?");
        $stmt->bind_param("i", $client_id);
        $stmt->execute();
        $client_result = $stmt->get_result();
        // Fetch client data
        if ($client_result->num_rows > 0) {
            $order['Client'] = $client_result->fetch_assoc();
        } else {
            $order['Client'] = null;
        }
        $stmt->close(); 
        $orders[] = $order;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Title for Page -->
    <title>Orders</title>
    <!-- Internal CSS for styling the page -->
    <link rel="stylesheet" href="style/barber_style.css">
</head>
<body>
    <div class="content-wrapper">
    <br><br>
        <div class="container">
        <h1>Orders</h1>
            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Client Name</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>View</th>
                        </tr>
                    </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['Order_ID']); ?></td>
                            <td><?php echo htmlspecialchars($order['Client']['First_Name']) . ' ' . htmlspecialchars($order['Client']['Last_Name']); ?></td>
                            <td><?php echo date('M j, Y g:i A', strtotime($order['Created_At'])); ?></td>
                            <td>$<?php echo number_format($order['Total_Price'], 2); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo htmlspecialchars($order['Status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($order['Status'])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="manage_orders.php?Order_ID=<?php echo $order['Order_ID']; ?>"><button class="view-btn">View</button></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>