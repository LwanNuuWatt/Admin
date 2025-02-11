<?php
session_start();
include 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch Admin Data
$admin_username = $_SESSION['admin_username'];
$query = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
$query->bind_param("s", $admin_username);
$query->execute();
$result = $query->get_result();
$admin = $result->fetch_assoc();

// Fetch Dashboard Stats
$total_users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$total_elections = $conn->query("SELECT COUNT(*) AS total FROM elections")->fetch_assoc()['total'];
$total_votes = $conn->query("SELECT COUNT(*) AS total FROM votes")->fetch_assoc()['total'];

// Admin Profile Picture
$profile_picture = (!empty($admin['profile_picture']) && file_exists("uploads/" . $admin['profile_picture']))
    ? "uploads/" . $admin['profile_picture']
    : "uploads/default_admin.png";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

    <style>
        /* 🔥 General Styles */
        body {
            background: #1e1e2d;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: -280px;
            width: 280px;
            background: #29293d;
            height: 100%;
            padding-top: 60px;
            transition: 0.4s;
            z-index: 1000;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px;
            margin: 10px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #ffcc00;
            color: black;
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 3px solid #ffcc00;
        }

        /* Menu Toggle Button */
        .menu-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #ffcc00;
            color: black;
            border: none;
            padding: 12px 15px;
            border-radius: 50%;
            cursor: pointer;
            transition: 0.3s;
            z-index: 1100;
        }

        .menu-button:hover {
            background: #ffaa00;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 20px;
            transition: margin-left 0.4s;
        }

        /* Dashboard Cards */
        .card {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            transition: 0.3s;
        }

        .card:hover {
            transform: scale(1.05);
        }

        /* Profile Edit Panel */
        #profile-panel {
            position: fixed;
            top: 0;
            right: -350px;
            width: 350px;
            height: 100%;
            background: #29293d;
            color: white;
            padding: 20px;
            transition: right 0.4s ease-in-out;
            z-index: 1000;
        }

        .profile-panel-open {
            right: 0;
        }
    </style>
</head>

<body>

    <!-- 🔥 Menu Toggle Button -->
    <button class="menu-button" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- 🔥 Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="<?php echo $profile_picture; ?>" alt="Admin Profile">
            <h3><?php echo htmlspecialchars($admin['username']); ?></h3>
        </div>
        <a href="admin_dashboard.php"><i class="fa-solid fa-home"></i> Dashboard</a>
        <a href="#" onclick="toggleProfilePanel()"><i class="fa-solid fa-user"></i> Profile</a>
        <a href="election_title.php"><i class="fa-solid fa-plus"></i> Make Election</a>
        <a href="voter_list.php"><i class="fa-solid fa-users"></i> Voter List</a>
        <a href="logout.php"><i class="fa-solid fa-sign-out"></i> Logout</a>
    </div>

    <!-- 🔥 Main Dashboard Content -->
    <div class="main-content">
        <h1 class="text-3xl font-bold">Welcome, <?php echo htmlspecialchars($admin['username']); ?>! 👋</h1>

        <div class="grid grid-cols-3 gap-6 mt-6">
            <div class="card">
                <h2 class="text-xl font-bold">📊 Total Users</h2>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="card">
                <h2 class="text-xl font-bold">🗳️ Active Elections</h2>
                <p><?php echo $total_elections; ?></p>
            </div>
            <div class="card">
                <h2 class="text-xl font-bold">✅ Votes Cast</h2>
                <p><?php echo $total_votes; ?></p>
            </div>
        </div>
    </div>

    <!-- 🔥 Profile Edit Panel -->
    <div id="profile-panel">
        <h2 class="text-2xl font-bold">Edit Profile</h2>
        <form action="update_admin.php" method="POST" enctype="multipart/form-data">
            <label>👤 Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" class="w-full p-2 rounded mt-2 bg-gray-800 text-white">

            <label>📧 Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" class="w-full p-2 rounded mt-2 bg-gray-800 text-white">

            <label>🖼️ Profile Picture</label>
            <input type="file" name="profile_picture" class="w-full p-2 rounded mt-2 bg-gray-800 text-white">

            <label>🔑 New Password</label>
            <input type="password" name="new_password" class="w-full p-2 rounded mt-2 bg-gray-800 text-white">

            <button type="submit" class="button w-full mt-4">Save Changes</button>
        </form>
        <button class="button w-full mt-4" onclick="toggleProfilePanel()">Close</button>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.style.left = sidebar.style.left === "0px" ? "-280px" : "0px";
        }

        function toggleProfilePanel() {
            document.getElementById("profile-panel").classList.toggle("profile-panel-open");
        }
    </script>

</body>

</html>