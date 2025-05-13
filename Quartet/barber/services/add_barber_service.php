<!--
add_barber_service.php
Purpose: Allow barbers to add services to their personal list
Authors: Alexandra Stratton, Jose Leyba, Brinley Hull, Ben Renner, Kyle Moore
Date: 4/10/2025
Revisions:
    4/18/2025 - Brinley Hull, change to where the barber whose service is added can be someone other than who logged in
Other Sources: ChatGPT
    Preconditions
        Acceptable inputs: all
        Unacceptable inputs: none
    Postconditions:
        None
    Error conditions:
        Database issues
    Side effects
        New entries are added to tables in the database.
    Invariants
        None
    Known faults:
        None
-->
<?php
session_start();
// Connects to the database
require 'db_connection.php';
require 'login_check.php';
require 'get_check.php';

// Check role
$barber_id = $_SESSION['username'];
$sql = "SELECT Role FROM Barber_Information WHERE Barber_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $barber_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$barber = isset($_GET['barber']) ?  $_GET['barber'] : $_SESSION['username'];

// Check role
if ($user['Role'] != "Manager" && $barber != $barber_id) {
    header("Location: login.php");
    exit();
}

$service_id = $_GET['Service_ID'];
// Prepares the SQL for inserting the new service into the database
$sql = "INSERT INTO Barber_Services (Barber_ID, Service_ID)
        SELECT ?, ?
        WHERE NOT EXISTS (
            SELECT 1 FROM Barber_Services WHERE Barber_ID = ? AND Service_ID = ?
        )";
$stmt = $conn->prepare($sql);
// Execute the statement and check if the insertion was successful
if (!$stmt) {
    echo "Error preparing statement: " . $conn->error;
    exit();
}

$stmt->bind_param("sisi", $barber, $service_id, $barber, $service_id);
// Execute the statement and check if the insertion was successful
if ($stmt->execute()) {
    echo "Service added successfully!";
    // Redirect to the service page after inserting infor into database
    header('Location: services_manager.php?barber=' . $barber);
    exit();
} else {
    // Display an error message if execution fails
    echo "Error executing statement: " . $stmt->error;
}
?>