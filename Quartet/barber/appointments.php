<!-- 
    calendar.php
    A page for the barber to view their calendar of scheduled appointments
    Author: Alexandra Stratton, Ben Renner, Brinley Hull, Jose Leyba, Kyle Moore
    Creation date: 04/13/2025
    Revisions:
      4/13/2025 - Ben, created functionality, merged existing files
      04/13/2025 -- Alexandra Stratton -- created appointments.php
-->

<?php
// Existing code...
session_start();
require 'db_connection.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$all_barbers = [];
$barber_query = "SELECT Barber_ID, First_Name, Last_Name FROM Barber_Information";
$barber_result = $conn->query($barber_query);
while ($row = $barber_result->fetch_assoc()) {
    $all_barbers[] = $row;
}
$all_clients = [];
$client_query = "SELECT Client_ID, First_Name, Last_Name FROM Client";
$client_result = $conn->query($client_query);
while ($row = $client_result->fetch_assoc()) {
    $all_clients[] = $row;
}
$selected_clients = $_GET['client_filter'] ?? ['all'];
if (!is_array($selected_clients)) {
    $selected_clients = [$selected_clients];
}
$selected_times = $_GET['time_slot_filter'] ?? ['all'];
if (!is_array($selected_times)) {
    $selected_times = [$selected_times];
}


$barber_id = $_SESSION['username'];
$selected_barber_id = $_GET['barber_filter'] ?? $barber_id;
// Set default values for filters
$current_filter = $_GET['time_filter'] ?? ''; // Default: All
$barber_filter = $_GET['barber_filter'] ?? $barber_id; // Default: Current logged-in barber
$selected_clients = $_GET['client_filter'] ?? ['all']; // Default: All clients
$selected_times = $_GET['time_slot_filter'] ?? ['all']; // Default: All time slots
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
if ($selected_barber_id === 'all') {
  $appt_query = "SELECT * FROM Confirmed_Appointments ORDER BY Month, Day, Year, Time";
  $appt_stmt = $conn->prepare($appt_query);
} else {
  $appt_query = "SELECT * FROM Confirmed_Appointments WHERE Barber_ID = ? ORDER BY Month, Day, Year, Time";
  $appt_stmt = $conn->prepare($appt_query);
  $appt_stmt->bind_param("s", $selected_barber_id);
}
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
$time_filter = $_GET['time_filter'] ?? '';
$now = new DateTime();
$today = $now->format('Y-m-d');

