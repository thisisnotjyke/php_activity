<?php

// Database configuration
$hostname = "localhost";
$username = "root";
$password = ""; // Assuming no password is set
$dbname = "phpact";

// Attempt to establish a connection to the database
$connection = mysqli_connect($hostname, $username, $password, $dbname);

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "Connected successfully!";
}

// Close the database connection
mysqli_close($connection);

?>
