<?php
session_start();
if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'patient') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Dashboard</title>
</head>
<body>
    <h2>Welcome, Patient</h2>
    <a href="logout.php">Logout</a><br><br>

    <ul>
        <li><a href="viewPrescriptions.php">📋 View My Prescriptions</a></li>
    </ul>
</body>
</html>
