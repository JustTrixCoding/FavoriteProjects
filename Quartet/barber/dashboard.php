<!-- 
    dashboard.php
    A page for the barber to have an overview for the day which includes revenue and schedule
    Author: Alexandra Stratton, Ben Renner, Brinley Hull, Jose Leyba, Kyle Moore
    Revisions:
        3/2/2025 -- Kyle Moore, add menu buttons
        4/23/2025 - Brinley, refactoring
    Creation date: 3/2/2025
-->

<?php
session_start();
require 'db_connection.php';
require 'login_check.php';
require 'role_check.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle checkout action
if (isset($_POST['checkout'])) {
    $client_id = $_POST['client_id'];
    $appointment_id = $_POST['appointment_id'];
    
    $stmt = $conn->prepare("INSERT INTO Checkout_History (Client_ID, Appointment_ID) VALUES (?, ?)");
    $stmt->bind_param("is", $client_id, $appointment_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM Confirmed_Appointments WHERE Appointment_ID = ?");
    $stmt->bind_param("s", $appointment_id);
    $stmt->execute();
    $stmt->close();
    
    // Refresh the page to reflect changes
    header("Location: dashboard.php");
    exit();
}
// Get today's appointments
$today_day = date('j'); // Current day of the month
$today_month = date('n') - 1; // Current month as a number
$today_year = date('Y'); // Current year

$sql = "SELECT Confirmed_Appointments.*, Client.First_Name, Client.Last_Name, Client.Email, Client.Phone 
          FROM Confirmed_Appointments
          JOIN Client ON Confirmed_Appointments.Client_ID = Client.Client_ID
          WHERE Confirmed_Appointments.Barber_ID = ? 
          AND Confirmed_Appointments.Day = ? 
          AND Confirmed_Appointments.Month = ? 
          AND Confirmed_Appointments.Year = ?
          ORDER BY Confirmed_Appointments.Time ASC, Confirmed_Appointments.Minute ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("siii", $barber_id, $today_day, $today_month, $today_year);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);

$sql = "SELECT * FROM Store";
$result = $conn->query($sql);
$store = [];
if ($result->num_rows > 0) {
    $store = $result->fetch_assoc();
}
$store_id = isset($store['Store_ID']) ? $store['Store_ID'] : null;
$store_hours = [];
if ($store_id) {
    $sql = "SELECT *
            FROM Store_Hours
            WHERE Store_ID = ? ORDER BY FIELD(Day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $store_id);
    $stmt->execute();
    $hours_result = $stmt->get_result();
    if ($hours_result->num_rows > 0) {
        while ($row = $hours_result->fetch_assoc()) {
            $store_hours[$row['Day']] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="style/barber_style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Barber Dashboard</title>
  </head>
  <body>
    <div class="content-wrapper">
        <h1>Today's Schedule (<?php echo date(' F j, Y'); ?>)</h1>
        <?php if (empty($appointments)): ?>
            <div class="no-appointments">
                <p>No appointments scheduled for today.</p>
            </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Contact</th>
                            <th>Time</th>
                            <th>Service</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appt): 
                            $time = $appt['Time'] . ':' . str_pad($appt['Minute'], 2, '0', STR_PAD_LEFT);
                            ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($appt['First_Name'] . ' ' . $appt['Last_Name']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($appt['Phone']); ?><br>
                                    <?php echo htmlspecialchars($appt['Email']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($time); ?></td>
                                <td><?php echo htmlspecialchars($appt['Service_ID']); ?></td>
                                <td>
                                    <form method="post" action="dashboard.php">
                                        <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appt['Appointment_ID']); ?>">
                                        <input type="hidden" name="client_id" value="<?php echo htmlspecialchars($appt['Client_ID']); ?>">
                                        <button type="submit" name="checkout">Checkout</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <div class="container">
            <div>
                <h2>Store Info</h2>
                <?php if ($store): ?>
                    <p>
                        <strong>Store Name: </strong> 
                        <?php echo htmlspecialchars($store['Name']); ?> 
                    </p>
                    <p>
                        <strong>Location: </strong> <br>
                        <?php echo htmlspecialchars($store['Address']); ?> <br>
                        <?php echo htmlspecialchars($store['City'] . ', ' . $store['State'] . ' ' . $store['Zip_Code']); ?>

                    </p>
                    <p>
                        <strong>Phone: </strong>
                        <?php echo htmlspecialchars($store['Phone']); ?>
                    </p>
                    <p>
                        <strong>Email: </strong>
                        <?php echo htmlspecialchars($store['Email']); ?>
                    </p>
                    <p>
                        <?php if(!empty($store['Facebook'])): ?>
                            <strong>Facebook: </strong>
                            <?php echo htmlspecialchars($store['Facebook']); ?>
                        <?php endif; ?>
                    </p>
                    <p>
                        <?php if(!empty($store['Facebook'])): ?>
                            <strong>Facebook: </strong>
                            <?php echo htmlspecialchars($store['Facebook']); ?>
                            <a href="https://www.facebook.com/<?php echo htmlspecialchars($store['Facebook']); ?>" target="_blank">Link</a>
                        <?php endif; ?>
                    </p>
                    <p>
                        <?php if(!empty($store['Instagram'])): ?>
                            <strong>Instagram: </strong> 
                            <?php echo htmlspecialchars($store['Instagram']); ?>
                            <a href="https://www.instagram.com/<?php echo htmlspecialchars($store['Instagram']); ?>" target="_blank">Link</a>
                        <?php endif; ?>
                    </p>
                    <p>
                        <?php if(!empty($store['TikTok'])): ?>
                            <strong>TikTok: </strong> 
                            <?php echo htmlspecialchars($store['TikTok']); ?>
                            <a href="https://www.tiktok.com/@<?php echo htmlspecialchars($store['TikTok']); ?>" target="_blank">Link</a>
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <p>No store information available</p>
                <?php endif; ?>
            </div>
            <div>
                <h2>Store Hours</h2>
                <?php if (!empty($store_hours)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $daysOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            foreach ($daysOrder as $day): 
                                $hours = isset($store_hours[$day]) ? $store_hours[$day] : null;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($day); ?></td>
                                    <td>
                                        <?php if ($hours && !$hours['Is_Closed']): ?>
                                            <?php 
                                                $openTime = date("g:i a", strtotime($hours['Open_Time']));
                                                $closeTime = date("g:i a", strtotime($hours['Close_Time']));
                                                echo htmlspecialchars("$openTime - $closeTime");
                                            ?>
                                        <?php else: ?>
                                            <span class="closed-day">Closed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No store hours available</p>
                <?php endif; ?>
                
            </div>
            <a href="store_info.php" class="change-btn">Change</a>
        </div>
    </div>
  </body>
</html>
