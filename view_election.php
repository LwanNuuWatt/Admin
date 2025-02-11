<?php
include 'db_connect.php';

// Get the election ID from the URL
if (isset($_GET['id'])) {
    $election_id = $_GET['id'];

    // Fetch the election details from the database
    $stmt = $conn->prepare("SELECT * FROM elections WHERE id = ?");
    $stmt->bind_param("i", $election_id);
    $stmt->execute();
    $election_result = $stmt->get_result();
    $election = $election_result->fetch_assoc();

    // Fetch the candidates for this election
    $stmt_candidates = $conn->prepare("SELECT * FROM candidates WHERE election_id = ?");
    $stmt_candidates->bind_param("i", $election_id);
    $stmt_candidates->execute();
    $candidates_result = $stmt_candidates->get_result();

    // Fetch the positions for this election
    $stmt_positions = $conn->prepare("SELECT * FROM positions WHERE election_id = ?");
    $stmt_positions->bind_param("i", $election_id);
    $stmt_positions->execute();
    $positions_result = $stmt_positions->get_result();
} else {
    echo "Invalid Election ID!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Election</title>
    <style>
        .details-container {
            margin: 20px;
            padding: 20px;
            background-color: #f1f1f1;
            border-radius: 5px;
        }

        .details-container h2 {
            margin-bottom: 20px;
        }

        .details-container table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .details-container table th,
        .details-container table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .details-container table th {
            background-color: #f2f2f2;
        }

        .vote-button {
            background-color: #4CAF50;
            color: white;
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .vote-button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>

    <div class="details-container">
        <h2>Election Title: <?php echo htmlspecialchars($election['election_title']); ?></h2>
        <p><strong>Start Time:</strong> <?php echo $election['start_time']; ?></p>
        <p><strong>End Time:</strong> <?php echo $election['end_time']; ?></p>

        <h3>Positions (Categories):</h3>
        <ul>
            <?php while ($position = $positions_result->fetch_assoc()) { ?>
                <li><?php echo htmlspecialchars($position['position_name']); ?></li>
            <?php } ?>
        </ul>

        <h3>Candidates:</h3>
        <form method="POST">
            <table>
                <tr>
                    <th>Name</th>
                    <th>Platform</th>
                    <th>Position</th>
                    <th>Photo</th>

                </tr>
                <?php
                while ($candidate = $candidates_result->fetch_assoc()) {
                    $candidate_id = $candidate['id'];
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($candidate['candidate_name']); ?></td>
                        <td><?php echo htmlspecialchars($candidate['candidate_platform']); ?></td>
                        <td><?php echo htmlspecialchars($candidate['position']); ?></td>
                        <td><img src="uploads/<?php echo $candidate['candidate_photo']; ?>" alt="Candidate Photo" width="100" height="100"></td>
                        <!-- <td><button type="submit" name="vote_<?php echo $candidate_id; ?>" class="vote-button">Vote</button></td> -->
                    </tr>
                <?php
                }
                ?>
            </table>
        </form>
    </div>

</body>

</html>