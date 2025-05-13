<?php
/*
    set_hours_db.php
    Sends specific week barber hour information to the database.
    Author: Alexandra Stratton, Ben Renner, Brinley Hull, Jose Leyba, Kyle Moore
    Revisions:
        3/29/2025 - Brinley, add week to database.
        4/2/2025 - Brinley, refactoring
        4/5/2025 - Brinley, fix mixed months information
        4/7/2025 - Brinley, allow minute intervals
    Other sources of code: ChatGPT
    Creation date: 3/14/2025
    Preconditions:
        Acceptable inputs: All
        Unacceptable inputs: None
    Postconditions:
        None
    Error/exceptions: Database errors
    Side effects: Changes the database
    Invariants: 
        None
    Known faults:
        None
*/

// start session to be able to get session information
session_start();

// connect to the database
require 'db_connection.php';
require 'login_check.php';
require 'form_check.php';

$daysOfWeek = range(0, 6); // initialize days of the week list

$times = range(8, 17); // make range of valid times
$minutes = ['00', '15','30','45'];
$barber = $_POST["barber"];
$week = $_SESSION["week"];

// Check whether barber exists
$query = "SELECT COUNT(*) AS count FROM Barber_Information WHERE Barber_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $barber);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    die("Invalid barber value: " . htmlspecialchars($barber));
}

$stmt->close();

$month = (int)$_SESSION["month"] - 1;
$startDate = $_SESSION["startDate"];
$year = (int)$_SESSION["year"];
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month-1, $year); // Number of days in the month
$tempMonth = (int)$month;
$tempYear = $year;
foreach ($times as $hour) { // create each row of times and checkboxees
    foreach ($minutes as $minute) {
        foreach ($daysOfWeek as $day) { //create variables for every day and time combo
            $tempMonth = $month;
            $tempYear = $year;
            $varName = $day . '-' . $hour . '-' . $minute;
            $$varName = isset($_POST[$varName]) ? $_POST[$varName] : "unchecked"; // Dynamically create the variable
            
            // delete any previous rows to avoid duplicates
            $query = "DELETE FROM Appointment_Availability WHERE Barber_ID=? AND Time = ? AND Minute = ? AND Month = ? AND Day = ? AND Year = ?";
    
            // prepare the query 
            $stmt = $conn->prepare($query);
            if (!$stmt) { // if the query is not valid, throw error
            die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
            }
    
            // Double check date to see if valid
            $date = $day + (int)$startDate;
    
            // if day of month is greater than the end of the month, change to next month; vice versa
            if ($date > $daysInMonth) {
                $tempMonth+=1;
                if ($tempMonth > 11) {
                    $tempYear+=1;
                    $tempMonth = 0;
                }
                $date -= $daysInMonth;
            }
            if ($date < 1) {
                $tempMonth-=1;
                if ($tempMonth < 0) {
                    $tempYear-=1;
                    $tempMonth = 11;
                }
                $date += cal_days_in_month(CAL_GREGORIAN, $tempMonth, $tempYear);
            }
            $available = $$varName != "unchecked" ? "Y" : "N";
    
            // Bind parameters to put them into the SQL query
            $stmt->bind_param("ssssss", $barber, $hour, $minute, $tempMonth, $date, $tempYear);
            $stmt->execute(); // execute the SQL query
    
            // prepare a query to insert a row into the confirmed appointments table in the database with the corresponding info
            $query = "INSERT INTO Appointment_Availability 
            VALUES (?, -1, ?, ?, ?, ?, ?, ?, ?)";
    
            // prepare the query 
            $stmt = $conn->prepare($query);
            if (!$stmt) { // if the query is not valid, throw error
            die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
            }
    
            // Bind parameters to put them into the SQL query
            $stmt->bind_param("ssssssss", $barber, $hour, $minute, $tempMonth, $date, $tempYear, $week, $available);
            $stmt->execute(); // execute the SQL query
    
            $stmt->close();
            
        }
    }
}

// close the connections
$conn->close();

// redirect to the home page
header("Location: set_hours_manager.php?barber=" . $barber);

?>
