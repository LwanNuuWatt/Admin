<?php
include 'db_connect.php';

// Fetch all election titles
$query = "SELECT * FROM elections";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Titles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

    <style>
        body {
            background: #1e1e2d;
            color: white;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #29293d;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }

        h2 {
            text-align: center;
            color: #ffcc00;
        }

        .election-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #333;
            margin: 10px 0;
            border-radius: 5px;
            transition: 0.3s;
        }

        .election-title:hover {
            transform: scale(1.05);
        }

        .view-button,
        .delete-button {
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .view-button {
            background: #4CAF50;
            color: white;
        }

        .view-button:hover {
            background: #3e8e41;
        }

        .delete-button {
            background: #f44336;
            color: white;
        }

        .delete-button:hover {
            background: #d32f2f;
        }

        .add-button {
            background: #ffcc00;
            color: black;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            display: block;
            width: 100%;
            text-align: center;
            margin: 20px 0;
            transition: 0.3s;
        }

        .add-button:hover {
            background: #ffaa00;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>🗳️ Election Titles</h2>

        <!-- + Button to Add New Election -->
        <button class="add-button" onclick="window.location.href='create_election.php'">+ Create New Election</button>

        <!-- List of Existing Elections -->
        <?php
        if ($result->num_rows > 0) {
            while ($election = $result->fetch_assoc()) {
                $election_id = $election['id'];
                $election_title = $election['election_title'];
                $num_candidates = $election['num_candidates'];
                $positions = $election['positions'];

                echo "<div class='election-title'>";
                echo "<div>";
                echo "<h3>" . htmlspecialchars($election_title) . "</h3>";
                echo "<p>📌 Candidates: $num_candidates | Positions: $positions</p>";
                echo "</div>";
                echo "<div>";
                echo "<a href='view_election.php?id=$election_id' class='view-button'>View</a> ";
                echo "<a href='delete_election.php?id=$election_id' class='delete-button' onclick='return confirm(\"Are you sure you want to delete this election?\")'>Delete</a>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p style='text-align:center;'>❌ No elections found.</p>";
        }
        ?>

    </div>

</body>

</html>

<?php
$conn->close();
?>