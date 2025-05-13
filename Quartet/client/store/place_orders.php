<!--
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 03/17/2025
Revisions:
    03/17/2025 -- Alexandra Stratton -- Created the place_order.php
    03/29/2025 -- Alexandra Stratton -- Add Comments
    04/06/2025 -- Alexandra Stratton -- Refactoring
Purpose: Allow customers to place orders from their shopping cart
-->

<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'config.php';
require 'PHPMailerMaster/src/Exception.php';
require 'PHPMailerMaster/src/PHPMailer.php';
require 'PHPMailerMaster/src/SMTP.php';

// Enable error reporting
error_reporting(E_ALL);  // Report all PHP errors
ini_set('display_errors', 1);  // Display errors to the browser
ini_set('display_startup_errors', 1);  // Display startup errors

require 'db_connection.php'; // Ensure this file contains your database connection logic
$error = "";
$success = "";
$session_id = session_id();
$sql = "SELECT Cart.*, Products.Name, Products.Price, Products.Image 
        FROM Cart 
        JOIN Products ON Cart.Product_ID = Products.Product_ID
        WHERE Cart.Session_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $session_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

// Calculate total price
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['Price'] * $item['Quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $comments = $_POST['comments'];

    // Check if the client already exists
    $check_client_query = "SELECT Client_ID FROM Client WHERE Email = ?";
    $stmt = $conn->prepare($check_client_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Client exists, fetch their client_id
        $row = $result->fetch_assoc();
        $client_id = $row['Client_ID'];

        // Update client information
        $update_client_query = "UPDATE Client SET First_Name = ?, Last_Name = ?, Phone = ? WHERE Client_ID = ?";
        $stmt = $conn->prepare($update_client_query);
        $stmt->bind_param("ssss", $first_name, $last_name, $phone, $client_id);
        $stmt->execute();
    } else {
        // Insert new client
        $insert_client_query = "INSERT INTO Client (First_Name, Last_Name, Email, Phone) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_client_query);
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $phone);
        $stmt->execute();
        $client_id = $stmt->insert_id;
    }

    // Insert the order
    $order_query = "INSERT INTO Orders (Client_ID, Client_Comments, Total_Price) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("isd", $client_id, $comments, $total_price);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert order items
    foreach ($cart_items as $item) {
        $product_id = $item['Product_ID'];
        $quantity = $item['Quantity'];
        $price = $item['Price'];
        $price = $price * $quantity;

        $order_item_query = "INSERT INTO Order_Items (Order_ID, Product_ID, Quantity, Price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($order_item_query);
        $stmt->bind_param("isid", $order_id, $product_id, $quantity, $price);
        $stmt->execute();
    }

// After the order is successfully placed (right before the header redirect)
$order_id = $stmt->insert_id;

    // Send order confirmation email
    try {
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Sender
        $mail->setFrom('quartetbarber@gmail.com', 'Quartet Barbershop');
        
        // Recipient
        $mail->addAddress($email, $first_name . ' ' . $last_name);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = "Your Order #$order_id Has Been Received";
        
        // Build HTML email body
        $mail->Body = "
            <html>
            <head>
                <title>Order Confirmation</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .order-details { margin: 20px 0; }
                    .product { margin-bottom: 10px; }
                    .total { font-weight: bold; font-size: 1.2em; }
                    .notes { margin-top: 20px; padding: 10px; background-color: #f5f5f5; }
                </style>
            </head>
            <body>
                <h2>Hello $first_name,</h2>
                <p>Thank you for your order at Quartet Barbershop! We've received your order #$order_id and will process it shortly.</p>
                
                <div class='order-details'>
                    <h3>Order Summary</h3>";
        
        foreach ($cart_items as $item) {
            $mail->Body .= "
                    <div class='product'>
                        <img src='{$item['Image']}' alt='{$item['Name']}' width='50' style='vertical-align:middle; margin-right:10px;'>
                        {$item['Name']} - 
                        Quantity: {$item['Quantity']} - 
                        Price: $" . number_format($item['Price'] * $item['Quantity'], 2) . "
                    </div>";
        }
        
        $mail->Body .= "
                    <p class='total'>Order Total: $" . number_format($total_price, 2) . "</p>
                </div>";
        
        if (!empty($comments)) {
            $mail->Body .= "
                <div class='notes'>
                    <h4>Your Comments:</h4>
                    <p>" . nl2br(htmlspecialchars($comments)) . "</p>
                </div>";
        }
        
        $mail->Body .= "
                <p>We'll notify you when your order is ready for pickup.</p>
                <p>If you have any questions, please contact us at quartetbarber@gmail.com</p>
                <p>Thank you for choosing Quartet Barbershop!</p>
            </body>
            </html>
        ";
        
        $mail->send();
        $success = "Order placed successfully! Confirmation email sent.";
    } catch (Exception $e) {
        $error = "Order placed but confirmation email failed: " . $e->getMessage();
        error_log("Order confirmation email error for order #$order_id: " . $e->getMessage());
    }

    // Clear the cart for the current session
    $clear_cart_query = "DELETE FROM Cart WHERE Session_ID = ?";
    $stmt = $conn->prepare($clear_cart_query);
    $stmt->bind_param("s", $session_id);
    $stmt->execute();

    header("Location: order_confirmation.php?order_id=" . $order_id);
    exit();

}
?>

