<?php
session_start();
include 'db_connect.php';  // Ensure to include the database connection

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header('Location: admin_login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    $admin_username = $_SESSION['admin_username'];

    // Validate current password with the database
    $sql = "SELECT * FROM admin_users WHERE username = '$admin_username' AND password = '$current_password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Password match, now update the profile
        $update_sql = "UPDATE admin_users SET username = '$name', email = '$email', password = '$new_password' WHERE username = '$admin_username'";

        if ($conn->query($update_sql) === TRUE) {
            // Profile updated successfully, update session variable too
            $_SESSION['admin_username'] = $name;
            header("Location: admin_dashboard.php");  // Redirect to the dashboard after update
        } else {
            echo "Error updating profile: " . $conn->error;
        }
    } else {
        echo "Incorrect current password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Admin Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        form {
            width: 300px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>

    <h2 style="text-align: center;">Update Admin Profile</h2>
    <form action="update_admin.php" method="POST">
        <input type="text" name="name" placeholder="New Name" value="<?php echo $_SESSION['admin_username']; ?>" required>
        <input type="email" name="email" placeholder="New Email" required>
        <input type="password" name="current_password" placeholder="Current Password" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <button type="submit">Update</button>
    </form>

    <a href="logout.php" style="text-align: center; display: block; padding: 10px; text-decoration: none; background-color: red; color: white; text-align: center; border-radius: 5px; margin-top: 20px;">Logout</a>

</body>

</html>