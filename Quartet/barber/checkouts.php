<!-- 
    checkouts.php
    A page for the barber to view their recent checkouts for the day
    Author: Alexandra Stratton, Ben Renner, Brinley Hull, Jose Leyba, Kyle Moore
    Revisions:
        3/2/2025 -- Kyle Moore, add menu buttons
        04/08/2025 -- Jose Leyba -- Reworked for new Databases
    Creation date: 3/2/2025
-->

<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'db_connection.php';
require 'login_check.php';
require 'role_check.php';

$query = "
    SELECT 
        c.First_Name,
        c.Last_Name,
        MONTH(ch.Checkout_Time) AS Month,
        DAY(ch.Checkout_Time) AS Day,
        DATE_FORMAT(ch.Checkout_Time, '%H:%i') AS Time,
        ch.Checkout_Time
    FROM 
        Checkout_History ch
    JOIN 
        Client c ON ch.Client_ID = c.Client_ID
    ORDER BY 
        ch.Checkout_time DESC;
";

$result = $conn->query($query);
$checkouts = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Barber Checkouts</title>
    <link rel="stylesheet" href="style/barber_style.css">
  </head>
  
  <body>
    <div class="content-wrapper">
        <br><br>
        <p>See history of previous appointments here</p>
        <h1>Checkout History</h1>
        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Month</th>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Checkout Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($checkouts as $checkout): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($checkout['First_Name']); ?></td>
                        <td><?php echo htmlspecialchars($checkout['Last_Name']); ?></td>
                        <td><?php echo htmlspecialchars($checkout['Month']); ?></td>
                        <td><?php echo htmlspecialchars($checkout['Day']); ?></td>
                        <td><?php echo htmlspecialchars($checkout['Time']); ?></td>
                        <td><?php echo htmlspecialchars($checkout['Checkout_Time']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
  </body>
