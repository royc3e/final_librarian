<?php
$servername = "localhost";  // Database server (change as per your setup)
$username = "root";         // Database username
$password = "";             // Database password
$dbname = "new_library";     // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "";
?>
