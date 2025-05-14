<?php
session_start();
if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'patient') {
    header("Location: login.php");
    exit();
}

require_once 'PharmacyDatabase.php';
$pharmacyDB = new PharmacyDatabase();
$result = $pharmacyDB->getUserDetails($_SESSION['userId']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Prescriptions</title>
</head>
<body>
    <h2>Your Prescriptions</h2>
    <a href="patient_dashboard.php">← Back to Dashboard</a><br><br>

    <table border="1" cellpadding="8">
        <tr>
            <th>Prescription ID</th>
            <th>Dosage Instructions</th>
            <th>Quantity</th>
            <th>Medication Name</th>
            <th>Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) {
            if ($row['prescriptionId']) {
        ?>
        <tr>
            <td><?= $row['prescriptionId'] ?></td>
        
            <td><?= htmlspecialchars($row['dosageInstructions']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= $row['medicationName'] ?></td>
            <td><?= $row['prescribedDate'] ?></td>
        </tr>
        <?php }} ?>
    </table>
</body>
</html>


