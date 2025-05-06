<?php
session_start();
if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'pharmacist') {
    header("Location: login.php");
    exit();
}

require_once 'PharmacyDatabase.php';
$pharmacyDB = new PharmacyDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicationName = $_POST['medicationName'];
    $dosage = $_POST['dosage'];
    $manufacturer = $_POST['manufacturer'];

    if ($pharmacyDB->addMedication($medicationName, $dosage, $manufacturer)) {
        echo "<p style='color:green;'>Medication added successfully.</p>";
    } else {
        echo "<p style='color:red;'>Failed to add medication.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Medication</title>
</head>
<body>
    <h2>Add New Medication</h2>
    <a href="pharmacist_dashboard.php">← Back to Dashboard</a><br><br>

    <form method="POST" action="">
        <label>Medication Name:</label><br>
        <input type="text" name="medicationName" required><br><br>

        <label>Dosage:</label><br>
        <input type="text" name="dosage" required><br><br>

        <label>Manufacturer:</label><br>
        <input type="text" name="manufacturer" required><br><br>

        <button type="submit">Add Medication</button>
    </form>
</body>
</html>
