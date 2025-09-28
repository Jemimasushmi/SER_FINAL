<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employee_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $sql = "SELECT * FROM employees WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['employee_id'] = $employee_id;
        header("Location: dashboard.php");
    } else {
        echo "Invalid Employee ID";
    }
}
$conn->close();
?>