<?php
//Adds the header to the page reducing redunacny
include("header.php");
?>
<head>
    <!-- Title for Page --> 
    <title>Place Order</title>
    <link rel="stylesheet" href="style/style1.css">
    <script src="validate.js"></script>
    <style>
        /* Style for the page */

        .cart-container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 20px;
            text-align: center;
        }
        .cart-contatiner label {
            text-align: left;
        }
        .basic-details {
            flex: 1;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
        }
        .order-details {
            flex: 2;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
        }
        .total-price {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            margin-bottom: 20px;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        img {
            max-width: 50px;
            height: auto;
            border-radius: 5px;
        }
        label {
            text-align: left;
        }
        /* Style for input boxes */
        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        textarea {
            height: 150px; 
            resize: vertical; 
        }
        .confirm-btn {
            color: white;
            background: #c4454d;
            padding: 5px 100px;
            font-size: 18px;
            font-family: 'Georgia', serif;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        .confirm-btn:hover {
            background: rgb(143, 48, 55);
        }
        .order-title {
            color: black;
        }
        .black-text {
            color: black;
        }
    </style>
</head>
<body>
<h1 class="order-title">Place Your Order</h1>
<!-- Form for client information -->
<div class="cart-container">
    <div class="order-details">
        <h2 class="black-text">Basic Details</h2>
        <form action="place_orders.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            
            <label for="first_name"><strong>First Name:</strong></label>
            <input type="text" name="first_name" id="first_name" required onchange="validateName.call(this)">
            <span id="first_name-error" style="color: red; display: none;"></span>
            <br>
            <label for="last_name"><strong>Last Name:</strong></label>
            <input type="text" name="last_name" id="last_name" required onchange="validateName.call(this)">
            <span id="last_name-error" style="color: red; display: none;"></span>
            <br>
            <label for="email"><strong>Email:</strong></label>
            <input type="email" name="email" id="email" required onchange="validateEmail()">
            <span id="email-error" style="color: red; display: none;"></span>
            <br>
            <label for="phone"><strong>Phone #:</strong></label>
            <input type="tel" name="phone" id="phone" required onchange="validatePhone()">
            <span id="phone-error" style="color: red; display: none;"></span>
            <br>
            <label for="comments"><strong>Comments:</strong></label>
            <textarea name="comments"></textarea>
            <br>
            <br>
            <input type="submit" value="Place Order" class="confirm-btn">
        </form>
    </div>
    <!-- Displays cart seen in shopping cart -->
    <div class="order-details">
        <h2 class="black-text">Order Details</h2>
        <table>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <img src="<?php echo $item['Image']; ?>" alt="<?php echo $item['Name']; ?>">
                        </td>
                        <td>
                            <?php echo $item['Name']; ?>
                        </td>
                        <td>
                            $<?php echo number_format($item['Price'] * $item['Quantity'], 2); ?>
                        </td>
                        <td>
                            x<?php echo $item['Quantity']; ?>    
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <div class="total-price">
                        Total: $<?php echo number_format($total_price, 2); ?>
                    </div>
                </tr>

            </tbody>
        </table>
    </div>
</div>

<script>    
    // Validate the entire form
    function validateForm(event) {
        const isfNameValid = validateName.call(document.getElementById("first_name"));
        const islNameValid = validateName.call(document.getElementById("last_name"));
        const isEmailValid = validateEmail();
        const isPhoneValid = validatePhone();

        if (!isfNameValid || !islNameValid || !isEmailValid || !isPhoneValid) {
            event.preventDefault(); // Prevent form submission
            return false;
        }
        return true; // Allow form submission
    }
    // Attach the validateForm function to the form's submit event
    document.querySelector('form').addEventListener('submit', validateForm);
</script>
</body>
</html>