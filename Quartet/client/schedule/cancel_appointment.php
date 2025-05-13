<?php
// Start the session
session_start();
include('header.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Appointment</title>
    <link rel="stylesheet" href="style/style1.css">

    <style>
        body {
            font-family: 'Georgia', serif; 
        }
        .info_form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }
        input[type="number"] {
            width: 90%;
        }
        button {
            background-color: #c4454d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
            button:hover {
            background-color: rgb(143, 48, 55);
        }

        #appointmentDetails {
            margin-top: 15px;
            font-size: 16px;
        }
        #cancelButton {
            display: none; /* Initially hidden */
            margin: 20px auto; /* Center the button */
            text-align: center;
            width: fit-content;
        }

    </style>
    <script> 
        function fetchAppointment() { //used to fetch appointments when user enters an appointment id
            let appointmentID = document.getElementById("appointmentID").value;
            if (appointmentID === "") {
                document.getElementById("appointmentDetails").innerHTML = "<p style='color: red;'>❌ Please enter an Appointment ID.</p>";
                document.getElementById("cancelButton").style.display = "none"; // Hide cancel button if no ID
                return;
            }

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "fetch_appointment.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("appointmentDetails").innerHTML = xhr.responseText;
                    if (!xhr.responseText.includes("❌ No appointment found")) {
                        document.getElementById("cancelButton").style.display = "block"; // Show cancel button if appointment found
                        document.getElementById("hiddenAppointmentID").value = appointmentID; // Pass ID to cancel form
                    } else {
                        document.getElementById("cancelButton").style.display = "block"; 
                        document.getElementById("cancelButton").style.display = "none"; // Hide cancel button if no appointment found
                    }
                }
            };
            xhr.send("appointmentID=" + appointmentID);

        }
    </script>
</head>
<body>

    <h1>Cancel Appointment</h1>

    <div class="info_form">
        <label for="appointmentID">Appointment ID:</label><br>
        <input type="number" id="appointmentID" name="appointmentID" required><br><br>
        
        <button type="button" onclick="fetchAppointment()">Find Appointment</button>
        
        <div id="appointmentDetails"></div> <!-- Appointment info will appear here -->

        <form action="cancel.php" method="POST">
            <input type="hidden" id="hiddenAppointmentID" name="appointmentID">
            <button type="submit" id="cancelButton">Cancel Appointment</button>
        </form>
    </div>

</body>
</html>
