<?php
session_start();
$conn = new mysqli("localhost", "root", "", "admin");

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$result = $conn->query("SELECT name, email, password, profile_picture FROM users WHERE id = '$user_id'");
$user_data = $result->fetch_assoc();
$profile_picture = (!empty($user_data['profile_picture']) && file_exists("../uploads/" . $user_data['profile_picture']))
    ? "../uploads/" . $user_data['profile_picture'] . "?t=" . time()
    : "../uploads/default_user.png";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check if the user exists and verify the current password
    if (!password_verify($current_password, $user_data['password'])) {
        $_SESSION['error'] = "⚠️ Incorrect Current Password!";
        header("Location: user_setting.php");
        exit();
    }

    // Validate email to check if it's already taken by another user
    $email_check = $conn->query("SELECT id FROM users WHERE email='$email' AND id != '$user_id'");
    if ($email_check->num_rows > 0) {
        $_SESSION['error'] = "⚠️ Email is already taken!";
        header("Location: user_setting.php");
        exit();
    }

    // Initialize the update query parts
    $update_fields = [];

    // Check if the name or email has changed
    if ($name !== $user_data['name']) {
        $update_fields[] = "name='$name'";
        $_SESSION['user_name'] = $name;
    }
    if ($email !== $user_data['email']) {
        $update_fields[] = "email='$email'";
    }

    // Update profile picture if a new file is uploaded
    if (!empty($_FILES['profile_picture']['name'])) {
        $image_dir = "../uploads/";
        $image_name = "profile_" . $user_id . "_" . time() . ".jpg";
        $image_path = $image_dir . $image_name;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $image_path)) {
            $update_fields[] = "profile_picture='$image_name'";
            $_SESSION['profile_picture'] = $image_name;
        } else {
            $_SESSION['error'] = "⚠️ Profile picture upload failed!";
            header("Location: user_setting.php");
            exit();
        }
    }

    // Update password if a new one is provided
    if (!empty($new_password)) {
        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = "⚠️ New passwords do not match!";
            header("Location: user_setting.php");
            exit();
        }
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $update_fields[] = "password='$hashed_password'";
    }

    // Only run the update query if changes were made
    if (!empty($update_fields)) {
        $update_query = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id='$user_id'";
        $conn->query($update_query);

        $_SESSION['success'] = "✅ Profile updated successfully!";
        header("Location: user_dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "⚠️ No changes detected! Please update at least one field.";
        header("Location: user_setting.php");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

    <style>
        body {
            background: #1E1E2D;
            color: white;
            font-family: Arial, sans-serif;
        }

        .settings-container {
            max-width: 450px;
            margin: 50px auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.6);
            border-radius: 10px;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(255, 204, 0, 0.4);
        }

        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #ffcc00;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }

        .input-group {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
        }

        .input-group i {
            margin-right: 10px;
            color: #ffcc00;
        }

        .input-group input {
            width: 100%;
            background: none;
            border: none;
            outline: none;
            color: white;
        }

        .update-btn {
            background: linear-gradient(45deg, #ff5733, #ffcc00);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            transition: transform 0.3s ease-in-out;
            cursor: pointer;
            display: block;
            width: 100%;
            margin-top: 15px;
        }

        .update-btn:hover {
            transform: scale(1.1);
        }

        .error-message {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }

        .success-message {
            color: green;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="settings-container">
        <h2 class="text-2xl font-bold text-yellow-400">Update Profile</h2>

        <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="profile-pic">

        <!-- Error / Success Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message"><?php echo $_SESSION['error'];
                                        unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message"><?php echo $_SESSION['success'];
                                            unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form action="user_setting.php" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="current_password" placeholder="Current Password (Required)" required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-key"></i>
                <input type="password" name="new_password" placeholder="New Password (Optional)">
            </div>

            <div class="input-group">
                <i class="fa-solid fa-check"></i>
                <input type="password" name="confirm_password" placeholder="Confirm New Password">
            </div>

            <input type="file" name="profile_picture" accept="image/*" class="mt-4">

            <button type="submit" class="update-btn">Update Profile</button>
        </form>
    </div>
</body>

</html>