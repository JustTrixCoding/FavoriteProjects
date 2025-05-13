<!-- 
    logout.php
    A button for the barbers to log out of their accounts
    Author: Alexandra Stratton, Ben Renner, Brinley Hull, Jose Leyba, Kyle Moore
    Revisions:
    Creation date: 2/27/2025
    Preconditions
        Acceptable inputs: None
        Unacceptable inputs: None
    Postconditions:
        Session for Barber is destroyed
    Error conditions:
        Session Issues
    Side effects
        None
    Invariants
        None
    Known faults:
        None
-->
<?php
session_start();
session_unset(); 
session_destroy(); 
header("Location: index.php");
exit();
?>