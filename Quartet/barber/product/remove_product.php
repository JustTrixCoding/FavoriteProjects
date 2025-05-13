<!--
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 03/12/2025
Revisions:
    03/12/2025 -- Alexandra Stratton -- Created the remove product page
    03/15/2025  -- Alexandra Stratton  -- Commenting
    03/15/2025  -- Alexandra Strattoon -- When a product is deleted it also deletes those products from everyones cart
Purpose: Allow barbers to remove a product from the store
    Preconditions
        Acceptable inputs: Product ID
        Unacceptable inputs: None
    Postconditions:
        Item gets deleted from database
    Error conditions:
        DB Issues
    Side effects
        None
    Invariants
        None
    Known faults:
        None
-->
<?php
//Connects to the database
require 'db_connection.php';
session_start();

if (isset($_GET['Product_ID'])) {
    //Gets the product_id
    $product_id = $_GET['Product_ID'];
    // Delete this product from everyone's shopping cart
    $sql_delete_cart = "DELETE FROM Cart WHERE Product_ID = ?";
    $stmt_delete_cart = $conn->prepare($sql_delete_cart);
    $stmt_delete_cart->bind_param("i", $product_id);
    if ($stmt_delete_cart->execute()) {
        $sql = "DELETE FROM Products WHERE Product_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $product_id);
        // Execute the statement and check if the Delete was successful
        if ($stmt->execute()) {
            header('Location: product.php');
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>