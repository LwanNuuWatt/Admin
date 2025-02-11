<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $voter_id = trim($_POST['voter_id']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($voter_id) || empty($password)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: user_login.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format!";
        header("Location: user_login.php");
        exit();
    }

    // Connect to database
    $conn = new mysqli("localhost", "root", "", "admin");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ? AND voter_id = ?");
    $stmt->bind_param("ss", $email, $voter_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['success'] = "Login successful!";
            echo "<script>alert('Login successful!')</script>";
            header("Location: user_dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Incorrect password!";
        }
    } else {
        $_SESSION['error'] = "No account found with this email and voter ID!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        bounceSlow: 'bounce 3s infinite',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: url('../uploads/x.webp') no-repeat center center fixed;
            background-size: cover;
        }

        .login-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.5);
            text-align: center;
            width: 400px;
        }

        h2 {
            color: #ffcc00;
            font-size: 2rem;
            font-weight: bold;
            transition: 0.3s ease-in-out;
        }

        input {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid #ffcc00;
            color: white;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            transition: 0.3s ease-in-out;
        }

        input:focus {
            background: rgba(255, 255, 255, 0.3);
            outline: none;
            transform: scale(1.05);
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        /* 🔥 NEW ANIMATED LOGIN BUTTON 🔥 */
        .login-btn {
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            color: white;
            background-color: #171717;
            padding: 1em 2em;
            border: none;
            border-radius: .6rem;
            position: relative;
            cursor: pointer;
            overflow: hidden;
            margin-top: 10px;
        }

        .login-btn span:not(:nth-child(6)) {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            height: 30px;
            width: 30px;
            background-color: #0c66ed;
            border-radius: 50%;
            transition: .6s ease;
        }

        .login-btn span:nth-child(6) {
            position: relative;
        }

        .login-btn span:nth-child(1) {
            transform: translate(-3.3em, -4em);
        }

        .login-btn span:nth-child(2) {
            transform: translate(-6em, 1.3em);
        }

        .login-btn span:nth-child(3) {
            transform: translate(-.2em, 1.8em);
        }

        .login-btn span:nth-child(4) {
            transform: translate(3.5em, 1.4em);
        }

        .login-btn span:nth-child(5) {
            transform: translate(3.5em, -3.8em);
        }

        .login-btn:hover span:not(:nth-child(6)) {
            transform: translate(-50%, -50%) scale(4);
            transition: 1.5s ease;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen">
    <div class="login-container">
        <h2 id="welcomeText" class="animate-bounceSlow">Welcome!</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="p-3 mt-3 text-red-700 bg-red-200 border border-red-400 rounded">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="user_login.php" method="POST" class="mt-4 space-y-4">
            <input type="email" name="email" placeholder="📧 Email Address" required class="rounded-lg">
            <input type="text" name="voter_id" id="voter_id" placeholder="🆔 Voter ID" required class="rounded-lg" oninput="fetchUsername()">
            <input type="password" name="password" placeholder="🔒 Password" required class="rounded-lg">

            <!-- 🔥 Custom Animated Login Button -->
            <button type="submit" class="login-btn">
                <span class="circle1"></span>
                <span class="circle2"></span>
                <span class="circle3"></span>
                <span class="circle4"></span>
                <span class="circle5"></span>
                <span class="text">Login</span>
            </button>
        </form>

        <p class="mt-4 text-white">Don't have an account? <a href="user_register.php" class="text-yellow-300">Register</a></p>
    </div>

    <script>
        function fetchUsername() {
            let voterId = document.getElementById("voter_id").value;
            let welcomeMessage = document.getElementById("welcomeText");

            if (voterId.length > 3) {
                fetch("fetch_username.php?voter_id=" + voterId)
                    .then(response => response.text())
                    .then(data => {
                        if (data.trim() !== "") {
                            welcomeMessage.innerHTML = "Welcome, " + data + "!";
                        } else {
                            welcomeMessage.innerHTML = "Welcome!";
                        }
                    });
            } else {
                welcomeMessage.innerHTML = "Welcome!";
            }
        }
    </script>
</body>

</html>