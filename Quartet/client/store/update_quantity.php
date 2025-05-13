<!--
 Authors: Alexandra, Jose, Brinley, Ben, Kyle
 Date: 03/12/2025
 Revisions:
    03/12/2025 -- Alexandra Stratton -- Created the update quanity page
    03/15/2025  -- Alexandra Stratton  -- Commenting
    04/06/2025 -- Alexandra Stratton -- Refactoring
 Purpose: Allow customers to update the quantity of a certain item from their cart
 -->
 <?php
 session_start();
 //Connects to the database
 require 'db_connection.php'; 
 
 if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_id'])) {
     // Gets the cart_id
     $cart_id = $_POST['cart_id'];
     $action = $_POST['action']; 
     // Prepares the SQL to get the quanity from the cart database
     $sql = "SELECT Quantity FROM Cart WHERE Cart_ID = ?";
     $stmt = $conn->prepare($sql);
     $stmt->bind_param("i", $cart_id);
     $stmt->execute();
     $result = $stmt->get_result();
     $cart_item = $result->fetch_assoc();
 
     $new_quantity = $cart_item['Quantity'];
     // See if the quanity needs to be increased or decreased
     // Acts accordingly 
     if ($action == "increase") {
         $new_quantity += 1;
     } elseif ($action == "decrease") {
         $new_quantity -= 1;
     }
     // If there quanity is less than one it removes it from the cart
     if ($new_quantity <= 0) {
         $sql = "DELETE FROM Cart WHERE Cart_ID = ?";
         $stmt = $conn->prepare($sql);
         $stmt->bind_param("i", $cart_id);
     } else {
         //Otherwise it updates the database
         $sql = "UPDATE Cart SET Quantity = ? WHERE Cart_ID = ?";
         $stmt = $conn->prepare($sql);
         $stmt->bind_param("ii", $new_quantity, $cart_id);
     }
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