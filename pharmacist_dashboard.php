<?php
session_start();
if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'pharmacist') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pharmacist Dashboard</title>
</head>
<body>
    <h2>Welcome, Pharmacist</h2>
    <a href="logout.php">Logout</a><br><br>

    <ul>
        <li><a href="viewInventory.php"> View Medication Inventory</a></li>
        <li><a href="addMedication.php"> Add New Medication</a></li>
        <li><a href="addPrescription.php"> Assign Prescription to Patient</a></li>
        <li><a href="processSale.php"> Process Sale</a></li>
    </ul>
</body>
</html>
