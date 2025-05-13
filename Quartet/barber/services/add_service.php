<!--
add_service.php
Purpose: Allow barbers to add the services offered
Authors: Alexandra Stratton, Jose Leyba, Brinley Hull, Ben Renner, Kyle Moore
Date: 4/10/2025
Revisions:
    4/18/2025 - Brinley Hull, make sure the redirect is to the correct page
    4/23/2025 - Brinley, refactoring
    4/26/2025 - Brinley, refactoring and fix redirect
Other Sources: ChatGPT
Preconditions
    Acceptable inputs: all
    Unacceptable inputs: none
Postconditions:
    None
Error conditions:
    Database issues
Side effects
    New entries are added to tables in the database.
Invariants
    None
Known faults:
    None
-->
<?php
session_start();
// Connects to the database
require 'db_connection.php';
require 'login_check.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve service details from the submitted form
    $service_name = $_POST['service_name'];
    $service_duration = $_POST['service_duration'];
    $service_price = $_POST['service_price'];

    // Prepares the SQL for inserting the new service into the database
    $sql = "INSERT INTO Services (Name, Price, Duration) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    // Execute the statement and check if the insertion was successful
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }

    $stmt->bind_param("sss", $service_name, $service_price, $service_duration);
    // Execute the statement and check if the insertion was successful
    if ($stmt->execute()) {
        header('Location: services_manager.php');
        exit();
    } else {
        // Display an error message if execution fails
        echo "Error executing statement: " . $stmt->error;
    }
}
?>
<head>
    <!-- Title for Page --> 
    <title>Add Service</title>
    <link rel="stylesheet" href="style/barber_style.css">
    <script src="validate.js"></script>
    <!-- Internal/External CSS for styling the page -->
    <style>
        body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }
        /* Style for the form box */
        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
            color: black;
        }
        /* Style for the labels */
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        /* Style for input boxes */
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        textarea {
            height: 150px; 
            resize: vertical; 
        }
        /* Add prodct button style */
        .add-btn {
            color: white;
            background: #c4454d;
            padding: 5px 100px;
            font-size: 18px;
            font-family: 'Georgia', serif;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        .add-btn:hover {
            background: rgb(143, 48, 55);
        }
        /* Back button style */
        .back-btn {
            color: white;
            background: #c4454d;
            padding: 15px 50px;
            font-size: 16px;
            font-family: 'Georgia',     serif;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            position: fixed;
            bottom: 20px;
            right: 20px;

        }
        .back-btn:hover {
            background: rgb(143, 48, 55);
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <!--let's user know the current page they are on-->
        <br><br>
        <h1>Add Service</h1>
        <!-- Allows barber's to add a new item to the store -->
        <div class="add-container">
        <form action="add_service.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                <label for="service_name">Service Name:</label>
                <input type="text" name="service_name" id="service_name" required onchange="validateName.call(this)">
                <span id="service_name-error" style="color: red; display: none;"></span>
                <br>
                <label for="service_price">Service Price:</label>
                <input type="number" name="service_price" id="service_price" step="0.01" required onchange="validatePrice.call(this)">
                <span id="service_price-error" style="color: red; display: none;"></span>
                <br>
                <label for="service_duration">Service Duration (minutes):</label>
                <input type='number' name="service_duration" id="service_duration" step='1' required onchange="validateDuration.call(this)"></input>
                <span id="service_duration-error" style="color: red; display: none;"></span>
                <br>
                <br>
                <button type="submit" class="add-btn">Add Service</button>
            </form>
        </div>
        <!-- Redirects to service.php page (Barber's side) -->
        <div class="back-btn">
            <a href="services_manager.php" class="back-btn"><button class="back-btn">Back to Service List</button></a>
        </div>
        <script>
            // Validate the entire form
            function validateForm(event) {
                const isNameValid = validateName.call(document.getElementById('service_name'));
                const isPriceValid = validatePrice.call(document.getElementById('service_price'));
                const isDurationValid = validateDuration.call(document.getElementById('service_duration'));

                if (!isNameValid || !isPriceValid || !isDurationValid) {
                    event.preventDefault(); // Prevent form submission
                    return false;
                }
                return true; // Allow form submission
            }
            // Attach the validateForm function to the form's submit event
            document.querySelector('form').addEventListener('submit', validateForm);
        </script>
    </div>
</body>
</html>