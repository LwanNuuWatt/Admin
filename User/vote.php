

<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

// Get the election ID and candidate ID
$election_id = $_POST['election_id'];
$candidate_id = $_POST['candidate_id'];

// Connect to the database
$conn = new mysqli("localhost", "root", "", "admin");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user has already voted in this election
$stmt = $conn->prepare("SELECT id FROM votes WHERE user_id = ? AND election_id = ?");
$stmt->bind_param("ii", $_SESSION['user_id'], $election_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['error'] = "You have already voted in this election!";
    header("Location: user_dashboard.php");
    exit();
}
$stmt->close();

// Insert the user's vote (now assuming the user is allowed to vote)
$stmt = $conn->prepare("INSERT INTO votes (user_id, election_id, candidate_id) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $_SESSION['user_id'], $election_id, $candidate_id);
if ($stmt->execute()) {
    $_SESSION['success'] = "Your vote has been successfully submitted!";
} else {
    $_SESSION['error'] = "Failed to submit your vote. Please try again.";
}

$stmt->close();
$conn->close();

header("Location: user_dashboard.php");
exit();
?>



