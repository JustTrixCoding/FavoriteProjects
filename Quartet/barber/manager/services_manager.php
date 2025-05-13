<!--
services_manager.php
Page to allow the manager to create, edit, delete, and select types of services for specific barbers.
Authors: Brinley Hull, Alexandra Stratton, Jose Leyba, Ben Renner, Kyle Moore
Creation date: 04/18/2025
Revisions:
    4/23/2025 - Brinley, refactoring
    4/26/2025 - Brinley, fix redirect
 -->

 <?php
 session_start();
// Connects to the database
require 'db_connection.php';
require 'login_check.php';

$barber_id = $_SESSION['username'];
$sql = "SELECT Role FROM Barber_Information WHERE Barber_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $barber_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['Role'] != "Manager") {
    header("Location: services.php");
    exit();
}

require 'role_check.php';

$sql = "SELECT * FROM Services";
$result = $conn->query($sql);
$services = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

$sql = "SELECT * FROM Barber_Services, Services WHERE Barber_ID = ? AND Barber_Services.Service_ID = Services.Service_ID";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "Error preparing statement: " . $conn->error;
    exit();
}

$barber = isset($_GET['barber']) ? $_GET['barber'] : $_SESSION['username'];

$stmt->bind_param("s", $barber);
$stmt->execute();
$result = $stmt->get_result();
$barber_services = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $barber_services[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Title for Page -->
    <title>Service List</title>
    <link rel="stylesheet" href="style/barber_style.css">
    <!-- Internal CSS for styling the page -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .product-container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .add-btn-container {
            text-align: right;
            margin-bottom: 20px;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            transition: 0.3s;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            color: black;
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            text-align: center;
            border-radius: 10px;
            color: black;
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
    <br><br>
        <h1>Service List</h1>
        <div class="product-container">
            <!-- Add Service Button at the Top Right -->
            <div class="add-btn-container">
                <a href="add_service.php" class="add-btn">Add Service</a>
            </div>

            
            <table>
                <label>Enter barber's username to set or retrieve services:</label>
                <input type="text" value="<?php echo $barber?>" name="barber" id="barber_username">
                <button type="button" name="retrieve" onclick="retrieveServices()">Retrieve</button>

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barber_services as $service): ?>
                        <tr>
                            <td><?php echo $service['Name']; ?></td>
                            <td>$<?php echo number_format($service['Price'], 2); ?></td>
                            <td><?php echo $service['Duration'] . " min"; ?></td>
                            <td>
                                <a href="delete_barber_service.php?Service_ID=<?php echo $service['Service_ID']; ?>&barber=<?php echo $barber?>"><button>Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- All services Table -->
            <table>
                <p>All services</p>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?php echo $service['Name']; ?></td>
                            <td>$<?php echo number_format($service['Price'], 2); ?></td>
                            <td><?php echo $service['Duration'] . " min"; ?></td>
                            <td>
                                <a href="edit_service.php?Service_ID=<?php echo $service['Service_ID']; ?>"><button class="btn edit-btn">Edit</button></a>
                            </td>
                            <td>
                                <button class="btn delete-btn" onclick="confirmDelete('<?php echo $service['Service_ID']; ?>')">Delete</button>
                            </td>
                            <td>
                                <a href="add_barber_service.php?Service_ID=<?php echo $service['Service_ID']; ?>&barber=<?php echo $barber?>"><button>Add to your services</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Delete Confirmation -->
        <div id="deleteModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Are you sure you want to remove this service?</h2>
                <button class="btn delete-btn" id="confirmDeleteBtn">Yes</button>
                <button class="btn" onclick="closeModal()">No</button>
            </div>
        <!-- Script for confirming deletion -->
        <script>
            function confirmDelete(serviceID) {
                document.getElementById('confirmDeleteBtn').setAttribute('onclick', `window.location.href='remove_service.php?Service_ID=${serviceID}'`);
                document.getElementById('deleteModal').style.display = 'block';
            }
            function closeModal() {
                document.getElementById('deleteModal').style.display = 'none';
            }
            function retrieveServices() {
                var barber = document.getElementById('barber_username').value;
                window.location.href = `?barber=${barber}`;
            }
        </script>
    </div>
</body>
</html>