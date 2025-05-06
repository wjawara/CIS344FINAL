<?php
session_start();
if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'pharmacist') {
    header("Location: login.php");
    exit();
}

require_once 'PharmacyDatabase.php';
$pharmacyDB = new PharmacyDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['userId'];
    $medicationId = $_POST['medicationId'];
    $dosageInstructions = $_POST['dosageInstructions'];
    $quantity = $_POST['quantity'];
    $refillCount = $_POST['refillCount'];

    if ($pharmacyDB->addPrescription($userId, $medicationId, $dosageInstructions, $quantity, $refillCount)) {
        echo "<p style='color:green;'>Prescription added successfully.</p>";
    } else {
        echo "<p style='color:red;'>Failed to add prescription.</p>";
    }
}

$users = $pharmacyDB->conn->query("SELECT userId, userName FROM Users WHERE userType='patient'");
$meds = $pharmacyDB->conn->query("SELECT medicationId, medicationName FROM Medications");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Prescription</title>
</head>
<body>
    <h2>Add Prescription</h2>
    <a href="pharmacist_dashboard.php">← Back to Dashboard</a><br><br>

    <form method="POST" action="">
        <label>Patient:</label><br>
        <select name="userId" required>
            <?php while ($u = $users->fetch_assoc()) {
                echo "<option value='{$u['userId']}'>{$u['userName']}</option>";
            } ?>
        </select><br><br>

        <label>Medication:</label><br>
        <select name="medicationId" required>
            <?php while ($m = $meds->fetch_assoc()) {
                echo "<option value='{$m['medicationId']}'>{$m['medicationName']}</option>";
            } ?>
        </select><br><br>

        <label>Dosage Instructions:</label><br>
        <textarea name="dosageInstructions" required></textarea><br><br>

        <label>Quantity:</label><br>
        <input type="number" name="quantity" min="1" required><br><br>

        <label>Refill Count:</label><br>
        <input type="number" name="refillCount" min="0" value="0"><br><br>

        <button type="submit">Add Prescription</button>
    </form>
</body>
</html>
