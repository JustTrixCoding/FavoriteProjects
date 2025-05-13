<!--
client.php
Page displays a list of all clients with search functionality
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 03/30/2025
Revisions:
    03/31/2025 -- Alexandra Stratton -- created employee_list.php
    04/10/2025 -- Alexandra Stratton -- removed the styling, added
    04/26/2025 -- Alexandra Stratton -- Error Checking
Preconditions
    Acceptable inputs: All
    Unacceptable inputs: None
    Required Access: User must be logged in and have appropriate role permissions
Postconditions:
    None
Error conditions:
    Database issues
Side effects
    None
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
require 'role_check.php';

if (!isset($conn)) {
    die("No database connection");
}
$sql = "SELECT * FROM Client";
$result = $conn->query($sql);
if (!$result) {
    die("Database error");
}
$clients = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title for Page -->
    <title>Client List</title>
    <!-- Internal CSS for styling the page -->
    <link rel="stylesheet" href="style/barber_style.css">
</head>

<body>
    <div class="content-wrapper">
    <br><br>
        <h1>Client List</h1>
        <div class="search-bar">
            <input type="text" id="search-input" placeholder="Search by...">
        </div>
        <div class="container">
            <div class="card">
                <table id="clientTable"> 
                    <thead>
                        <tr>
                            <th>Client ID</th>
                            <th>Client Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($client['Client_ID']); ?></td>
                                <td><?php echo htmlspecialchars($client['First_Name']) . ' ' . htmlspecialchars($client['Last_Name']); ?></td>
                                <td><?php echo htmlspecialchars($client['Email']); ?></td>
                                <td><?php echo htmlspecialchars($client['Phone']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            // JavaScript for filtering the table - fixed version
            document.getElementById("search-input").addEventListener("input", function() {
                const filter = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll("#clientTable tbody tr");

                rows.forEach(row => {
                    let match = false;
                    const cells = row.querySelectorAll("td");

                    cells.forEach(cell => {
                        if (cell.textContent.toLowerCase().includes(filter)) {
                            match = true;
                        }
                    });

                    row.style.display = match ? "" : "none";
                });
            });
        </script>
    </div>
</body>

</html>