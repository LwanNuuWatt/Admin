<?php
session_start();

// If the form is submitted, save the data in session
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['election_title'] = $_POST['election_title'];
    $_SESSION['num_candidates'] = $_POST['num_candidates'];
    $_SESSION['positions'] = $_POST['positions'];
    $_SESSION['start_time'] = $_POST['start_time'];
    $_SESSION['end_time'] = $_POST['end_time'];
    header("Location: save_election.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Election</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

    <style>
        body {
            background: #1e1e2d;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            max-width: 500px;
            padding: 25px;
            background: #29293d;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        h2 {
            text-align: center;
            color: #ffcc00;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            padding-left: 40px;
            border-radius: 8px;
            border: none;
            background: #333;
            color: white;
            font-size: 16px;
            outline: none;
            transition: 0.3s ease-in-out;
        }

        .form-group input:focus {
            transform: scale(1.05);
            background: #3a3a4d;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #ffcc00;
            font-size: 18px;
        }

        .button {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
        }

        .save-button {
            background: #ffcc00;
            color: black;
        }

        .save-button:hover {
            background: #ffaa00;
        }

        .cancel-button {
            background: #f44336;
            color: white;
            margin-top: 10px;
        }

        .cancel-button:hover {
            background: #d32f2f;
        }

        /* 🔥 Slide-in Panel */
        .slide-panel {
            position: fixed;
            right: -100%;
            top: 0;
            width: 400px;
            height: 100vh;
            background: #222;
            padding: 20px;
            transition: right 0.4s ease-in-out;
        }

        .slide-panel.open {
            right: 0;
        }

        .close-button {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            float: right;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>🗳️ Create a New Election</h2>

        <form action="create_election.php" method="POST">
            <div class="form-group">
                <i class="fas fa-trophy"></i>
                <input type="text" name="election_title" placeholder="Election Title" required>
            </div>

            <div class="form-group">
                <i class="fas fa-user-friends"></i>
                <input type="number" name="num_candidates" placeholder="Number of Candidates" required>
            </div>

            <div class="form-group">
                <i class="fas fa-award"></i>
                <input type="number" name="positions" placeholder="Number of Positions" required>
            </div>

            <div class="form-group">
                <i class="fas fa-calendar-alt"></i>
                <input type="datetime-local" name="start_time" placeholder="start time" required>
            </div>

            <div class="form-group">
                <i class="fas fa-clock"></i>
                <input type="datetime-local" name="end_time" placeholder="end time" required>
            </div>

            <form action="save_election.php" method="POST">
                <button type="submit" class="button save-button">🚀 Save Election</button>
            </form>

            <button type="button" class="button cancel-button" onclick="window.location.href='election_title.php'">❌ Cancel</button>
        </form>
    </div>

    <!-- 🔥 Slide-in Panel for Additional Features -->
    <div id="slide-panel" class="slide-panel">
        <button class="close-button" onclick="togglePanel()">✖</button>
        <h2 class="text-yellow-400 text-lg font-bold">⚙️ Election Settings</h2>
        <p class="text-gray-300 text-sm mt-2">Here you can configure additional settings for your election.</p>
    </div>

    <script>
        function togglePanel() {
            document.getElementById('slide-panel').classList.toggle('open');
        }
    </script>

</body>

</html>