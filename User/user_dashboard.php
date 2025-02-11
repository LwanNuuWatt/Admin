<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "admin");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details
$result = $conn->query("SELECT name, email, profile_picture FROM users WHERE id = '$user_id'");
$user_data = $result->fetch_assoc();

// Default profile picture if none is set
// Ensure the session holds the latest profile picture
$profile_picture = (!empty($_SESSION['profile_picture']) && file_exists("../uploads/" . $_SESSION['profile_picture']))
    ? "../uploads/" . $_SESSION['profile_picture'] . "?t=" . time() // Forces reload
    : "../uploads/b7.jpg";


// Fetch active elections
$elections = $conn->query("SELECT id, election_title, description FROM elections ORDER BY id DESC");

// Fetch voting history
$history_stmt = $conn->prepare("SELECT v.*, e.election_title, c.candidate_name, c.position 
    FROM votes v 
    JOIN elections e ON v.election_id = e.id 
    JOIN candidates c ON v.candidate_id = c.id 
    WHERE v.user_id = ?");
$history_stmt->bind_param("i", $_SESSION['user_id']);
$history_stmt->execute();
$history_result = $history_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        body {
            background: #1E1E2D;
            color: white;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            width: 240px;
            background: #0D0D26;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
        }

        .profile-pic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #ffcc00;
            object-fit: cover;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 10px;
            width: 100%;
            text-align: center;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .sidebar a:hover {
            background: #ffcc00;
            color: black;
        }

        .dashboard {
            margin-left: 260px;
            padding: 20px;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            transition: transform 0.3s;
            margin-bottom: 20px;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .vote-btn {
            background: linear-gradient(45deg, #ff5733, #ffcc00);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            color: white;
            transition: transform 0.3s ease-in-out;
            display: block;
            margin-top: 10px;
        }

        .vote-btn:hover {
            transform: scale(1.1);
        }

        .theme-toggle {
            position: fixed;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 24px;
        }

        .hidden {
            display: none;
        }
    </style>

    <script>
        function toggleSection(id) {
            document.getElementById(id).classList.toggle("hidden");
        }

        function downloadPDF() {
            const {
                jsPDF
            } = window.jspdf;
            let doc = new jsPDF();
            doc.setFont("helvetica", "bold");
            doc.setFontSize(16);
            doc.text("Your Voting History", 10, 15);
            let y = 30;

            document.querySelectorAll(".history-item").forEach(item => {
                let lines = item.innerText.split("\n");
                doc.setFont("helvetica", "normal");
                doc.setFontSize(12);
                lines.forEach(line => {
                    doc.text(line, 10, y);
                    y += 8;
                });
                y += 5;
            });

            doc.save("Voting_History.pdf");
        }
    </script>
</head>

<body>
    <div class="sidebar">
        <img src="<?php echo $profile_picture; ?>" alt="Profile" class="profile-pic">
        <h2 class="text-yellow-400"><?php echo htmlspecialchars($user_name); ?></h2>
        <a href="user_dashboard.php"><i class="fa-solid fa-home"></i> Dashboard</a>
        <a href="result.php"><i class="fa-solid fa-chart-bar"></i> View Results</a>
        <a href="user_setting.php" class="button"><i class="fa-solid fa-gear"></i> Settings</a>
        <a href="user_login.php"><i class="fa-solid fa-sign-out"></i> Logout</a>
    </div>

    <div class="dashboard">
        <h2 class="text-3xl font-bold">User Dashboard</h2>

        <div class="mt-6 flex gap-4">
            <button onclick="toggleSection('elections-section')" class="vote-btn"><i class="fa-solid fa-list"></i> Show Active Elections</button>
            <button onclick="toggleSection('history-section')" class="vote-btn"><i class="fa-solid fa-history"></i> View Voting History</button>
            <button onclick="downloadPDF()" class="vote-btn"><i class="fa-solid fa-download"></i> Download History</button>
        </div>

        <div id="elections-section" class="mt-6 hidden">
            <h3 class="text-2xl font-bold text-yellow-400">Active Elections</h3>
            <div class="grid grid-cols-2 gap-6 mt-4">
                <?php while ($row = $elections->fetch_assoc()): ?>
                    <div class="card p-4">
                        <h4 class="text-lg font-semibold text-yellow-300"><?php echo htmlspecialchars($row['election_title']); ?></h4>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <a href="user_view_election.php?id=<?php echo $row['id']; ?>" class="vote-btn">Vote Now</a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div id="history-section" class="mt-6 hidden">
            <h3 class="text-2xl font-bold text-yellow-400">Your Voting History</h3>
            <?php while ($history = $history_result->fetch_assoc()): ?>
                <div class="card history-item">
                    <p><strong>Election:</strong> <?php echo htmlspecialchars($history['election_title']); ?></p>
                    <p><strong>Candidate:</strong> <?php echo htmlspecialchars($history['candidate_name']); ?></p>
                    <p><strong>Position:</strong> <?php echo htmlspecialchars($history['position']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>

</html>