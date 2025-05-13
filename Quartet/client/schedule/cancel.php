<?php
/*
Authors: Alexandra Stratton, Ben Renner, Brinley Hull, Jose Leyba, Kyle Moore
Creation Date: 03/02/2025
Revisions: 
    03/03/2025 - Updated to cancel appointments by AppointmentID instead of email
Purpose: A page where clients can enter their appointment ID and cancel the appointment (sending info back to database).
*/

// Start the session to remember user info
session_start();

// Connect to the database
require 'db_connection.php';

// Get the appointment ID from the form
$appointmentID = $_POST['appointmentID'];

// Check if the appointment exists
$query = "SELECT * FROM Confirmed_Appointments WHERE Appointment_ID = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
}

// Bind the appointment ID parameter
$stmt->bind_param("i", $appointmentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die(json_encode(["error" => "No appointment found with this ID."]));
}

// Delete the appointment with the given ID
$deleteQuery = "DELETE FROM Confirmed_Appointments WHERE Appointment_ID = ?";
$deleteStmt = $conn->prepare($deleteQuery);
if (!$deleteStmt) {
    die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
}

// Bind the appointment ID parameter and execute the delete query
$deleteStmt->bind_param("i", $appointmentID);
if ($deleteStmt->execute()) {
    echo json_encode(["success" => "Appointment canceled successfully!"]);
} else {
    echo json_encode(["error" => "Failed to cancel appointment: " . $deleteStmt->error]);
}

// Close the database connections
$stmt->close();
$deleteStmt->close();
$conn->close();
header("Location: cancel_appointment.php");
exit();
?>