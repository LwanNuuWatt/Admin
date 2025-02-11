<?php
session_start();
$conn = new mysqli("localhost", "root", "", "admin");

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$error_message = "";

// If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_or_username = trim($_POST['email_or_username']);
    $password = trim($_POST['password']);
    $voter_id = isset($_POST['voter_id']) ? trim($_POST['voter_id']) : "";
    $login_type = $_POST['login_type']; // 'user' or 'admin'

    if ($login_type === "user") {
        // USER LOGIN
        $stmt = $conn->prepare("SELECT id, name, password, voter_id FROM users WHERE email = ? AND voter_id = ?");
        $stmt->bind_param("ss", $email_or_username, $voter_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: User/user_dashboard.php");
            exit();
        } else {
            $error_message = "⚠️ Invalid email/voter ID or password!";
        }
    } elseif ($login_type === "admin") {
        // ADMIN LOGIN
        $stmt = $conn->prepare("SELECT id, username FROM admin_users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $email_or_username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error_message = "⚠️ Invalid admin username or password!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

    <style>
        body {
            background: url('uploads/x.webp') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: white;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            max-width: 400px;
            padding: 30px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(255, 204, 0, 0.4);
            transition: transform 0.5s;
            position: relative;
        }

        .switch-login {
            cursor: pointer;
            color: yellow;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
            display: inline-block;
            margin-top: 10px;
        }

        .switch-login:hover {
            transform: scale(1.1);
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

        .login-btn {
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

        .login-btn:hover {
            transform: scale(1.1);
        }

        .hidden {
            display: none;
        }

        /* 🔥 Fix Arrow Switch Issue */
        .switch-arrow {
            font-size: 25px;
            position: absolute;
            top: 50%;
            right: -40px;
            color: yellow;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
            z-index: 10;
        }

        .switch-arrow:hover {
            transform: scale(1.2);
        }

        .back-arrow {
            font-size: 25px;
            position: absolute;
            top: 50%;
            left: -40px;
            color: yellow;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
            z-index: 10;
        }

        .back-arrow:hover {
            transform: scale(1.2);
        }
    </style>

    <script>
        function switchLogin(type) {
            if (type === 'admin') {
                document.getElementById("user-login").classList.add("hidden");
                document.getElementById("admin-login").classList.remove("hidden");
            } else {
                document.getElementById("admin-login").classList.add("hidden");
                document.getElementById("user-login").classList.remove("hidden");
            }
        }
    </script>
</head>

<body>
    <div class="login-container">
        <h2 class="text-2xl font-bold text-yellow-400">Login</h2>

        <!-- Error Message -->
        <?php if (!empty($error_message)): ?>
            <div class="text-red-500 font-bold mt-2"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- User Login Form (Default) -->
        <form id="user-login" action="login.php" method="POST">
            <input type="hidden" name="login_type" value="user">

            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="text" name="email_or_username" placeholder=" Email" required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-id-card"></i>
                <input type="text" name="voter_id" placeholder=" Voter ID" required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder=" Password" required>
            </div>

            <button type="submit" class="login-btn">Login as User</button>
            <p class="mt-4 text-white">Don't have an account? <a href="User/user_register.php" class="text-yellow-300">Register</a></p>

            <i class="fa-solid fa-arrow-right switch-arrow" onclick="switchLogin('admin')"></i>
        </form>

        <!-- Admin Login Form (Hidden by Default) -->
        <form id="admin-login" action="login.php" method="POST" class="hidden">
            <input type="hidden" name="login_type" value="admin">

            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="email_or_username" placeholder="👤 Admin Username" required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="🔒 Password" required>
            </div>

            <button type="submit" class="login-btn">Login as Admin</button>

            <i class="fa-solid fa-arrow-left back-arrow" onclick="switchLogin('user')"></i>
        </form>
    </div>
</body>

</html>