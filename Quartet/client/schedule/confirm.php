<?php
/*
    confirm.php
    Sends confirmed appointment information with corresponding client and appointment details to the database.
    Authors: Ben Renner, Jose Leyba, Brinley Hull, Kyle Moore, Alexandra Stratton
    Other sources of code: ChatGPT
    Creation date: 3/1/2025
    Revisions:
        3/2/2025 - Brinley, commenting
        3/3/2025 - Added unique 7-digit integer AppointmentID
        3/4/2025 - Added email confirmation functionality using PHPMailer
        4/2/2025 - Brinley, refactoring and implementing client ID
        4/10/2025 - Brinley, add services and minute
    Preconditions:
        Acceptable inputs: 
            A form with method "post" that sends variable strings for variables fname, lname, email, and phone
            Previously set session information for day, month, year, time, and appointment
            Appointment session info is in the form of an array with a BarberID element
            All variables are in string form
        Unacceptable inputs:
            Null values
    Postconditions:
        None
    Error/exceptions:
        Connection failed -> database could not connect.
        SQL prepare failed -> the SQL query was not valid and could not be prepared.
    Side effects:
        Adds a row to the confirmed_appointments table in the database.
        Sends a confirmation email to the client.
    Invariants: 
        None
    Known faults:
        None
*/

// Start session to be able to get session information
session_start();
// Connect to the database
require 'db_connection.php';
require 'config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to generate a unique 7-digit integer
function generateUniqueAppointmentID($conn) {
    do {
        // Generate a random 7-digit number
        $appointmentID = mt_rand(1000000, 9999999); // mt_rand is faster and more random than rand()
        
        // Check if the ID already exists in the database
        $query = "SELECT Appointment_ID FROM Confirmed_Appointments WHERE Appointment_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $appointmentID);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0; // If rows are found, the ID already exists
        $stmt->close();
    } while ($exists); // Repeat until a unique ID is generated

    return $appointmentID;
}

function generateUniqueClientID($conn) {
    // function to generate the unique client ID
    do {
        // Generate a random 5-digit number
        $clientID = mt_rand(10000, 99999); // mt_rand is faster and more random than rand()
        
        // Check if the ID already exists in the database
        $query = "SELECT Client_ID FROM Client WHERE Client_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $clientID);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0; // If rows are found, the ID already exists
        $stmt->close();
    } while ($exists); // Repeat until a unique ID is generated

    return $clientID;
}

// Set corresponding variables from the form post from the confirm appointment page and from the previously-set session variables from schedule.php
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$day = $_SESSION["day"];
$month = $_SESSION["month"];
$year = $_SESSION["year"];
$time = $_SESSION['time'];//date("G", strtotime($_SESSION['time']));
$minute = $_SESSION['minute'];
$barber = $_SESSION['appointment']["Barber_ID"];
$service = $_POST['service'];

// get or create client id
$client = -1;

// check to see whether client exists
$query = "SELECT * FROM Client WHERE Email = ?";
$stmt = $conn->prepare($query);
if (!$stmt) { // If the query is not valid, throw error
    die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
}

// Bind parameters to put them into the SQL query
$stmt->bind_param("s", $email);
$stmt->execute(); // Execute the SQL query
$result = $stmt->get_result(); // get row results
if (!$result) {
    die(json_encode(["error" => "SQL execution failed: " . $stmt->error]));
}

// Fetch all rows
$count = 0;
while ($row = $result->fetch_assoc()) {
    $count++;
    $client = $row['Client_ID'];
}
if ($count == 0) {
    // check to see whether client exists
    $query = "INSERT INTO Client (Client_ID, First_Name, Last_Name, Email, Phone) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) { // If the query is not valid, throw error
        die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
    }
    $client = generateUniqueClientID($conn);

    // Bind parameters to put them into the SQL query
    $stmt->bind_param("isssi", $client, $fname, $lname, $email, $phone);
    $stmt->execute(); // Execute the SQL query
}

// Generate a unique 7-digit appointment ID
$appointmentID = generateUniqueAppointmentID($conn);

// Prepare a query to insert a row into the confirmed appointments table in the database with the corresponding info
$query = "INSERT INTO Confirmed_Appointments (Barber_ID, Client_ID, Month, Day, Year, Time, Minute, Appointment_ID, Service_ID)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Prepare the query 
$stmt = $conn->prepare($query);
if (!$stmt) { // If the query is not valid, throw error
    die(json_encode(["error" => "SQL prepare failed: " . $conn->error]));
}

// Bind parameters to put them into the SQL query
$stmt->bind_param("sissssiii", $barber, $client, $month, $day, $year, $time, $minute, $appointmentID, $service);
$stmt->execute(); // Execute the SQL query

// Close the database connections
$stmt->close();
$conn->close();

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailerMaster/src/Exception.php';
require 'PHPMailerMaster/src/PHPMailer.php';
require 'PHPMailerMaster/src/SMTP.php';

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP(); // Use SMTP
    $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server (e.g., smtp.gmail.com, smtp.sendgrid.net)
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = SMTP_USERNAME; // SMTP username (your email)
    $mail->Password = SMTP_PASSWORD; // SMTP password
    $mail->SMTPSecure = 'tls'; // Enable TLS encryption (or 'ssl' if required)
    $mail->Port = 587; // TCP port (587 for TLS, 465 for SSL)

    // Sender
    $mail->setFrom('quartetbarber@gmail.com', 'Quartet Barbershop'); // Replace with your email and name

    // Recipient
    $mail->addAddress($email, "$fname $lname"); // Add the client's email and name

    // Email content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Appointment Confirmation'; // Email subject
    $mail->Body = "
        <html>
        <head>
            <title>Appointment Confirmation</title>
        </head>
        <body>
            <h2>Hello $fname $lname,</h2>
            <p>Your appointment has been successfully scheduled!</p>
            <p><strong>Appointment ID:</strong> $appointmentID</p>
            <p><strong>Date:</strong> $month/$day/$year</p>
            <p><strong>Time:</strong> $time</p>
            <p><strong>Barber:</strong> Barber $barber</p>
            <p>Thank you for choosing our service. We look forward to seeing you!</p>
        </body>
        </html>
    ";

    // Send the email
    //$mail->SMTPDebug = 2; // Enable verbose debug output to debug SMPT email connection
    $mail->send();
    echo 'Confirmation email sent successfully!';
} catch (Exception $e) {
    echo "Failed to send email. Error: {$mail->ErrorInfo}";
}

// Redirect to the home page
header("Location: index.php");
exit(); // Ensure no further code is executed after the redirect
?>