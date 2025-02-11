<?php
session_start();
include 'db_connect.php'; // Ensure database connection works

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Debug: Check if form data is being received
    echo "Email: " . $email . "<br>";
    echo "Password: " . $password . "<br>";

    // Check if email exists in the admin table
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE email = ?");
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Debug: Check if query ran successfully
    if ($result->num_rows == 0) {
        die("⚠️ No admin found with this email. Check database.");
    }

    $admin = $result->fetch_assoc();

    // **Direct password comparison (since passwords are NOT hashed)**
    if ($password !== $admin['password']) {
        die("⚠️ Incorrect password!");
    }

    // Debug: Check if user data is fetched
    // echo "<pre>";
    // print_r($admin);
    // echo "</pre>";
    // exit();

    // If login successful, store session data
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_name'] = $admin['name'];

    header("Location: admin_dashboard.php");
    exit();
}
