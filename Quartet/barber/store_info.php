<!--
store_info.php
Page to allow barbers/managers to update information about the store and its operating hours
Authors: Alexandra, Jose, Brinley, Ben, Kyle
Date: 03/30/2025
Revisions:
    04/10/2025 -- Alexandra Stratton -- created store_info.php
    04/10/2025 -- Alexandra Stratton -- Fix the errors
    04/26/2025 -- Alexandra Stratton -- Error Checking
Sources:
    -- ChatGPT
Preconditions
    Acceptable inputs: Valid Store and Store_Hours data 
    Unacceptable inputs: Invalid store data
    Required Access: User must be logged in and have appropriate role permissions
Postconditions:
    Updates the Store and Store_Hours database tables
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
require 'db_connection.php';
require 'login_check.php';
require 'role_check.php';

if (!isset($conn)) {
    die("No database connection");
}
$sql = "SELECT * FROM Store LIMIT 1";
$result = $conn->query($sql);
if (!$result) {
    die("Failed to load store information");
}
$store = [];
if ($result && $result->num_rows > 0) {
    $store = $result->fetch_assoc();
}


$store_hours = [];
if (!empty($store)) {
    $store_id = $store['Store_ID'];
    $sql = "SELECT *
            FROM Store_Hours
            WHERE Store_ID = ? ORDER BY FIELD(Day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Failed to prepare store hours query");
    }
    
    if (!$stmt->bind_param("i", $store_id)) {
        die("Failed to bind store ID parameter");
    }
    
    if (!$stmt->execute()) {
        die("Failed to execute store hours query");
    }
    $hours_result = $stmt->get_result();
    if ($hours_result->num_rows > 0) {
        while ($row = $hours_result->fetch_assoc()) {
            $store_hours[$row['Day']] = $row;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['store_information'])) {
        $store_name = isset($_POST['store_name']) ? $conn->real_escape_string($_POST['store_name']) : '';
        $address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';
        $city = isset($_POST['city']) ? $conn->real_escape_string($_POST['city']) : '';
        $state = isset($_POST['state']) ? $conn->real_escape_string($_POST['state']) : '';
        $zip = isset($_POST['zip']) ? $conn->real_escape_string($_POST['zip']) : '';
        $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : '';
        $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
        $facebook = isset($_POST['facebook']) ? $conn->real_escape_string($_POST['facebook']) : '';
        $instagram = isset($_POST['instagram']) ? $conn->real_escape_string($_POST['instagram']) : '';
        $tiktok = isset($_POST['tiktok']) ? $conn->real_escape_string($_POST['tiktok']) : '';
        if (!empty($store)) {
            $sql = "UPDATE Store SET Name=?, Address=?, City=?, State=?, Zip_Code=?, Phone=?, Email=?, Facebook=?, Instagram=?, TikTok=? WHERE Store_ID=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssssi", $store_name, $address, $city, $state, $zip, $phone, $email, $facebook, $instagram, $tiktok, $store['Store_ID']);
            if ($stmt->execute()) {
                $sql = "SELECT * FROM Store LIMIT 1";
                $result = $conn->query($sql);
                if ($result && $result->num_rows > 0) {
                    $store = $result->fetch_assoc();
                }
               
            }
        } else {
            $sql = "INSERT INTO Store (Name, Address, City, State, Zip_Code, Phone, Email, Facebook, Instagram, TikTok) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssss", $store_name, $address, $city, $state, $zip, $phone, $email, $facebook, $instagram, $tiktok);
            $stmt->execute();
        }
    }
    if (isset($_POST['store_hours'])) {
        if (empty($store) || empty($store['Store_ID'])) {
            $_SESSION['error'] = "Please save store information before setting hours";
        } else {
            $store_id = $store['Store_ID'];
            
            foreach ($_POST['hours'] as $day => $data) {
                $is_closed = isset($data['is_closed']) ? 1 : 0;
                
                $open_time = ($is_closed) ? NULL : (!empty($data['open_time']) ? $data['open_time'] : NULL);
                $close_time = ($is_closed) ? NULL : (!empty($data['close_time']) ? $data['close_time'] : NULL);
                
                $check_sql = "SELECT Hours_ID FROM Store_Hours WHERE Store_ID = ? AND Day = ?";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("is", $store_id, $day);
                $check_stmt->execute();
                $result = $check_stmt->get_result();
                $exists = $result->fetch_assoc();
                
                if ($exists) {
                    $sql = "UPDATE Store_Hours SET 
                            Open_Time = ?, 
                            Close_Time = ?, 
                            Is_Closed = ? 
                            WHERE Hours_ID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssii", $open_time, $close_time, $is_closed, $exists['Hours_ID']);
                } else {
                    $sql = "INSERT INTO Store_Hours 
                            (Store_ID, Day, Open_Time, Close_Time, Is_Closed) 
                            VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isssi", $store_id, $day, $open_time, $close_time, $is_closed);
                }
                
                if (!$stmt->execute()) {
                    $_SESSION['error'] = "Error saving hours for ".$day.": ".$stmt->error;
                    error_log("Database error: " . $stmt->error);
                    break; 
                }
            }
            
            if (!isset($_SESSION['error'])) {
                
                $sql = "SELECT * FROM Store_Hours WHERE Store_ID = ? ORDER BY FIELD(Day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $store_id);
                $stmt->execute();
                $hours_result = $stmt->get_result();
                $store_hours = [];
                while ($row = $hours_result->fetch_assoc()) {
                    $store_hours[$row['Day']] = $row;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Title for Page -->
        <title>Store Information</title>
        <!-- Internal CSS for styling the page -->
        <link rel="stylesheet" href="style/barber_style.css">
        <style>
            .error {
                color: #dc3545;
                font-size: 0.875em;
                margin-top: 0.25rem;
                display: block;
            }

            #hours-errors p {
                margin: 0.25rem 0;
                color: red;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="store-info-form">
                <div class="store-info-form">
                    <form method="POST" enctype="multipart/form-data" id="store_information">
                        <div class="form-header">
                            <h2>Store Information</h2>
                        </div>
                        <div class="form-group">
                            <label for="store_name">Store Name:</label>
                            <input type="text" name="store_name" id="store_name" placeholder="Enter the store's name"
                                value="<?php echo htmlspecialchars($store['Name']); ?>" required onchange="validateName()">
                            <span id="store_name_error" class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone #:</label>
                            <input type="tel" name="phone" id="phone" placeholder="Enter phone number"  maxlength="10"
                                value="<?php echo htmlspecialchars($store['Phone']); ?>" required onchange="validateEmail()">
                                <span id="phone_error" class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" name="email" id="email" placeholder="Enter email"
                                value="<?php echo htmlspecialchars($store['Email']); ?>" required onchange="validatePhone()">
                                <span id="email_error" class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" name="address" id="address" placeholder="Enter address"
                                value="<?php echo htmlspecialchars($store['Address']); ?>" required onchange="validateAddress()">
                            <span id="address_error" class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="city">City:</label>
                            <input type="text" name="city" id="city" placeholder="Enter city"
                                value="<?php echo htmlspecialchars($store['City']); ?>" required onchange="validateCity()">
                            <span id="city_error" class="error"></span>
                        </div>
                        <div class="form-group">    
                            <label for="state">State:</label>
                            <select name="state" id="state" class="form-control" required>
                                <?php
                            $states = ['AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 
                            'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 
                            'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'];
                            foreach ($states as $abbr) {
                                    $selected = ($store['State'] == $abbr) ? 'selected' : '';
                                    echo "<option value='$abbr' $selected>$abbr</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="zip">Zip Code:</label>
                            <input type="text" name="zip" id="zip" placeholder="Enter zip code"
                                value="<?php echo htmlspecialchars($store['Zip_Code']); ?>" required onchange="validateZip()">
                            <span id="zip_error" class="error"></span>
                        </div>
                        <div class="form-group">
                            <label for="facebook">Facebook:</label>
                            <input type="text" name="facebook" id="facebook" placeholder="Enter Facebook handle"
                                value="<?php echo isset($store['Facebook']) ? htmlspecialchars($store['Facebook']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="instagram">Instagram:</label>
                            <input type="text" name="instagram" id="instagram" placeholder="Enter Instagram handle"
                                value="<?php echo isset($store['Instagram']) ? htmlspecialchars($store['Instagram']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="tiktok">TikTok:</label>
                            <input type="text" name="tiktok" id="tiktok" placeholder="Enter TikTok handle"
                            value="<?php echo isset($store['TikTok']) ? htmlspecialchars($store['TikTok']) : ''; ?>">
                        </div>
                        <button type="submit" name="store_information" class="save-btn">Save</button>
                    </form>
                </div>
                <div class="store-hours-form">
                    <form method="POST" enctype="multipart/form-data" id="store_hours">
                        <h2>Store Hours</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Open Time</th>
                                    <th>Closed Time</th>
                                    <th>Closed</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                foreach ($days as $day) {
                                    $hours_data = isset($store_hours[$day]) ? $store_hours[$day] : null;
                                    $open_time = $hours_data ? $hours_data['Open_Time'] : '';
                                    $close_time = $hours_data ? $hours_data['Close_Time'] : '';
                                    $is_closed = $hours_data ? $hours_data['Is_Closed'] : 0;
                                ?>
                                <tr>
                                    <td><?php echo $day; ?></td>
                                    <td>
                                        <input type="time" name="hours[<?php echo $day; ?>][open_time]" 
                                            value="<?php echo htmlspecialchars($open_time); ?>" 
                                            <?php echo $is_closed ? 'disabled' : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="time" name="hours[<?php echo $day; ?>][close_time]" 
                                            value="<?php echo htmlspecialchars($close_time); ?>" 
                                            <?php echo $is_closed ? 'disabled' : ''; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="hours[<?php echo $day; ?>][is_closed]" 
                                            class="closed-checkbox" 
                                            <?php echo $is_closed ? 'checked' : ''; ?> 
                                            data-day="<?php echo $day; ?>">
                                    </td>
                                </tr> 
                                <?php } ?>
                            </tbody>
                        </table>
                        <div id="hours-errors" class="error"></div>
                        <button type="submit" name="store_hours" class="save-btn">Save</button>
                    </form>
                </div>
            </div>
        </div>
        <script>
            // Store Name Validation
            function validateName() {
                const input = document.getElementById('store_name');
                const error = document.getElementById('store_name_error');
                const value = input.value.trim();

                if (!value) {
                    error.textContent = "Store name is required";
                    input.classList.add('invalid');
                    return false;
                }
                if (!/^[A-Za-z0-9\s\-\'&]+$/.test(value)) {
                    error.textContent = "Only letters, numbers, spaces, hyphens, apostrophes and ampersands allowed";
                    input.classList.add('invalid');
                    return false;
                }
                if (value.length > 100) {
                    error.textContent = "Maximum 100 characters allowed";
                    input.classList.add('invalid');
                    return false;
                }

                error.textContent = "";
                input.classList.remove('invalid');
                return true;
            }

            // Phone Validation and Formating
            function validatePhone() {
                const input = document.getElementById('phone');
                const error = document.getElementById('phone_error');
                const value = input.value.trim();
                const digits = value.replace(/\D/g, '');

                if (!value) {
                    error.textContent = "Phone number is required";
                    input.classList.add('invalid');
                    return false;
                }
                
                // Check if contains any letters or special characters (except digits)
                if (/[^\d\s\-()]/.test(value)) {
                    error.textContent = "Phone number can only contain numbers";
                    input.classList.add('invalid');
                    return false;
                }
                
                // Check if it has exactly 10 digits (after removing non-digits)
                if (digits.length !== 10) {
                    error.textContent = "Phone must be 10 digits";
                    input.classList.add('invalid');
                    return false;
                }

                input.value = digits;
                
                error.textContent = "";
                input.classList.remove('invalid');
                return true;
            }
            // Email Validation
            function validateEmail() {
                const input = document.getElementById('email');
                const error = document.getElementById('email_error');
                const value = input.value.trim();
                if (!value) {
                    error.textContent = "Email number is required";
                    input.classList.add('invalid');
                    return false;
                }
                if (value && !/^\S+@\S+\.\S+$/.test(value)) {
                    error.textContent = "Invalid email format";
                    input.classList.add('invalid');
                    return false;
                }

                error.textContent = "";
                input.classList.remove('invalid');
                return true;
            }
            // Address Validation
            function validateAddress() {
                const input = document.getElementById('address');
                const error = document.getElementById('address_error');
                const value = input.value.trim();

                if (!value) {
                    error.textContent = "Address is required";
                    input.classList.add('invalid');
                    return false;
                }
                if (!/^[A-Za-z0-9\s\-\.,#]+$/i.test(value)) {
                    error.textContent = "Invalid address characters";
                    input.classList.add('invalid');
                    return false;
                }

                error.textContent = "";
                input.classList.remove('invalid');
                return true;
            }
            // City Validation
            function validateCity() {
                const input = document.getElementById('city');
                const error = document.getElementById('city_error');
                const value = input.value.trim();

                if (!value) {
                    error.textContent = "City is required";
                    input.classList.add('invalid');
                    return false;
                }
                if (!/^[A-Za-z\s\-]+$/.test(value)) {
                    error.textContent = "Only letters, spaces and hyphens allowed";
                    input.classList.add('invalid');
                    return false;
                }
                if (value.length > 100) {
                    error.textContent = "Maximum 120 characters allowed";
                    input.classList.add('invalid');
                    return false;
                }

                error.textContent = "";
                input.classList.remove('invalid');
                return true;
            }
            // Zip Code Validation
            function validateZip() {
                const input = document.getElementById('zip');
                const error = document.getElementById('zip_error');
                const value = input.value.trim();

                if (!value) {
                    error.textContent = "Zip code is required";
                    input.classList.add('invalid');
                    return false;
                }
                if (!/^\d{5}(-\d{4})?$/.test(value)) {
                    error.textContent = "Must be 5 digits or 9 digits with hyphen";
                    input.classList.add('invalid');
                    return false;
                }
                if (value.length > 100) {
                    error.textContent = "Maximum 300 characters allowed";
                    input.classList.add('invalid');
                    return false;
                }

                error.textContent = "";
                input.classList.remove('invalid');
                return true;
            }
            // ChatGPT helped with validating the store hours
            // Store Hours Validation
            function validateStoreHours(event) {
                let isValid = true;
                const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                const errorDiv = document.getElementById('hours-errors');
                errorDiv.innerHTML = '';
                
                days.forEach(day => {
                    const checkbox = document.querySelector(`input[name="hours[${day}][is_closed]"]`);
                    const isClosed = checkbox.checked;
                    const openTime = document.querySelector(`input[name="hours[${day}][open_time]"]`).value;
                    const closeTime = document.querySelector(`input[name="hours[${day}][close_time]"]`).value;
                    
                    if (!isClosed) {
                        if (!openTime) {
                            errorDiv.innerHTML += `<p>${day}: Opening time is required when not closed</p>`;
                            isValid = false;
                        }
                        if (!closeTime) {
                            errorDiv.innerHTML += `<p>${day}: Closing time is required when not closed</p>`;
                            isValid = false;
                        }
                        if (openTime && closeTime && openTime >= closeTime) {
                            errorDiv.innerHTML += `<p>${day}: Closing time must be after opening time</p>`;
                            isValid = false;
                        }
                    }
                });
                
                if (!isValid) {
                    errorDiv.style.display = 'block';
                    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    event.preventDefault(); // Prevent form submission
                    return false;
                }
                
                return true; // Allow form to submit normally
            }

            // Closed checkbox handling
            document.addEventListener('DOMContentLoaded', function() {
                const closedCheckboxes = document.querySelectorAll('.closed-checkbox');
                
                closedCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const day = this.getAttribute('data-day');
                        const openInput = document.querySelector(`input[name="hours[${day}][open_time]"]`);
                        const closeInput = document.querySelector(`input[name="hours[${day}][close_time]"]`);
                        
                        openInput.disabled = this.checked;
                        closeInput.disabled = this.checked;
                        
                        if (this.checked) {
                            openInput.value = '';
                            closeInput.value = '';
                        }
                    });
                    
                    // Initialize on page load
                    checkbox.dispatchEvent(new Event('change'));
                });

                // Form submission
                const storeHoursForm = document.getElementById('store_hours');
                if (storeHoursForm) {
                    storeHoursForm.addEventListener('submit', validateStoreHours);
                }
                
            });
           
            // Event Listeners
            document.addEventListener('DOMContentLoaded', function() {
                // Field validation on blur
                document.getElementById('store_name').addEventListener('blur', validateName);
                document.getElementById('phone').addEventListener('blur', validatePhone);
                document.getElementById('email').addEventListener('blur', validateEmail);
                document.getElementById('address').addEventListener('blur', validateAddress);
                document.getElementById('city').addEventListener('blur', validateCity);
                document.getElementById('zip').addEventListener('blur', validateZip);

                // Form submission validation
                document.getElementById('store_information').addEventListener('submit', function(e) {
                    if (!validateName() || !validatePhone() || !validateEmail() || 
                        !validateAddress() || !validateCity() || !validateZip()) {
                        e.preventDefault();
                        return false;
                    }
                    return true;
                });
            });

        </script>
    </body>
</html>