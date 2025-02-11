<?php
// Start the session
session_start();

// Database connection (update with your database credentials)
$conn = new mysqli("localhost", "root", "", "admin");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the election id from the URL
if (isset($_GET['id'])) {
    $election_id = $_GET['id'];

    // First, delete related candidates and positions
    $stmt_candidates = $conn->prepare("DELETE FROM candidates WHERE election_id = ?");
    $stmt_candidates->bind_param("i", $election_id);
    $stmt_candidates->execute();
    $stmt_candidates->close();

    $stmt_positions = $conn->prepare("DELETE FROM positions WHERE election_id = ?");
    $stmt_positions->bind_param("i", $election_id);
    $stmt_positions->execute();
    $stmt_positions->close();

    // Then, delete the election
    $stmt_election = $conn->prepare("DELETE FROM elections WHERE id = ?");
    $stmt_election->bind_param("i", $election_id);

    if ($stmt_election->execute()) {
        // Redirect back to election_title.php after successful deletion
        header("Location: election_title.php");
        exit();
    } else {
        echo "Error deleting election.";
    }

    $stmt_election->close();
} else {
    echo "Election ID not specified.";
}

// Close the database connection
$conn->close();

include 'db_connect.php';

if (isset($_GET['id'])) {
    $election_id = $_GET['id'];

    // Delete candidates associated with the election
    $stmt_candidates = $conn->prepare("DELETE FROM candidates WHERE election_id = ?");
    $stmt_candidates->bind_param("i", $election_id);
    $stmt_candidates->execute();
    $stmt_candidates->close();

    // Delete positions associated with the election
    $stmt_positions = $conn->prepare("DELETE FROM positions WHERE election_id = ?");
    $stmt_positions->bind_param("i", $election_id);
    $stmt_positions->execute();
    $stmt_positions->close();

    // Delete the election itself
    $stmt_election = $conn->prepare("DELETE FROM elections WHERE id = ?");
    $stmt_election->bind_param("i", $election_id);
    $stmt_election->execute();
    $stmt_election->close();

    // Redirect to the election titles page
    header("Location: election_title.php");
    exit();
}
