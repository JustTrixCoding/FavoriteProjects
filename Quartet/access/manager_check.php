<?php
/*
manager_check.php
A script to check if a user is manager role.
Authors: Brinley Hull, Allie Stratton, Jose Leyba, Ben Renner, Kyle Moore
Creation date: 4/25/2025
Revisions:
Preconditions: None
Postconditions: None
Error conditions: None
Side effects: None
Invariants: None
Any known faults: None
*/

session_start();
require 'db_connection.php';

$barber_id = $_SESSION['username'];
$sql = "SELECT Role FROM Barber_Information WHERE Barber_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $barber_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['Role'] != "Manager") {
    header("Location: login.php");
    exit();
} else {
    include("manager_header.php");
}
?>