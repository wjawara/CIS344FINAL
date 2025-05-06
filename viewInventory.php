<?php
session_start();
if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'pharmacist') {
    header("Location: login.php");
    exit();
}

require_once 'PharmacyDatabase.php';
$pharmacyDB = new PharmacyDatabase();
$inventory = $pharmacyDB->getMedicationInventory();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory - Pharmacist</title>
</head>
<body>
    <h2>Medication Inventory</h2>
    <a href="pharmacist_dashboard.php">← Back to Dashboard</a><br><br>

    <table border="1" cellpadding="8">
        <tr>
            <th>Medication Name</th>
            <th>Dosage</th>
            <th>Manufacturer</th>
            <th>Total Quantity</th>
        </tr>
        <?php while ($row = $inventory->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['medicationName']) ?></td>
            <td><?= htmlspecialchars($row['dosage']) ?></td>
            <td><?= htmlspecialchars($row['manufacturer']) ?></td>
            <td><?= htmlspecialchars($row['totalQuantity']) ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
