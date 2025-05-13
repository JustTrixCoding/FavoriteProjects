<?php
/*
login_check.php
A script to check whether a user is logged in before they can access barber pages.
Authors: Brinley Hull, Allie Stratton, Jose Leyba, Ben Renner, Kyle Moore
Creation date: 4/23/2025
Revisions:
Preconditions: None
Postconditions: None
Error conditions: None
Side effects: None
Invariants: None
Any known faults: None
*/
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>