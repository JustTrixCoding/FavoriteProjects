
<?php
/*  
    db_connection.php
    A program to connect to the database.
    Authors: Alexandra Stratton, Kyle Moore, Ben Renner, Brinley Hull, Jose Leyba
    Creation date: 
    Revisions:
        4/2/2025 - Brinley, remove you are connected message
*/
$server = "sql312.infinityfree.com";
$user = "if0_38323969";
$pass = "Quartet44";
$dbname = "if0_38323969_quartet";
$conn = "";

// Check connection
try{
    $conn = mysqli_connect($server, $user, $pass, $dbname);
}
catch(mysqli_sql_exception){
    echo"could not connect";
}
?>