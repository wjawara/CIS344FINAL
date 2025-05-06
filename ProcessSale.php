<?php
session_start();
if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'pharmacist') {
    header("Location: login.php");
    exit();
}

require_once 'PharmacyDatabase.php';
$pharmacyDB = new PharmacyDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prescriptionId = $_POST['prescriptionId'];
    $quantitySold = $_POST['quantitySold'];

    if ($pharmacyDB->processSale($prescriptionId, $quantitySold)) {
        echo "<p style='color:green;'>Sale processed successfully.</p>";
    } else {
        echo "<p style='color:red;'>Sale failed. Check stock and prescription.</p>";
    }
}


$prescriptions = $pharmacyDB->conn->query("
    SELECT p.prescriptionId, u.userName, m.medicationName
    FROM Prescriptions p
    JOIN Users u ON u.userId = p.userId
    JOIN Medications m ON m.medicationId = p.medicationId
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Process Sale</title>
</head>
<body>
    <h2>Process Sale</h2>
    <a href="pharmacist_dashboard.php">← Back to Dashboard</a><br><br>

    <form method="POST" action="">
        <label>Prescription:</label><br>
        <select name="prescriptionId" required>
            <?php while ($p = $prescriptions->fetch_assoc()) {
                echo "<option value='{$p['prescriptionId']}'>Prescription #{$p['prescriptionId']} - {$p['userName']} ({$p['medicationName']})</option>";
            } ?>
        </select><br><br>

        <label>Quantity Sold:</label><br>
        <input type="number" name="quantitySold" min="1" required><br><br>

        <button type="submit">Process Sale</button>
    </form>
</body>
</html>
