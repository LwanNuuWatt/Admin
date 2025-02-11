<?php
$conn = new mysqli("localhost", "root", "", "admin");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['voter_id'])) {
    $voter_id = $_GET['voter_id'];

    $stmt = $conn->prepare("SELECT name FROM users WHERE voter_id = ?");
    $stmt->bind_param("s", $voter_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($name);
    $stmt->fetch();

    echo $name ?? ""; // Return the username or empty if not found
}

$stmt->close();
$conn->close();
