<?php
session_start();
if (isset($_SESSION['employee_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Login</title>
</head>
<body>
    <h1>Employee Login</h1>
    <form method="POST" action="login.php">
        <label>Employee ID:</label>
        <input type="text" name="employee_id" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>
