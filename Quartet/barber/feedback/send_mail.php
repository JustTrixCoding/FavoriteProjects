<!-- 
    send_mail.php
    A php script that sends will us a mail whenever an user sends a comment. Not part of this sprint
    Authors: Alexandra Stratton, Ben Renner, Brinley Hull, Jose Leyba, Kyle Moore
    Revisions:
        03/30/2025 -- Jose Leyba -- Reworked to Happen When sending information from the Barber to the Client
    Creation date: 03/02/2025 -- Jose Leyba

    Other sources: ChatGPT
-->
<?php
require 'config.php';
require 'PHPMailerMaster/src/Exception.php';
require 'PHPMailerMaster/src/PHPMailer.php';
require 'PHPMailerMaster/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Connect to Database
$mysqli = new mysqli('sql312.infinityfree.com', 'if0_38323969', 'Quartet44', 'if0_38323969_quartet');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

$email = $_POST['email'];
$name = $_POST['name'];
$question = $_POST['question'];
$comment = $_POST['comment'];
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
    $mail->addAddress($email, $name); // Add the client's email and name

    // Email content
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = 'Feedback Response from Quartet'; // Email subject
    $mail->Body = "
        <html>
        <head>
            <title>Appointment Confirmation</title>
        </head>
        <body>
            <h2>Hello $name,</h2>
            <p><strong>We recieved your question:</strong>$question</p>
            <p><strong>Here is the response from one of our Barbers:</strong> </p>
            <p>$comment</p>
            <p>Thank you for choosing our service. We look forward to seeing you!</p>
        </body>
        </html>
    ";

    // Send the email
    //$mail->SMTPDebug = 2; // Enable verbose debug output to debug SMPT email connection
    $mail->send();
    echo 'Response email sent successfully!';

    $stmt = $mysqli->prepare("DELETE FROM questions WHERE Email = ? AND Name = ? AND Comment = ?");
    $stmt->bind_param("sss", $email, $name, $question);
    $stmt->execute();
    $stmt->close();

} catch (Exception $e) {
    echo "Failed to send email. Error: {$mail->ErrorInfo}";
}
sleep(3);
header("Location: see_feedback.php");

?>