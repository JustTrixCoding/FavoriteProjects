<!--
forgot_password.php
Description: Allows users to recover their account if they have forgotten their password using a token
Authors: Jose Leyba
Date: 04/26/2025
Revisions: 
    Preconditions
        Acceptable inputs: Email of the Barber
        Unacceptable inputs: None
    Postconditions:
        Barber gets an email with a token for password recovery
    Error conditions:
        DB Issues, PHPmailer issues
    Side effects
        None
    Invariants
        None
    Known faults:
        None
-->
<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1); 
session_start();
include("db_connection.php");
require 'config.php';
require 'PHPMailerMaster/src/Exception.php';
require 'PHPMailerMaster/src/PHPMailer.php';
require 'PHPMailerMaster/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$error_message = "";
//Check if the login form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');

    if (!empty($email) && strlen($email) <= 50) {
        $sql = "SELECT Barber_ID FROM Barber_Information WHERE Email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($barberID);
                $stmt->fetch();

                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', time() + 3600); // valid for 1 hour

                $update = $conn->prepare("UPDATE Barber_Information SET Reset_Token = ?, Token_Expiry = ? WHERE Email = ?");
                $update->bind_param("sss", $token, $expiry, $email);
                $update->execute();

                $resetLink = "$token";

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = SMTP_USERNAME;
                    $mail->Password = SMTP_PASSWORD;
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('quartetbarber@gmail.com', 'Quartet Barbershop');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset Request';
                    $mail->Body = "
                        <h2>Password Reset Request</h2>
                        <p>If you requested a password reset, To reset the password enter the following token on the Quartet Website:</p>
                        <p>$resetLink</p>
                        <p>If you haven't requested a password reset ignore this email</p>
                        <p>This link will expire in 1 hour.</p>
                    ";

                    $mail->send();
                } catch (Exception $e) {
                    $error_message = "Mailer Error: " . $mail->ErrorInfo;

                }
            }
        }
    }
    $conn->close();
    header("Location: reset_password.php");
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!--Define character encoding-->
    <meta charset="UTF-8">
    <!--Ensure proper rendering and touch zooming on mobile devices-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Set the title of the page-->
    <title>Forgot Password</title>
    <!--Link to external CSS file-->
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <!--Container for the login form-->
    <div class="login-container">
        <!--Login form-->
        <form id="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <!--Title for the form-->
            <h2 class="login-title">Forgot Password?</h2>
            <!--Input field for username-->
            <div class="login-input">
                <input type="email" id="email" name="email" placeholder="Enter your Email Address" required>
            </div>
            <!--Login button-->
            <button type="submit">Send Mail for Resetting Password</button>
            <!--Display error message if any-->
            <p id="error-message" class="error-message"><?php echo htmlspecialchars($error_message); ?></p>

        </form>

    </div>
</body>
</html>