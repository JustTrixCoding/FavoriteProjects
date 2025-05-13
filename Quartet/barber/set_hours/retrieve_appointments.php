<?php
/*  
    retrieve_availability.php
    A program to connect to the database and return the appointments for a barber for a specific week.
    Authors: Brinley Hull
    Creation date: 3/29/2025
    Revisions:
        3/29/2025 - Brinley, creation
        4/2/2025 - Brinley, refactoring
        4/5/2025 - Brinley, fix weeks with mixed months
        4/7/2025 - Brinley, allow minute intervals
        4/25/2025 - Brinley, validation
    Preconditions: All
    Postconditions: None
    Error conditions: Database errors
    Side effects: None
    Invariants: None
    Any known faults: None
*/
session_start(); //start the session

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); // Ensure JSON response

//connect to the database
require 'db_connection.php';
require 'get_check.php';

// Check role
$barber_id = $_SESSION['username'];
$sql = "SELECT Role FROM Barber_Information WHERE Barber_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $barber_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Validate input
$week = $_GET['week'];
$year = $_GET['year'];
$barber = isset($_GET['barber']) ?  $_GET['barber'] : -1;

// Check role
if ($user['Role'] != "Manager" && $barber != $barber_id) {
    header("Location: login.php");
    exit();
}

// check whether barber exists
$query = "SELECT COUNT(*) AS count FROM Barber_Information WHERE Barber_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $barber);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    die("Invalid barber value: " . htmlspecialchars($barber));
}

// Prepare SQL query
$query = "SELECT * FROM Appointment_Availability a 
          WHERE Barber_ID=? 
          AND Available='Y' 
          AND ((Week=? AND Year=?) OR (Weekday != -1
          AND NOT EXISTS (SELECT 1 FROM Appointment_Availability b
                WHERE a.Barber_ID = b.Barber_ID
                AND a.Time = b.Time
                AND a.Minute = b.Minute
                AND b.Week = ?
                AND b.Year = ?
                AND a.Weekday != WEEKDAY(STR_TO_DATE(CONCAT(b.Year, '-', b.Month+1, '-', b.Day), '%Y-%m-%d'))
                AND b.Available = 'N')))"; // AND NOT EXISTS checks for non-available over recurring

// execute queyr
$stmt = $conn->prepare($query);
if (!$stmt) {
    die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
}

// Bind parameters
$stmt->bind_param("siiii", $barber, $week, $year, $week, $year);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die(json_encode(["error" => "SQL execution failed: " . $stmt->error]));
}

// Fetch all rows
$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

// Return JSON response
echo json_encode($appointments, JSON_PRETTY_PRINT);

$stmt->close();
$conn->close();
?>
