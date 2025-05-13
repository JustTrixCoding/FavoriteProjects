<!--
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 03/12/2025
Revisions:
    03/12/2025 -- Alexandra Stratton -- Created the empty cart page
    03/15/2025  -- Alexandra Stratton  -- Commenting
    04/06/2025 -- Alexandra Stratton -- Refactoring
Purpose: Allow customers to remove all items from their shopping cart
-->
<?php
session_start();
//Connects to the database
require 'db_connection.php';

// Retrieves the users session_id
$session_id = session_id();
// Prepares the SQL that deletes all rows with that session ID
$sql = "DELETE FROM Cart WHERE Session_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $session_id);

// Execute the statement and check if is was successful
if ($stmt->execute()) {
    header('Location: cart.php');
    exit();
} else {
    echo "Error: " . $stmt->error;
}
?>