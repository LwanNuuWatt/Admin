<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    function generateRandomKey($length = 8)
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }
    $voter_id = generateRandomKey(8);

    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: user_register.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format!";
        header("Location: user_register.php");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $conn = new mysqli("localhost", "root", "", "admin");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email already registered!";
        header("Location: user_register.php");
        exit();
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, voter_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashedPassword, $voter_id);

    if ($stmt->execute()) {
        require_once 'send_mail.php';
        if (sendMail($email, $voter_id)) {
            $_SESSION['success'] = "Registration successful!";
        }
        header("Location: ../login.php");
        exit();
    } else {
        $_SESSION['error'] = "Something went wrong!";
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
    <title>User Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: url('../uploads/x.webp') no-repeat center center fixed;
            background-size: cover;
        }

        .register-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        h2 {
            color: #ffcc00;
            font-size: 2rem;
            font-weight: bold;
        }

        input {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid #ffcc00;
            color: white;
            padding: 12px;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        /* Register Button */
        .register-btn {
            background: linear-gradient(90deg, #ff00ff, #ffcc00);
            border: none;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            padding: 12px 20px;
            border-radius: 30px;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .register-btn:hover {
            transform: scale(1.1);
            box-shadow: 0px 5px 15px rgba(255, 204, 0, 0.7);
        }

        /* Password Strength Box */
        .password-strength {
            margin-top: 10px;
            text-align: left;
            font-size: 1rem;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .password-strength img {
            width: 100px;
            height: 100px;
            transition: all 0.3s ease-in-out;
        }

        .valid {
            color: #0f0;
        }

        .invalid {
            color: #f00;
        }

        .valid::before {
            content: "✅ ";
        }

        .invalid::before {
            content: "❌ ";
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md register-container">
        <h2>Register Now</h2>

        <form action="user_register.php" method="POST" class="mt-4 space-y-4">
            <input type="text" name="name" placeholder="👤 Full Name" required class="w-full p-3 rounded-lg">
            <input type="email" name="email" placeholder="📧 Email Address" required class="w-full p-3 rounded-lg">
            <input type="password" name="password" id="password" placeholder="🔒 Password" required class="w-full p-3 rounded-lg" oninput="checkPasswordStrength()">

            <div id="password-strength-container" class="password-strength">
                <div>
                    <strong>Password Strength: <span id="strength-text">Too Weak</span></strong>
                    <ul>
                        <li id="length-check" class="invalid">At least 8 characters</li>
                        <li id="uppercase-check" class="invalid">At least 1 uppercase letter</li>
                        <li id="lowercase-check" class="invalid">At least 1 lowercase letter</li>
                        <li id="digit-check" class="invalid">At least 1 digit</li>
                        <li id="special-check" class="invalid">At least 2 special characters</li>
                    </ul>
                </div>
                <img id="strength-emoji" src="../uploads/s1.jfif" alt="Emoji">
            </div>

            <button type="submit" class="register-btn">✨ Register Now ✨</button>
            <p class="mt-4 text-white">Already have an account? <a href="../login.php" class="text-yellow-300">Login</a></p>
        </form>
    </div>

    <script>
        function checkPasswordStrength() {
            let password = document.getElementById("password").value;
            let strengthContainer = document.getElementById("password-strength-container");
            let strengthText = document.getElementById("strength-text");
            let emoji = document.getElementById("strength-emoji");

            let checks = {
                "length-check": password.length >= 8,
                "uppercase-check": /[A-Z]/.test(password),
                "lowercase-check": /[a-z]/.test(password),
                "digit-check": /\d/.test(password),
                "special-check": (password.match(/[^A-Za-z0-9]/g) || []).length >= 2
            };

            // Show password strength container only when user types
            strengthContainer.style.opacity = password ? "1" : "0";

            for (let id in checks) {
                document.getElementById(id).classList.toggle("valid", checks[id]);
                document.getElementById(id).classList.toggle("invalid", !checks[id]);
            }

            let allValid = Object.values(checks).every(Boolean);

            if (allValid) {
                strengthText.innerText = "Very Nice 🔥";
                emoji.src = "../uploads/s.webp"; // Sigma face
            } else if (checks["uppercase-check"] && checks["special-check"]) {
                strengthText.innerText = "Not Bad 😏";
                emoji.src = "../uploads/s2.webp"; // Halfway there face
            } else {
                strengthText.innerText = "Too Weak 😭";
                emoji.src = "../uploads/s1.jfif"; // Weak face
            }
        }
    </script>
</body>

</html>