<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: index.php");
    exit;
}

$employee_id = $_SESSION['employee_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['audio']) && $_FILES['audio']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['audio']['tmp_name'];
        $fileName = $_FILES['audio']['name'];
        $filePath = 'uploads/' . $fileName;
        move_uploaded_file($fileTmpPath, $filePath);

        // Call Python script for emotion detection
        $emotion = detectEmotion($filePath);

        // Update database with emotion
        updateSatisfaction($employee_id, $emotion);
        header("Location: dashboard.php");
        exit;
    }
}

function detectEmotion($audioPath) {
    $command = escapeshellcmd("python detect_emotion.py " . $audioPath);
    $output = shell_exec($command);
    return trim($output); // Expecting emotion like 'happy', 'sad', etc.
}

function updateSatisfaction($employee_id, $emotion) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "employee_db";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $column = ($emotion == 'happy' || $emotion == 'neutral') ? 'satisfaction_count' : 'dissatisfaction_count';
    $sql = "UPDATE employees SET $column = $column + 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $conn->close();
}
?>
