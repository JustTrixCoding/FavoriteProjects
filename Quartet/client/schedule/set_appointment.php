<?php
/*
set_appointment.php
File to set session data of the appointment that the user clicks.
Authors: Brinley Hull
Creation date: 3/1/2025
Revisions:
    4/10/2025 - Brinley, add minute
*/
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input from the fetch request
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['appointment'])) {
        $_SESSION["appointment"] = $data['appointment'];
        $_SESSION["day"] = $data['day'];
        $_SESSION["month"] = $data['month'];
        $_SESSION["year"] = $data['year'];
        $_SESSION["time"] = $data['time'];
        $_SESSION['minute'] = $data['minute'];
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "No appointment data received"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
