<?php
/*
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 03/09/2025
    Revisions:
    03/13/2025 -- Jose Leyba -- Finished implementation, added working ReviewID's method

Purpose: Send reviews submited from the user to the database
    Preconditions:
        Acceptable inputs: 
            A form with method "post" that sends variable strings for variables name, rating, and review
            All variables are in string form
        Unacceptable inputs:
            Null values
    Postconditions:
        None
    Error/exceptions:
        Connection failed -> database could not connect.
        SQL prepare failed -> the SQL query was not valid and could not be prepared.
    Side effects:
        Adds a row to the reviews table in the database.
    Invariants: 
        None
    Known faults:
        None
*/
// start session to be able to get session information
session_start();
require 'config.php';

// Connect to the database
$mysqli = new mysqli('sql312.infinityfree.com', 'if0_38323969', 'Quartet44', 'if0_38323969_quartet');
if ($mysqli->connect_error) { // Catch database connection failure error
    die("Connection failed: " . $mysqli->connect_error);
}

// Get latest ID from the table
$result = $mysqli->query("SELECT MAX(ReviewID) AS ReviewID FROM Reviews");
$row = $result->fetch_assoc();
$new_id = $row['ReviewID'] ? $row['ReviewID'] + 1 : 1;

// set corresponding variables from the form post from the confirm appointment page and from the previously-set session variables from schedule.php
$Name = isset($_POST['Name']) && !empty(trim($_POST['Name'])) ? trim($_POST['Name']) : "Anonymous";
$Rating = $_POST['Rating'];
$Review = $_POST['Review'];


// prepare a query to insert a row into the confirmed appointments table in the database with the corresponding info
$query = "INSERT INTO Reviews (Review_ID, Name, Rating, Review) VALUES (?, ?, ?, ?)";

// prepare the query 
$stmt = $mysqli->prepare($query);
if (!$stmt) {
    die(json_encode(["error" => "SQL prepare failed: " . $mysqli->error]));
}

// Bind parameters correctly (integer, string, integer, string)
$stmt->bind_param("isis", $new_id, $Name, $Rating, $Review);
$stmt->execute();

// close everything
$stmt->close();
$mysqli->close();
header("Location: index.php");
exit();
?>
