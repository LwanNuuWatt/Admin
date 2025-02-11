<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: user_dashboard.php");
    exit();
}

$election_id = $_GET['id'];

// Connect to database
$conn = new mysqli("localhost", "root", "", "admin");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch election details
$stmt = $conn->prepare("SELECT election_title, start_time, end_time FROM elections WHERE id = ?");
$stmt->bind_param("i", $election_id);
$stmt->execute();
$result = $stmt->get_result();
$election = $result->fetch_assoc();

if (!$election) {
    echo "<p class='text-center text-red-500'>Election not found.</p>";
    exit();
}

// Get the remaining time for countdown
$end_time = new DateTime($election['end_time']);
$current_time = new DateTime();
$time_remaining = $end_time->diff($current_time);

// Fetch the positions for this election
$stmt_positions = $conn->prepare("SELECT position_name FROM positions WHERE election_id = ?");
$stmt_positions->bind_param("i", $election_id);
$stmt_positions->execute();
$positions_result = $stmt_positions->get_result();

// Fetch candidates for this election
$stmt_candidates = $conn->prepare("SELECT id, candidate_name, candidate_platform, position, candidate_photo FROM candidates WHERE election_id = ?");
$stmt_candidates->bind_param("i", $election_id);
$stmt_candidates->execute();
$candidates_result = $stmt_candidates->get_result();

$stmt->close();
$stmt_positions->close();
$stmt_candidates->close();

// Check if the user has already voted in this election
$user_id = $_SESSION['user_id'];
$has_voted = false;
$conn = new mysqli("localhost", "root", "", "admin");
$stmt_check_vote = $conn->prepare("SELECT * FROM votes WHERE user_id = ? AND election_id = ?");
$stmt_check_vote->bind_param("ii", $user_id, $election_id);
$stmt_check_vote->execute();
$result_vote = $stmt_check_vote->get_result();
if ($result_vote->num_rows > 0) {
    $has_voted = true;
}
$stmt_check_vote->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-4xl p-6 bg-white rounded-xl shadow-xl">
        <h2 class="text-3xl font-bold text-gray-700 text-center"><?php echo htmlspecialchars($election['election_title']); ?></h2>
        <p class="mt-2 text-gray-700 text-center"><strong>Start Time:</strong> <?php echo $election['start_time']; ?></p>
        <p class="mt-2 text-gray-700 text-center"><strong>End Time:</strong> <?php echo $election['end_time']; ?></p>

        <h3 class="mt-6 text-xl font-bold text-gray-700">Time Remaining: </h3>
        <div id="countdown" class="text-lg font-bold text-red-500"></div>

        <script>
            // Set the end time from PHP
            const endTime = new Date("<?php echo $election['end_time']; ?>").getTime();

            const countdownElement = document.getElementById('countdown');

            const interval = setInterval(function() {
                const now = new Date().getTime();
                const remainingTime = endTime - now;

                if (remainingTime <= 0) {
                    clearInterval(interval);
                    countdownElement.innerHTML = "Time is over!";
                    document.getElementById("vote-form").style.display = "none"; // Hide vote button
                } else {
                    const hours = Math.floor((remainingTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((remainingTime % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((remainingTime % (1000 * 60)) / 1000);

                    countdownElement.innerHTML = `${hours}h ${minutes}m ${seconds}s`;
                }
            }, 1000);
        </script>

        <?php if ($has_voted): ?>
            <p class="text-center text-red-500">You have already voted in this election!</p>
        <?php else: ?>
            <h3 class="mt-6 text-xl font-bold text-gray-700">Candidates</h3>
            <form action="vote.php" method="POST" id="vote-form" class="mt-4">
                <?php
                // Reset the candidates result pointer to fetch candidates for each position
                $candidates_result->data_seek(0);
                ?>
                <?php while ($position = $positions_result->fetch_assoc()) { ?>
                    <h3 class="mt-6 text-lg font-semibold"><?php echo htmlspecialchars($position['position_name']); ?>: </h3>
                    <div class="flex flex-wrap">
                        <?php while ($candidate = $candidates_result->fetch_assoc()) { ?>
                            <?php if ($candidate['position'] == $position['position_name']) { ?>
                                <label class="block p-2 bg-gray-200 rounded-lg shadow mt-2">
                                    <input type="radio" name="position_<?php echo htmlspecialchars($position['position_name']); ?>" value="<?php echo $candidate['id']; ?>" required>
                                    <?php echo htmlspecialchars($candidate['candidate_name']); ?>
                                    <br>
                                    <?php if (!empty($candidate['candidate_photo']) && file_exists("../uploads/" . $candidate['candidate_photo'])): ?>
                                        <img src="../uploads/<?php echo htmlspecialchars($candidate['candidate_photo']); ?>" alt="Candidate Photo" width="100" height="100">
                                    <?php else: ?>
                                        <p>No Photo</p>
                                    <?php endif; ?>
                                </label>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php } ?>
                <button type="submit" class="mt-4 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">Vote</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>