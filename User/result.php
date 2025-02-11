<?php
session_start();

// Database Connection
$conn = new mysqli("localhost", "root", "", "admin");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all elections
$elections = $conn->query("SELECT id, election_title FROM elections");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="flex flex-col items-center justify-center min-h-screen bg-gray-900 text-white">
    <div class="w-full max-w-3xl p-6 bg-gray-800 rounded-xl shadow-xl">
        <h2 class="text-3xl font-bold text-center text-white">Election Results</h2>

        <?php while ($election = $elections->fetch_assoc()):
            $election_id = $election['id'];
            $election_title = $election['election_title'];

            // Fetch candidates and their vote counts
            $candidates = $conn->query("
                SELECT c.id, c.candidate_name, COUNT(v.id) AS vote_count 
                FROM candidates c
                LEFT JOIN votes v ON c.id = v.candidate_id
                WHERE c.election_id = $election_id
                GROUP BY c.id
                ORDER BY vote_count DESC
            ");

            $total_votes = 0;
            $candidate_data = [];
            while ($row = $candidates->fetch_assoc()) {
                $total_votes += $row['vote_count'];
                $candidate_data[] = $row;
            }

            if ($total_votes == 0) {
                $total_votes = 1; // Prevent division by zero
            }

            // Get winner (First in sorted list)
            $winner = $candidate_data[0];
            $winner_percentage = round(($winner['vote_count'] / $total_votes) * 100, 2);
        ?>

            <!-- Election Title -->
            <div class="mt-8 p-6 bg-gray-700 rounded-lg shadow-lg">
                <h3 class="text-2xl font-bold text-center text-yellow-400"><?php echo htmlspecialchars($election_title); ?></h3>

                <!-- Winner Section -->
                <div class="mt-4 p-4 bg-green-600 rounded-lg text-center">
                    <h4 class="text-xl font-bold text-white">🏆 Winner: <?php echo htmlspecialchars($winner['candidate_name']); ?></h4>
                    <p class="text-lg text-white">🎯 Votes: <?php echo $winner['vote_count']; ?> | 🏅 Winning %: <?php echo $winner_percentage; ?>%</p>

                    <div class="w-full bg-gray-400 h-6 rounded mt-2">
                        <div class="bg-green-500 h-6 rounded" style="width: <?php echo $winner_percentage; ?>%;"></div>
                    </div>
                </div>

                <!-- Other Candidates -->
                <table class="mt-4 w-full text-left border border-gray-500">
                    <thead>
                        <tr class="bg-gray-600">
                            <th class="p-2">Candidate</th>
                            <th class="p-2">Votes</th>
                            <th class="p-2">Vote %</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($candidate_data as $index => $row):
                            if ($index == 0) continue; // Skip winner
                            $vote_percentage = round(($row['vote_count'] / $total_votes) * 100, 2);
                        ?>
                            <tr class="border-b border-gray-500">
                                <td class="p-2"><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                                <td class="p-2"><?php echo $row['vote_count']; ?></td>
                                <td class="p-2"><?php echo $vote_percentage; ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Compact Graph -->
                <div class="mt-4 flex justify-center">
                    <canvas id="chart-<?php echo $election_id; ?>" style="max-width: 350px; max-height: 250px;"></canvas>
                </div>

                <script>
                    let ctx<?php echo $election_id; ?> = document.getElementById("chart-<?php echo $election_id; ?>").getContext("2d");
                    new Chart(ctx<?php echo $election_id; ?>, {
                        type: "bar",
                        data: {
                            labels: [<?php
                                        foreach ($candidate_data as $row) {
                                            echo '"' . addslashes($row['candidate_name']) . '",';
                                        }
                                        ?>],
                            datasets: [{
                                label: "Votes",
                                data: [<?php
                                        foreach ($candidate_data as $row) {
                                            echo $row['vote_count'] . ",";
                                        }
                                        ?>],
                                backgroundColor: ["#28a745", "#ff5733", "#f1c40f", "#3498db"]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: <?php echo max(1, $total_votes); ?>
                                }
                            }
                        }
                    });
                </script>
            </div>

        <?php endwhile; ?>

    </div>
</body>

</html>

<?php
$conn->close();
?>