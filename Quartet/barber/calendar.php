<!-- 
    calendar.php
    A page for the barber to view their calendar of scheduled appointments
    Author: Alexandra Stratton, Ben Renner, Brinley Hull, Jose Leyba, Kyle Moore
    Revisions:
        3/2/2025 -- Kyle Moore, add menu buttons
    Creation date: 3/2/2025
    Revisions:
      4/13/2025 - Ben, created functionality
-->

<?php
// Existing code...
session_start();
require 'db_connection.php';
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

// Handle appointment cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_cancel'])) {
    $cancel_id = $_POST['cancel_id'];

    $delete_stmt = $conn->prepare("DELETE FROM Confirmed_Appointments WHERE Appointment_ID = ?");
    $delete_stmt->bind_param("i", $cancel_id);
    if ($delete_stmt->execute()) {
        $success = "Appointment cancelled successfully.";
    } else {
        $error = "Failed to cancel appointment.";
    }
}
// Error Messaging
ini_set('display_errors', 1);
$error = "";
$success = "";

// ✅ Get all confirmed appointments for the logged-in barber
$appointments = [];
$appt_query = "SELECT * FROM Confirmed_Appointments WHERE Barber_ID = ? ORDER BY Month, Day, Year, Time";
$appt_stmt = $conn->prepare($appt_query);
$appt_stmt->bind_param("s", $barber_id);
$appt_stmt->execute();
$appt_result = $appt_stmt->get_result();

while ($row = $appt_result->fetch_assoc()) {
    // Get client details using Client_ID
    $client_id = $row['Client_ID'];
    $client_query = "SELECT First_Name, Last_Name, Email, Phone FROM Client WHERE Client_ID = ?";
    $client_stmt = $conn->prepare($client_query);
    $client_stmt->bind_param("s", $client_id);
    $client_stmt->execute();
    $client_result = $client_stmt->get_result();
    $client_info = $client_result->fetch_assoc();

    // Add client info to the appointment array
    if ($client_info) {
        $row['Client_First'] = $client_info['First_Name'];
        $row['Client_Last'] = $client_info['Last_Name'];
        $row['Client_Email'] = $client_info['Email'];
        $row['Client_Phone'] = $client_info['Phone'];
    }

    // Get service details using Service_ID
    $service_id = $row['Service_ID'];
    $service_query = "SELECT Name, Duration FROM Services WHERE Service_ID = ?";
    $service_stmt = $conn->prepare($service_query);
    $service_stmt->bind_param("s", $service_id);
    $service_stmt->execute();
    $service_result = $service_stmt->get_result();
    $service_info = $service_result->fetch_assoc();

    // Add service info to the appointment array
    if ($service_info) {
        $row['Service_Name'] = $service_info['Name'];
        $row['Service_Duration'] = $service_info['Duration'];
    }

    $appointments[] = $row;
}
?>
<?php
if ($user['Role'] == "Barber") {
    include("barber_header.php");
} else {
    include("manager_header.php");
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Appointments</title>
    <link rel="stylesheet" href="style/barber_style.css">
  </head>
  <body>
    <h1>Appointments</h1>
    <!-- ✅ Display appointments in a table -->
    <?php if (count($appointments) > 0): ?>
      <div class="container">
        <div class="card">
          <table border="1" cellpadding="10" cellspacing="0">
            <thead>
              <tr>
                <th>Client Info</th>
                <th>Service Info</th>
                <th>Date</th>
                <th>Time</th>
                <th>Completed</th>
                <th>Cancel</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($appointments as $appt): ?>
                <tr>
                <td><!-- Client Section -->
                  <?php
                    echo "Name: " . htmlspecialchars($appt['Client_First'] . ' ' . $appt['Client_Last']) . "<br>";
                    echo "Email: " . htmlspecialchars($appt['Client_Email']) . "<br>";
                    echo "Phone: " . htmlspecialchars($appt['Client_Phone']);
                  ?>
                </td>
                <td><!-- Service Section -->
                  <?php
                    echo "Name: " . htmlspecialchars($appt['Service_Name']) . "<br>";
                    echo "Duration: " . htmlspecialchars($appt['Service_Duration']) . " mins";
                  ?>
                </td>
                  <td><!-- Date Section -->
                    <?php
                      // Optional: convert numeric month to full name
                      $month = (int)$appt['Month'] + 1;
                      $date = DateTime::createFromFormat('!m-d-Y', $month . '-' . $appt['Day'] . '-' . $appt['Year']);
                      echo $date ? $date->format('F j, Y') : "{$month}/{$appt['Day']}/{$appt['Year']}";
                    ?>
                  </td>
                  <td> <!-- Time Section -->
                    <?php
                      $hour = (int)$appt['Time']; // convert string to integer
                      $minute = (int)$appt['Minute'];
                      $formatted_time = date("g:i A", strtotime(sprintf("%02d:%02d", $hour, $minute)));
                      echo $formatted_time;
                    ?>
                  </td>
                  <td style="text-align: center;"> <!-- Completed Section --> 
                  <?php
                    $hour = (int)$appt['Time'];
                    $minute = (int)$appt['Minute'];
                    $month = (int)$appt['Month'] + 1; // fix: month starts at 0 in database
                    $appt_datetime = DateTime::createFromFormat('Y-m-d H:i', sprintf(
                        '%04d-%02d-%02d %02d:%02d',
                        $appt['Year'],
                        $month,
                        $appt['Day'],
                        $hour,
                        $minute
                    ));
                    $now = new DateTime();

                    echo $appt_datetime < $now ? '✅' : '❌';
                  ?>
                  </td>
                  <td style="text-align: center;">
                    <button onclick="showConfirmPopup(<?php echo $appt['Appointment_ID']; ?>)">Cancel</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No appointments scheduled.</p>
        <?php endif; ?>
      </div>
    </div>
    <!-- Cancel Confirmation Popup -->
    <div id="cancelPopup" style="display: none; position: fixed; top: 0; left: 0;
        width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); 
        align-items: center; justify-content: center;">
      <div style="background: white; padding: 30px; border-radius: 10px; text-align: center;">
        <p>Are you sure you want to cancel this appointment?</p>
        <form method="post">
          <input type="hidden" name="cancel_id" id="cancel_id" value="">
          <button type="button" onclick="hideConfirmPopup()">No, Back</button>
          <button type="submit" name="confirm_cancel">Yes, Cancel</button>
        </form>
      </div>
    </div>

    <script>
      function showConfirmPopup(appointmentId) {
        document.getElementById('cancel_id').value = appointmentId;
        document.getElementById('cancelPopup').style.display = 'flex';
      }

      function hideConfirmPopup() {
        document.getElementById('cancelPopup').style.display = 'none';
      }
    </script>
  </body>
</html>
