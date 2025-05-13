<!--
 Authors: Alexandra, Jose, Brinley, Ben, Kyle
 Date: 03/12/2025
 Revisions:
    03/12/2025 -- Alexandra Stratton -- Created the app item page
    03/15/2025  -- Alexandra Stratton  -- Commenting
    04/06/2025 -- Alexandra Stratton -- Refactoring
 Purpose: Allow customers to add's item to cart from their shopping cart
 -->
<?php
session_start();
//Connects to the database
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    // Retrieves the 
    $product_id = $_POST['product_id'];
    $quantity = 1;
    $session_id = session_id();
    // SQL to see if the product is already in the cart
    $sql = "SELECT * FROM Cart WHERE Product_ID = ? AND Session_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $product_id, $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // When the product is already in the chart it add ones to the quantity
        $sql = "UPDATE Cart SET Quantity = Quantity + 1 WHERE Product_ID = ? AND Session_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $product_id, $session_id);
    } else {
        // When the product isn't in the cart already it will indsert the product into the database
        $sql = "INSERT INTO Cart (Product_ID, Quantity, Session_ID) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $product_id, $quantity, $session_id);
    }
    // Execute the statement and check if is was successful
    if ($stmt->execute()) {
        header('Location: store.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
?>