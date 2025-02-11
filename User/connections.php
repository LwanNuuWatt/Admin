<?php
// Define database connection variables
$servername = "localhost";   // Database server (usually localhost)
$username = "root";          // Database username (replace with your username)
$password = "";              // Database password (replace with your password)
$dbname = "admin";           // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
