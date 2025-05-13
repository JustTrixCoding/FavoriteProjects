<!--
 Authors: Alexandra, Jose, Brinley, Ben, Kyle
 Date: 03/12/2025
 Revisions:
    03/12/2025 -- Alexandra Stratton -- Created the remove item page
    03/15/2025  -- Alexandra Stratton  -- Commenting
    04/06/2025 -- Alexandra Stratton -- Refactoring
 Purpose: Allow customers to remove an item from their shopping cart
 -->
<?php
session_start();
//Connects to the database
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_id'])) {
    //Gets the cart_id
    $cart_id = $_POST['cart_id'];
    // Prepares the SQL to delete the item with the cart_id from the cart
    $sql = "DELETE FROM Cart WHERE Cart_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cart_id);
    // Execute the statement and check if is was successful
    if ($stmt->execute()) {
        header('Location: cart.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
?>