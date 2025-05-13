<?php
/*
    recieve_feedback.php
    Sends client feedback information with corresponding client and email to the database.
    Authors: Ben Renner, Jose Leyba, Brinley Hull, Kyle Moore, Alexandra Stratton
    Other sources of code:
    Creation date: 03/30/2025
    Revisions:

    Preconditions:
        Acceptable inputs: 
            A form with method "post" that sends variable strings for variables email, name, and comment
            All variables are in string form
        Unacceptable inputs:
            Null values for email or comment
    Postconditions:
        None
    Error/exceptions:
        Connection failed -> database could not connect.
        SQL prepare failed -> the SQL query was not valid and could not be prepared.
    Side effects:
        Adds a row to the questions table in the database.
    Invariants: 
        None
    Known faults:
        None
*/

// Start session to be able to get session information
session_start();
require 'config.php';
// Connect to the database
$mysqli = new mysqli('sql312.infinityfree.com', 'if0_38323969', 'Quartet44', 'if0_38323969_quartet');
if ($mysqli->connect_error) { // Catch database connection failure error
    die("Connection failed: " . $mysqli->connect_error);
}


// Set corresponding variables from the form post from the confirm appointment page and from the previously-set session variables from schedule.php
$email = $_POST['email'];
$name = $_POST['name'];
$comment = $_POST['comment'];



// Prepare a query to insert a row into the confirmed appointments table in the database with the corresponding info
$query = "INSERT INTO Questions (Email, Name, Comment)
          VALUES (?, ?, ?)";

// Prepare the query 
$stmt = $mysqli->prepare($query);
if (!$stmt) { // If the query is not valid, throw error
    die(json_encode(["error" => "SQL prepare failed: " . $mysqli->error]));
}

// Bind parameters to put them into the SQL query
$stmt->bind_param("sss", $email, $name, $comment);
$stmt->execute(); // Execute the SQL query

// Close the database connections
$stmt->close();
$mysqli->close();

header("Location: index.php");
exit(); // Ensure no further code is executed after the redirect
?>