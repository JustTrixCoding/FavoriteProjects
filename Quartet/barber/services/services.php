<!--
services.php
Page to allow barbers to create, edit, delete, and select types of services.
Authors: Brinley Hull
Creation date: 04/06/2025
Revisions:
    4/23/2025 - Brinley, refactoring
Preconditions
    Acceptable inputs: all
    Unacceptable inputs: none
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

session_start();

// Connects to the database
require 'db_connection.php';

// access control
require 'login_check.php';
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

$stmt->bind_param("s", $_SESSION['username']);
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
        .add-btn {
            background: #c4454d;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .add-btn:hover {
            background: rgb(143, 48, 55);
        }
        img {
            max-width: 80px;
            height: auto;
            border-radius: 5px;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            transition: 0.3s;
        }
        .edit-btn {
            background: #007BFF;
            color: white;
        }
        .edit-btn:hover {
            background: #0056b3;
        }
        .delete-btn {
            background: #FF6A13;
            color: white;
        }
        .delete-btn:hover {
            background: #FF8A3D;
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
        .close {
            color: black;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover {
            color: black;
            cursor: pointer;
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
                <p>Your services</p>
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
                                <a href="delete_barber_service.php?Service_ID=<?php echo $service['Service_ID']; ?>"><button>Delete</button>
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
                                <a href="add_barber_service.php?Service_ID=<?php echo $service['Service_ID']; ?>"><button>Add to your services</button>
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
        </script>
    </div>
</body>
</html>