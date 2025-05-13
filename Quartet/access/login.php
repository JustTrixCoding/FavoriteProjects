<!--
login.php
Description: Allows users to log in to their accounts
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 02/12/2025
Revisions: 
    02/16/2024 -- Brinley, adding session information
    02/28/25 -- Kyle using database to validate user and pass
    04/27/2025 -- Jose, Testing
    Preconditions
        Acceptable inputs: Username and Password of Barber
        Unacceptable inputs: None or Just one Field
    Postconditions:
        Barber is logged into the account. Can change the DB and store information
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
$error_message = "";
//Check if the login form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Get form data
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
    } else {
        $error_message = "Username and password are required.";
    }
    //Validate input
    if (empty($username) || empty($password)) {
        $error_message = "Username and password are required.";
    } else {
        //prepare SQL statement to fetch user from User table
        $sql = "SELECT Barber_ID, Password FROM Barber_Information WHERE Barber_ID = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            //bind parameters to the prepared statement
            $stmt->bind_param("s", $username);
            //run the statement
            $stmt->execute();
            //store the result
            $stmt->store_result();
            //check if a user with the given username exists
            if ($stmt->num_rows > 0) {
                //bind the result variables
                $stmt->bind_result($db_username, $db_password);
                //fetch the result
                $stmt->fetch();
                //verify password
                echo "Username: $username<br>";
                echo "Password: $password<br>";
                echo "Password DB: $db_password<br>";
                if (password_verify($password, $db_password)) {
                    //set session variables
                    $_SESSION["username"] = $db_username;
                    //redirect to the barber dashboard page
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error_message = "Invalid username or password.";
                }
            } else {
                $error_message = "Invalid username or password.";
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
    <!--Define character encoding-->
    <meta charset="UTF-8">
    <!--Ensure proper rendering and touch zooming on mobile devices-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Set the title of the page-->
    <title>Login Page</title>
    <!--Link to external CSS file-->
    <link rel="stylesheet" href="style/styles.css">
</head>
<body>
    <!--Container for the login form-->
    <div class="login-container">
        <!--Login form-->
        <form id="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <!--Title for the form-->
            <h2 class="login-title">Login</h2>
            <!--Input field for username-->
            <div class="login-input">
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <!--Input field for password-->
            <div class="login-input">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <a href="forgot_password.php" class="login-forgot">Forgot password?</a>
            </div>
            <div class="remember-me">
                <input type="checkbox" id="rememberMe">
                <label for="rememberMe">Remember Me</label>
            </div>
            <!--Login button-->
            <button type="submit">Login</button>
            <!--Display error message if any-->
            <p id="error-message" class="error-message"><?php echo $error_message; ?></p>
        </form>

    </div>
</body>
</html>