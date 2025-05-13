<?php
/* 
fetch_appointment.php
Finds confirmed appointments.
Authors: Alexandra Stratton, Jose Leyba, Brinley Hull, Ben Renner, Kyle Moore
Creation date:
Revisions:
    4/4/2025 - Brinley, fix cancel appointment 
    4/10/2025 - Brinley, add minute
*/
// Connect to the database
require 'db_connection.php';

// Get the appointment ID from the request
if (!isset($_POST['appointmentID']) || empty($_POST['appointmentID'])) {
    die(json_encode(["error" => "No appointment ID provided."]));
}

$appointmentID = intval($_POST['appointmentID']); // Ensure it's an integer

// Fetch appointment details
$query = "SELECT Month, Day, Year, Time, Minute, Barber_ID FROM Confirmed_Appointments WHERE Appointment_ID = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
}
//Bind values
$stmt->bind_param("i", $appointmentID);
$stmt->execute();
$stmt->bind_result($month, $day, $year, $time, $minute, $barberID);

//Display 
if ($stmt->fetch()) { //if appointment is found
    echo "<p>📅 <b>Date:</b> $month/$day/$year</p>";
    echo "<p>⏰ <b>Time:</b> $time:$minute</p>";
    echo "<p>💈 <b>Barber ID:</b> $barberID</p>";
    echo "<script>document.getElementById('hiddenAppointmentID').value = $appointmentID;</script>";
} else { //no appointment found
    echo "<p style='color: red;'>❌ No appointment found.</p>";
}

$stmt->close();
$conn->close();
?>