$appointments = array_filter($appointments, function($appt) use ($time_filter, $now, $today, $selected_clients, $selected_times) {
  $month = (int)$appt['Month'] + 1;
  $appt_date_str = sprintf('%04d-%02d-%02d', $appt['Year'], $month, $appt['Day']);
  $appt_time = sprintf('%02d:%02d', (int)$appt['Time'], (int)$appt['Minute']);
  $appt_datetime = DateTime::createFromFormat('Y-m-d H:i', "$appt_date_str $appt_time");

  if (!$appt_datetime) return false;

  // Time filter (past/present/future)
  switch ($time_filter) {
    case 'past':
        if ($appt_datetime >= $now) return false;
        break;
    case 'present':
        if ($appt_date_str !== $today) return false;
        break;
    case 'future':
        if ($appt_date_str <= $today) return false;
        break;
  }

  // Client filter
  if (!in_array('all', $selected_clients) && !in_array($appt['Client_ID'], $selected_clients)) {
      return false;
  }

  // Time slot filter
  if (!in_array('all', $selected_times)) {
      $appt_time_exact = sprintf('%02d:%02d', (int)$appt['Time'], (int)$appt['Minute']);
      if (!in_array($appt_time_exact, $selected_times)) {
          return false;
      }
  }

  return true;
});


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
    <?php
    $current_filter = $_GET['time_filter'] ?? '';
    ?>

    <form method="get" style="margin-bottom: 20px;">
      <label for="time_filter" style="margin-right: 10px;">Show Appointments:</label>
      <select name="time_filter" id="time_filter" style="margin-right: 20px;">
        <option value="" <?= $current_filter === '' ? 'selected' : '' ?>>All</option>
        <option value="past" <?= $current_filter === 'past' ? 'selected' : '' ?>>Past</option>
        <option value="present" <?= $current_filter === 'present' ? 'selected' : '' ?>>Present</option>
        <option value="future" <?= $current_filter === 'future' ? 'selected' : '' ?>>Future</option>
      </select>

      <label for="barber_filter" style="margin-right: 10px;">Barber:</label>
      <select name="barber_filter" id="barber_filter" style="margin-right: 20px;">
        <option value="all" <?= $selected_barber_id === 'all' ? 'selected' : '' ?>>All</option>
        <?php foreach ($all_barbers as $barber): ?>
          <option value="<?= htmlspecialchars($barber['Barber_ID']) ?>"
            <?= $selected_barber_id === $barber['Barber_ID'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($barber['First_Name'] . ' ' . $barber['Last_Name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="client_filter">Filter by Client:</label>
      <select name="client_filter[]" id="client_filter" multiple>
        <option value="all" <?= in_array('all', $selected_clients) ? 'selected' : '' ?>>All</option>
        <?php foreach ($all_clients as $client): ?>
          <?php
            $cid = $client['Client_ID'];
            $cname = htmlspecialchars($client['First_Name'] . ' ' . $client['Last_Name']);
            $selected = in_array($cid, $selected_clients) ? 'selected' : '';
          ?>
          <option value="<?= $cid ?>" <?= $selected ?>><?= $cname ?></option>
        <?php endforeach; ?>
      </select>

      <?php
      $selected_times = $_GET['time_slot_filter'] ?? ['all'];
      if (!is_array($selected_times)) {
          $selected_times = [$selected_times];
      }

      function generateTimeOptions() {
          $start = new DateTime('06:00');
          $end = new DateTime('20:00');
          $interval = new DateInterval('PT15M');
          $times = [];

          while ($start <= $end) {
              $times[] = $start->format('H:i');
              $start->add($interval);
          }

          return $times;
      }
      ?>

      <label for="time_slot_filter">Filter by Time:</label>
      <select name="time_slot_filter[]" id="time_slot_filter" multiple>
        <option value="all" <?= in_array('all', $selected_times) ? 'selected' : '' ?>>All</option>
        <?php foreach (generateTimeOptions() as $time): ?>
          <option value="<?= $time ?>" <?= in_array($time, $selected_times) ? 'selected' : '' ?>>
            <?= date("g:i A", strtotime($time)) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Apply Filters</button>
      
      <!-- Reset Button -->
      <a href="appointments.php?time_filter=&barber_filter=<?= $barber_id ?>&client_filter[]=all&time_slot_filter[]=all">
        <button type="button">Reset</button>
      </a>
    </form>


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
      // ✅ Existing Cancel Confirmation Popup functions
      function showConfirmPopup(appointmentId) {
        document.getElementById('cancel_id').value = appointmentId;
        document.getElementById('cancelPopup').style.display = 'flex';
      }

      function hideConfirmPopup() {
        document.getElementById('cancelPopup').style.display = 'none';
      }

      // ✅ Toggle-select dropdown functionality for both client and time filters
      document.addEventListener("DOMContentLoaded", function () {
        const clientSelect = document.getElementById("client_filter");
        const timeSelect = document.getElementById("time_slot_filter");

        // 🔁 Utility to handle toggle-select for any <select> element
        function setupToggleSelect(selectElement, displayId, label) {
          // Create or update display below the select
          function updateSelectedDisplay() {
            const selectedOptions = Array.from(selectElement.selectedOptions)
              .map(opt => opt.text)
              .filter(text => text !== 'All');
            const display = selectedOptions.join(', ') || "None";
            let selectedDisplay = document.getElementById(displayId);

            if (!selectedDisplay) {
              selectedDisplay = document.createElement("div");
              selectedDisplay.id = displayId;
              selectedDisplay.style.marginTop = "10px";
              selectElement.parentNode.insertBefore(selectedDisplay, selectElement.nextSibling);
            }

            selectedDisplay.innerText = `${label} Selected: ${display}`;
          }

          // Toggle selection on click
          selectElement.addEventListener("mousedown", function (e) {
            if (e.target.tagName === "OPTION") {
              e.preventDefault(); // stop default behavior
              const option = e.target;
              option.selected = !option.selected;
              updateSelectedDisplay();
            }
          });

          updateSelectedDisplay();
        }

        // Initialize both dropdowns
        setupToggleSelect(clientSelect, "selectedClientsDisplay", "Clients");
        setupToggleSelect(timeSelect, "selectedTimesDisplay", "Times");
      });
    </script>
  </body>
</html>
