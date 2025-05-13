<!--
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 03/32/2025
Revisions:
     03/31/2025 -- Alexandra Stratton -- created employees.php
     04/12/2025 -- Alexandra Stratton -- Removing employee
 Purpose: Allow the manager to see all the employees

 -->
 <?php
//Connects to the database
session_start();
require 'db_connection.php';
require 'login_check.php';
require 'manager_check.php';

$sql = "SELECT * FROM Barber_Information";
$result = $conn->query($sql);
$barbers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $barbers[] = $row;
    }
}
//Remove employee with manual cascade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_barber'])) {
    $barber_id = $_POST['barber_id'];
    $error = false;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete from Barber_Gallery
        $sql = "DELETE FROM Barber_Gallery WHERE Barber_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $barber_id);
        $stmt->execute();
        
        // Delete from Barber_Services
        $sql = "DELETE FROM Barber_Services WHERE Barber_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $barber_id);
        $stmt->execute();
        
        // Delete from Confirmed_Appointments
        $sql = "DELETE FROM Confirmed_Appointments WHERE Barber_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $barber_id);
        $stmt->execute();
        
        // Delete from Appointment_Availability
        $sql = "DELETE FROM Appointment_Availability WHERE Barber_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $barber_id);
        $stmt->execute();
        
        // Finally delete the barber
        $sql = "DELETE FROM Barber_Information WHERE Barber_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $barber_id);
        $stmt->execute();
        
        // Commit transaction if all queries succeeded
        $conn->commit();
        header("Location: employees.php");
        exit();
        
    } catch (Exception $e) {
        // Roll back transaction if any error occurs
        $conn->rollback();
        $error_message = "Error deleting barber: " . $e->getMessage();
        error_log($error_message);
        // You could display $error_message to the user if desired
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Title for Page -->
    <title>Employee List</title>
    <!-- Internal CSS for styling the page -->
    <link rel="stylesheet" href="style/barber_style.css">
</head>
<body>
    <div class="content-wrapper">
    <br><br>
        <div class="container">
            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>Barber ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($barbers as $barber): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($barber['Barber_ID']); ?></td>
                                <td><?php echo htmlspecialchars($barber['First_Name']) . ' ' . htmlspecialchars($barber['Last_Name']);; ?></td>
                                <td><?php echo htmlspecialchars($barber['Email']); ?></td>
                                <td><?php echo htmlspecialchars($barber['Phone_Number']); ?></td>
                                <td>
                                    <a href="edit_profile.php?Barber_ID=<?php echo $barber['Barber_ID']; ?>"><button class="edit-btn">Edit</button></a>
                                    <button class="remove-btn" onclick="confirmDelete('<?php echo $barber['Barber_ID']; ?>', '<?php echo htmlspecialchars($barber['First_Name'] . ' ' . $barber['Last_Name']); ?>')">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Delete Confirmation -->
        <div id="deleteModal" class="modal" style="display: none;">
            <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Removal</h3>
                <button class="close-btn" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div class="form-group">
                <p>Are you sure you want to remove <strong id="displayBarberName"></strong>?</p>
                <p class="warning-text">This action cannot be undone!</p>
            </div>

            <form id="deleteForm" method="POST">
                <input type="hidden" name="barber_id" id="barber_id" value="">
                <input type="hidden" name="remove_barber" value="1">
                
                <div class="modal-footer">
                    <button type="button" class="cancel-btn" onclick="closeDeleteModal()">Cancel</button>
                    <button type="submit" class="yes-btn">Yes, Delete</button>
                </div>
            </form>
        </div>
            </div>
        <!-- Script for confirming deletion -->
        <script>
            function confirmDelete(barberId, barberName) {
                document.getElementById('barber_id').value = barberId;
                document.getElementById('displayBarberName').textContent = barberName;
                document.getElementById('deleteModal').style.display = 'block';
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').style.display = 'none';
            }

            window.onclick = function(event) {
                if (event.target.classList.contains('modal')) {
                    closeDeleteModal();
                }
            }
        </script>
    </div>
</body>
</html>
