<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $election_title = $_POST['election_title'];
    $num_candidates = $_POST['num_candidates'];
    $num_positions = $_POST['positions'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    include 'db_connect.php';
    $stmt = $conn->prepare("INSERT INTO elections (election_title, num_candidates, positions, start_time, end_time) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiis", $election_title, $num_candidates, $num_positions, $start_time, $end_time);
    $stmt->execute();
    $election_id = $stmt->insert_id;
    $stmt->close();

    for ($j = 1; $j <= $num_positions; $j++) {
        $position_name = $_POST['position_name_' . $j];

        if (empty($position_name)) {
            echo "<script>alert('Please provide position names for all positions.'); window.location.href = 'create_election.php';</script>";
            exit();
        }

        $stmt_position = $conn->prepare("INSERT INTO positions (election_id, position_name) VALUES (?, ?)");
        $stmt_position->bind_param("is", $election_id, $position_name);
        $stmt_position->execute();
        $stmt_position->close();
    }

    for ($i = 1; $i <= $num_candidates; $i++) {
        $candidate_name = $_POST['candidate_name_' . $i];
        $candidate_platform = $_POST['candidate_platform_' . $i];
        $position = $_POST['position_' . $i];

        if (isset($_FILES['candidate_photo_' . $i]) && $_FILES['candidate_photo_' . $i]['error'] == 0) {
            $candidate_photo = $_FILES['candidate_photo_' . $i]['name'];
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($candidate_photo);
            move_uploaded_file($_FILES['candidate_photo_' . $i]['tmp_name'], $target_file);

            $stmt = $conn->prepare("INSERT INTO candidates (election_id, candidate_name, candidate_photo, candidate_platform, position) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $election_id, $candidate_name, $candidate_photo, $candidate_platform, $position);
            $stmt->execute();
            $stmt->close();
        }
    }

    echo "<script>
            alert('Your election title has been successfully added!');
            window.location.href = 'admin_dashboard.php';
          </script>";
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
    <style>
        body {
            background: #1E1E2D;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
            width: 500px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }

        .form-container:hover {
            transform: scale(1.02);
        }

        .form-container h2 {
            color: #ffcc00;
            margin-bottom: 20px;
        }

        .form-container input,
        .form-container select,
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .form-container input:focus,
        .form-container select:focus,
        .form-container textarea:focus {
            background: rgba(255, 255, 255, 0.3);
            outline: none;
        }

        .save-button {
            padding: 12px;
            background: #ffcc00;
            color: black;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s ease-in-out;
        }

        .save-button:hover {
            background: #ffaa00;
        }
    </style>
</head>

<body>

    <div class="form-container">
        <h2>Create Election Title</h2>
        <form action="save_election.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="election_title" placeholder="Election Title" required>
            <input type="number" name="num_candidates" placeholder="Number of Candidates" required>
            <input type="number" name="positions" placeholder="How Many Positions?" required>

            <div id="positions-section"></div>
            <div id="candidates-section"></div>

            <h3>Timeline</h3>
            <input type="datetime-local" name="start_time" required>
            <input type="datetime-local" name="end_time" required>

            <button type="submit" class="save-button">🚀 Save Election</button>
        </form>
    </div>

    <script>
        let positionNames = [];

        document.querySelector('input[name="positions"]').addEventListener('input', function() {
            const numPositions = this.value;
            const positionsSection = document.getElementById('positions-section');
            positionsSection.innerHTML = '';
            positionNames = [];

            for (let j = 1; j <= numPositions; j++) {
                const positionForm = `
                    <h3 style="color: #ffcc00;">Position ${j}</h3>
                    <input type="text" name="position_name_${j}" placeholder="Position Name (e.g., King, Queen)" required oninput="updatePositionNames(${j}, this.value)">
                `;
                positionsSection.innerHTML += positionForm;
            }
        });

        function updatePositionNames(index, value) {
            positionNames[index - 1] = value;
            updateCandidatePositionOptions();
        }

        function updateCandidatePositionOptions() {
            const positionOptions = positionNames.map(name => `<option value="${name}">${name}</option>`).join('');
            document.querySelectorAll('select[name^="position_"]').forEach(select => {
                select.innerHTML = `<option value="">Select Position</option>` + positionOptions;
            });
        }

        document.querySelector('input[name="num_candidates"]').addEventListener('input', function() {
            const numCandidates = this.value;
            const candidatesSection = document.getElementById('candidates-section');
            candidatesSection.innerHTML = '';

            for (let i = 1; i <= numCandidates; i++) {
                const candidateForm = `
                    <h3 style="color: #ffcc00;">Candidate ${i}</h3>
                    <input type="text" name="candidate_name_${i}" placeholder="Candidate Name" required>
                    <textarea name="candidate_platform_${i}" placeholder="Platform (Biography)" required></textarea>
                    <input type="file" name="candidate_photo_${i}" required>
                    <select name="position_${i}" required>
                        <option value="">Select Position</option>
                        ${positionNames.map(name => `<option value="${name}">${name}</option>`).join('')}
                    </select>
                `;
                candidatesSection.innerHTML += candidateForm;
            }
        });
    </script>

</body>

</html>