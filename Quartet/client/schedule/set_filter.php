<?php
session_start();
/*
set_filter.php
Sets the filter SESSION variables for the calendar filter.
Authors: Ben Renner, Kyle Moore, Alexandra Stratton, Jose Leyba, Brinley Hull
Creation date: 3/16/2025
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['filter'])) {
        // Ensure barber and time filters are properly set as arrays
        $_SESSION["barberFilter"] = isset($data['barber']) && is_array($data['barber']) ? $data['barber'] : [];
        $_SESSION["serviceFilter"] = isset($data['service']) ? $data['service'] : "None";
        $_SESSION["timeFilter"] = isset($data['time']) && is_array($data['time']) ? $data['time'] : [];
        
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Filter not set"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
