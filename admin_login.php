<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background: url('uploads/admin-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
        }

        .login-container {
            max-width: 400px;
            margin: auto;
            margin-top: 100px;
            background: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0px 0px 20px rgba(255, 204, 0, 0.5);
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .input-group {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.1);
            padding: 12px;
            border-radius: 5px;
            margin-top: 15px;
        }

        .input-group i {
            color: #ffcc00;
        }

        .input-group input {
            width: 100%;
            background: none;
            border: none;
            outline: none;
            color: white;
            font-size: 16px;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .login-btn {
            background: linear-gradient(45deg, #ff5733, #ffcc00);
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            transition: transform 0.3s;
            width: 100%;
            display: block;
            color: white;
            margin-top: 20px;
            border: none;
            cursor: pointer;
        }

        .login-btn:hover {
            transform: scale(1.1);
        }

        .error-message {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen">

    <div class="login-container">
        <h2 class="text-2xl font-bold text-yellow-400">Admin Login</h2>

        <!-- Error Message -->
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error-message"><?php echo $_SESSION['error'];
                                        unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form action="admin_auth.php" method="POST">
            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="name" placeholder="👤 Full Name" required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" placeholder="📧 Email Address" required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="🔒 Password" required>
            </div>

            <button type="submit" class="login-btn">🚀 Login</button>
        </form>
    </div>

</body>

</html>