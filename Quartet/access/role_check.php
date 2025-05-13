<?php
/*
role_check.php
A script to check a user's role.
Authors: Brinley Hull, Allie Stratton, Jose Leyba, Ben Renner, Kyle Moore
Creation date: 4/23/2025
Revisions:
Preconditions: None
Postconditions: None
Error conditions: None
Side effects: None
Invariants: None
Any known faults: None
*/

require 'db_connection.php';
require 'login_check.php';
$barber_id = $_SESSION['username'];
$sql = "SELECT Barber_Information.Role FROM Barber_Information WHERE Barber_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $barber_id);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

if ($role == "Barber") {
    include("barber_header.php");
}
else {
    include("manager_header.php");
}
?>