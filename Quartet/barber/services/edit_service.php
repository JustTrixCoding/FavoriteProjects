<!--
edit_service.php
Allow barbers to edit services in the database
Authors: Alexandra Stratton, Jose Leyba, Brinley Hull, Ben Renner, Kyle Moore
Date: 4/10/2025
Revisions:
Other Sources: ChatGPT
Preconditions
    Acceptable inputs: all
    Unacceptable inputs: none
Postconditions:
    None
Error conditions:
    Database issues
Side effects
    Entries in tables in the database are altered.
Invariants
    None
Known faults:
    None
-->
<?php
//Connects to the database
session_start();
require 'db_connection.php';
require 'login_check.php';

if (isset($_GET['Service_ID'])) {
    //Gets the service id
    $service_id = $_GET['Service_ID'];
    //Retrieves all the information from the give service_id
    $sql = "SELECT * FROM Services WHERE Service_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $service = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Retrieve service details from the submitted form
        $service_name = $_POST['service_name'];
        $service_price = $_POST['service_price'];
        $service_duration = $_POST['service_duration'];
        // Prepares the sql for updating the database
        $sql = "UPDATE Services SET Name = ?, Price = ?, Duration = ? WHERE Service_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $service_name, $service_price, $service_duration, $service_id);
        // Execute the statement and check if the update was successful
        if ($stmt->execute()) {
            header('Location: services_manager.php?barber=' . $barber);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
} else {
    echo "Service not found.";
    exit();
}
?>

<head>
    <!-- Title for Page -->
    <title>Edit Service</title>
    <script src="validate.js"></script>
    <!-- Internal CSS for styling the page -->
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

        /* Style for inputing a file */
        .file-input-container {
            position: relative;
            margin-top: 10px;
        }

        .file-input-container input[type="file"] {
            opacity: 0;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-label {
            display: inline-block;
            padding: 10px 20px;
            background-color: rgba(36, 35, 35);
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .file-input-label:hover {
            background-color: rgba(36, 35, 35);
        }

        /* Image preview container */
        .image-preview-wrapper {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
        }

        /* Individual image preview styling */
        .image-preview-box {
            width: 48%;
            text-align: center;
        }

        .image-preview {
            max-width: 100%;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }

        .current-image-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .new-image-label {
            font-weight: bold;
            margin-bottom: 5px;
            display: none;
            /* Hidden by default */
        }

        /* Update the service button style */
        .update-btn {
            color: white;
            background: #c4454d;
            padding: 5px 100px;
            font-size: 18px;
            font-family: 'Georgia', serif;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        .update-btn:hover {
            background: rgb(143, 48, 55);
        }

        /* Back button style */
        .back-btn {
            color: white;
            background: #c4454d;
            padding: 15px 50px;
            font-size: 16px;
            font-family: 'Georgia', serif;
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
    <!--let's user know the current page they are on-->
    <h1>Edit Service</h1>
    <!-- Allows barber's to add a new item to the store -->
    <div class="edit-container">
        <form action="edit_service.php?Service_ID=<?php echo $service['Service_ID']; ?>" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <label for="service_name">Service Name:</label>
            <input type="text" name="service_name" id="service_name" value="<?php echo $service['Name']; ?>" required onchange="validateName.call(this)">
            <span id="service_name-error" style="color: red; display: none;"></span>
            <br>
            <label for="service_price">Service Price:</label>
            <input type="number" name="service_price" id="service_price" step="0.01" value="<?php echo $service['Price']; ?>" required onchange="validatePrice.call(this)">
            <span id="service_price-error" style="color: red; display: none;"></span>
            <br>
            <label for="service_duration">Service Duration:</label>
            <input type="number" name="service_duration" id="service_duration" step="1" value="<?php echo $service['Duration']; ?>" required onchange="validateDuration.call(this)">
            <span id="service_duration-error" style="color: red; display: none;"></span>
            <br>

            <br>


            <button type="submit" class="update-btn">Update Service</button>
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
        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the form validation
            document.querySelector('form').addEventListener('submit', validateForm);
        });
    </script>
</body>

</html>