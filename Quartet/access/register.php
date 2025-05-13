<!--
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 02/12/2025
Last modified: 02/28/2025
Purpose: Creating a new barber account
-->
<?php
//Start the session to remember user info and use it to check if the user is a Manager
session_start();
require("db_connection.php");
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$barber_id = $_SESSION['username'];
$sql = "SELECT * FROM Barber_Information WHERE Barber_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $barber_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
// Error Messaging
ini_set('display_errors', 1);
$error = "";
$success = "";

//Get barber Role
$sql = "SELECT Barber_Information.Role FROM Barber_Information WHERE Barber_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $barber_id);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();
if ($role == "Barber") {
    header("Location: login.php");
    exit();
}


$error_message = "";
// Check if the register form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Get form data
    $email = trim($_POST['email']);
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = trim($_POST['role']);

    
    //Validate input
    if (empty($role) || empty($email) || empty($fname) || empty($lname) || empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($fname) > 50) {
        $error_message = "First name must not exceed 50 characters.";
    } elseif (strlen($lname) > 50) {
        $error_message = "Last Name must not exceed 50 characters.";
    } elseif (strlen($username) > 30) {
        $error_message = "Username must not exceed 30 characters.";
    } elseif (strlen($password) > 75) {
        $error_message = "Password must not exceed 75 characters.";
    } else {
        //Attempt to secure password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        
        //Prepare SQL statement to insert data into the Barber_Information table
        $sql = "INSERT INTO Barber_Information (First_Name, Last_name, Barber_ID, Password, Role, Email) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            //Bind parameters to the prepared statement
            $stmt->bind_param("ssssss", $fname, $lname, $username, $hashed_password, $role, $email);
            
            //run the statement
            if ($stmt->execute()) {
                //Set session username and role
                $_SESSION["username"] = $username;
                $_SESSION["Role"] = $role;
                
                //Redirect to the dashboard page
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Error: " . $stmt->error;
            }
            //close the statement
            $stmt->close();
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }
    //Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Define character encoding -->
    <meta charset="UTF-8">
    <!-- Ensure proper rendering and touch zooming on mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Set the title of the page -->
    <title>Register Barber Page</title>
    <!-- Link to external CSS file -->
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <!-- Container for the registration form -->
    <div class="register-container">
        <!-- Registration form -->
        <form id="registerForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <!-- Title for the form -->
            <h2 class="register-title">Create new account</h2>
            <!-- Selection of Role -->
            <div class="register-input">
                <label for="role" class="visually-hidden">Role</label>
                <select id="role" name="role" required>
                    <option value="" disabled selected hidden>Select role</option>
                    <option value="Barber">Barber</option>
                    <option value="Manager">Manager</option>
                </select>
            </div>
            <!-- Input field for Email  -->
            <div class="register-input">  
            <input type="email" id="email" name="email" placeholder="Email Address " required>
            </div>
            <!-- Input field for first name -->
            <div class="register-input">
                <input type="text" id="fname" name="fname" placeholder="First Name" required>
            </div>
            <!-- Input field for last name -->
            <div class="register-input">
                <input type="text" id="lname" name="lname" placeholder="Last Name" required>
            </div>
            <!-- Input field for username -->
            <div class="register-input">
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <!-- Input field for password -->
            <div class="register-input">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <!-- Input field for confirming password -->
            <div class="register-input">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <!-- Submit button to sign up -->
            <button type="submit">Sign Up</button>
            <!-- Display error message if any -->
            <p id="error-message" class="error-message"><?php echo $error_message; ?></p>
        </form>

        <!-- Link to the login page for existing users -->
        <p class="login-switch">
            Already have an account?
            <a href="login.php" class="register-login">Log in</a>
        </p>
    </div>
</body>
</html>