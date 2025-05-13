<!--
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 03/17/2025
Revisions:
    03/17/2025 -- Alexandra Stratton -- Created the order_confirmation.php
    03/18/2025 -- Alexandra Stratton -- Implemented order confirmation
    04/06/2025 -- Alexandra Stratton -- Refactoring
Purpose: Once an order is placed a confirmation screen will show
-->
<?php
//Connects to the database
session_start();
require 'db_connection.php';
if (!isset($_GET['order_id'])) {
    die("Order ID not provided.");
}

$order_id = $_GET['order_id'];

// Fetch order details
$order_query = "SELECT Orders.*, Client.First_Name, Client.Last_Name, Client.Email, Client.Phone 
                FROM Orders 
                JOIN Client ON Orders.Client_ID = Client.Client_ID 
                WHERE Orders.Order_ID = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

// Fetch order items
$items_query = "SELECT Order_Items.Quantity, Order_Items.Price, Products.Name, Products.Image 
                FROM Order_Items 
                JOIN Products ON Order_Items.Product_ID = Products.Product_ID
                WHERE Order_Items.Order_ID = ?";
$stmt = $conn->prepare($items_query);
$stmt = $conn->prepare($items_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
$items = $items_result->fetch_all(MYSQLI_ASSOC);
$store_query = "SELECT * FROM Store LIMIT 1"; 
$store_result = $conn->query($store_query);
$store = $store_result->fetch_assoc();
?>

<?php
//Adds the header to the page reducing redunacny
include("header.php");
?>
<head>
    <!-- Title for Page --> 
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="style/style1.css">
    <style>
        /* General page styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .order-conf{
            text-align: center;
            color: #333;
        }

        .confirmation-info {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .confirmation-info h2 {
            margin-bottom: 10px;
            color: #333;
        }

        .confirmation-info p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
        }

        .order-details {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .order-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-details th, .order-details td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .order-details th {
            background-color: #f4f4f4;
        }

        .order-details img {
            width: 50px;
            height: auto;
            border-radius: 5px;
            margin-right: 10px;
        }

        .total-price {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }

        .contact-info p {
            font-size: 16px;
            color: #555;
        }

        .contact-info h2 {
            margin-top: 20px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="order-conf">Thank you for your order!</h1>
        
        <div class="confirmation-info">
            <h2><strong>Confirmation Number:</strong> #<?php echo $order['Order_ID']; ?></h2>
            <p>We've received your order and are preparing it for pickup.</p>
        </div>

        <div class="confirmation-info">
            <h2>Pickup Location:</h2>
            <p><strong>Store Name:</strong> <?php echo $store['Name']; ?></p>
            <p><strong>Address:</strong> <?php echo $store['Address']; ?>, <?php echo $store['City']; ?>, <?php echo $store['State']; ?> <?php echo $store['Zip_Code']; ?></p>
            <p><strong>Phone:</strong> <?php echo $store['Phone']; ?></p>
        </div>

        <div class="confirmation-info">
            <h2>Pickup Instructions:</h2>
            <p>1. An email will be sent to <strong><?php echo $order['Email']; ?></strong> when your order is ready.</p>
            <p>2. Bring your confirmation number (<strong><?php echo $order['Order_ID']; ?></strong>) and a valid ID when picking up your order.</p>
            <p>3. Please do not come to the store until you receive confirmation that your order is ready.</p>
        </div>

        <div class="order-details">
            <h2>Order Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <img src="<?php echo $item['Image']; ?>" alt="<?php echo $item['Name']; ?>">
                                <?php echo $item['Name']; ?>
                            </td>
                            <td>
                                x<?php echo $item['Quantity']; ?>    
                            </td>
                            <td>
                                $<?php echo number_format($item['Price'], 2); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" style="text-align: right; font-weight: bold;">Total:</td>
                        <td>
                            $<?php echo number_format($order['Total_Price'], 2); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="contact-info">
            <h2>Contact Us</h2>
            <p>If you have any questions, please contact us at:</p>
            <p><strong>Email:</strong> <?php echo $store['Email']; ?></p>
            <p><strong>Phone:</strong> <?php echo $store['Phone']; ?></p>
        </div>
    </div>
</body>
</html>