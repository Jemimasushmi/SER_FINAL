<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: index.php");
    exit;
}

$employee_id = $_SESSION['employee_id'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employee_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM employees WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, Employee <?= $employee['id'] ?></h1>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="audio" accept=".wav" required />
        <button type="submit">Upload</button>
    </form>
    <h2>Satisfaction Count: <?= $employee['satisfaction_count'] ?></h2>
    <h2>Dissatisfaction Count: <?= $employee['dissatisfaction_count'] ?></h2>
    <a href="analyse.php">Analyse Communication</a>
</body>
</html>
