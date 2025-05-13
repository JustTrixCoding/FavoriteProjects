<?php
/*  
    get_appointments.php
    A program to connect to the database and return the count of the appointments for a certain date.
    Authors: Brinley Hull
    Creation date: 2/27/2025
    Revisions:
        3/16/2025 - Brinley, add filtering
        3/28/2025 - Brinley, remove confirmed appointments
        4/2/2025 - Brinley, refactoring
        4/10/2025 - Brinley, hide overlapping appointment times
        4/12/2025 - Brinley, fix get appointment logic
        4/14/2025 - Brinley, updated filtering
        4/26/2025 - Brinley, updated filtering
*/

session_start(); // Start the session

require 'db_connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); // Ensure JSON response

// Validate input
$year = isset($_GET['year']) ? (int)$_GET['year'] : -1;
$month = isset($_GET['month']) ? (int)$_GET['month'] : -1;
$day = isset($_GET['day']) ? (int)$_GET['day'] : -1;
$weekday = isset($_GET['weekday']) ? $_GET['weekday'] : date('w', strtotime("$year-$month-$day"));
$barberID = isset($_SESSION['barberFilter']) ? $_SESSION['barberFilter'] : [];
$service = isset($_SESSION['serviceFilter']) ? $_SESSION['serviceFilter'] : "None";
// Ensure timeFilter is treated as an array
$time = isset($_SESSION['timeFilter']) ? $_SESSION['timeFilter'] : null;
if (!is_array($time)) {
    $time = [$time]; // Ensure it's an array
}

$query = "SELECT * FROM Appointment_Availability a 
          WHERE Available='Y' 
          AND ((Weekday=? AND NOT EXISTS (SELECT 1 FROM Appointment_Availability b
                WHERE a.Barber_ID = b.Barber_ID
                AND a.Time = b.Time
                AND a.Minute = b.Minute
                AND b.Month = ?
                AND b.Day = ?
                AND b.Year = ?
                AND b.Available = 'N')) OR (Month=? AND Day=? AND Year=? 
                AND NOT EXISTS (SELECT 1 FROM Appointment_Availability d WHERE 
                                d.Barber_ID = a.Barber_ID
                                AND d.Time = a.Time 
                                AND d.Minute = a.Minute
                                AND d.Weekday = ?
                                AND d.Available = 'Y')))
          AND NOT EXISTS (SELECT 1 FROM Confirmed_Appointments c JOIN Services s ON c.Service_ID = s.Service_ID
                WHERE c.Barber_ID = a.Barber_ID 
                AND (c.Time*60 + c.Minute + s.Duration > a.Time*60 + a.Minute AND c.Time*60 + c.Minute <= a.Time*60 + a.Minute)
                AND c.Month = ?
                AND c.Day = ?
                AND c.Year = ?)";

$params = [$weekday, $month, $day, $year, $month, $day, $year, $weekday, $month, $day, $year];
$paramTypes = "iiiiiiiiiii";

// Add barbers filter (multiple or single)
$barberID = isset($_SESSION['barberFilter']) ? $_SESSION['barberFilter'] : ["None"];
if (!is_array($barberID)) {
    $barberID = [$barberID];
}
$filterByBarber = !empty($barberID) && !in_array("None", $barberID);
if ($filterByBarber) {
    $placeholders = implode(',', array_fill(0, count($barberID), '?'));
    $query .= " AND a.Barber_ID IN ($placeholders)";
    foreach ($barberID as $id) {
        $params[] = $id;
        $paramTypes .= "s"; // Barber ID is an integer
    }
}

// Handle service filter (adjust valid barbers for the service)
$filterByService = $service !== "None";
if ($filterByService) {
    // Fetch valid barbers for the selected service
    $validBarbersQuery = "SELECT Barber_ID FROM Barber_Services WHERE Service_ID = ?";
    $validBarbersStmt = $conn->prepare($validBarbersQuery);
    $validBarbersStmt->bind_param('i', $service); // assuming $service is an integer
    $validBarbersStmt->execute();
    $validBarbersResult = $validBarbersStmt->get_result();

    $validBarbers = [];
    while ($row = $validBarbersResult->fetch_assoc()) {
        $validBarbers[] = $row['Barber_ID'];
    }

    if (empty($validBarbers)) {
        $query .= " AND 1=0"; // No barbers available for this service
    } else {
        $placeholders = implode(',', array_fill(0, count($validBarbers), '?'));
        $query .= " AND a.Barber_ID IN ($placeholders)";
        foreach ($validBarbers as $id) {
            $params[] = $id;
            $paramTypes .= "s"; 
        }
    }
}

// Handle time filter (multiple or single)
if (!empty($time) && !in_array("None", $time)) {
    $placeholders = implode(',', array_fill(0, count($time), '?'));
    $query .= " AND a.Time IN ($placeholders)";
    foreach ($time as $t) {
        $params[] = $t;
        $paramTypes .= "i"; // Time is an integer
    }
}

// Finalize and execute query
$stmt = $conn->prepare($query);
if (!$stmt) {
    die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
}

$stmt->bind_param($paramTypes, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

echo json_encode($appointments, JSON_PRETTY_PRINT);
$stmt->close();
$conn->close();
?>
