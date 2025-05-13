<!--
reset_password.php
Description: Allows users to recover their account if they have forgotten their password using a token
Authors: Jose Leyba
Date: 04/26/2025
Revisions: 
    Preconditions
        Acceptable inputs: Token recieved in email, password, confirm password
        Unacceptable inputs: None or less than 3 fields
    Postconditions:
        Barber passwords gets changed
    Error conditions:
        DB Issues
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

$error_message = '';
$success_message = '';
$show_form = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = trim($_POST['token'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($token)) {
        $error_message = "Please enter the reset token.";
        $show_form = true;
    } else {
        $sql = "SELECT Email, Token_Expiry FROM Barber_Information WHERE Reset_Token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($email, $expiry);
            $stmt->fetch();

            if (strtotime($expiry) < time()) {
                $error_message = "This reset link has expired.";
                $show_form = false;
            } else {
                if (strlen($new_password) < 6) {
                    $error_message = "Password must be at least 6 characters.";
                }
                elseif (strlen($new_password) > 75) {
                    $error_message = "Password must be less than 75 characters.";
                } elseif ($new_password !== $confirm_password) {
                    $error_message = "Passwords do not match.";
                } else {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    $update = $conn->prepare("UPDATE Barber_Information SET Password = ?, Reset_Token = NULL, Token_Expiry = NULL WHERE Reset_Token = ?");
                    $update->bind_param("ss", $hashed_password, $token);

                    if ($update->execute()) {
                        $success_message = "Password successfully updated! You can now <a href='login.php'>log in</a>.";
                        $show_form = false;
                    } else {
                        $error_message = "Error updating password. Please try again.";
                    }
                }
            }
        } else {
            $error_message = "Invalid or expired reset token.";
            $show_form = false;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Reset Your Password</h2>

        <?php if ($error_message): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <?php if ($show_form): ?>
            <form method="POST">
                <div class="login-input">
                    <input type="text" name="token" placeholder="Enter Reset Token" required>
                </div>

                <div class="login-input">
                    <input type="password" name="new_password" placeholder="New Password" required>
                </div>
                <div class="login-input">
                    <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                </div>

                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
