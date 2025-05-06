<?php
session_start();
require_once 'PharmacyDatabase.php';

$pharmacyDB = new PharmacyDatabase();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userName = $_POST['userName'];
    $contactInfo = $_POST['contactInfo'];
    $userType = $_POST['userType'];
    $password = $_POST['password'];
    
  
    $hashedPassword = $pharmacyDB->hashPassword($password);
    

    $stmt = $pharmacyDB->conn->prepare("INSERT INTO Users (userName, contactInfo, userType, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $userName, $contactInfo, $userType, $hashedPassword);

    if ($stmt->execute()) {
        echo "Registration successful! <a href='login.php'>Login Here</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Pharmacy Portal</title>
</head>
<body>
    <h2>Register</h2>
    <form method="POST" action="">
        <label>Username:</label><br>
        <input type="text" name="userName" required><br><br>

        <label>Contact Info:</label><br>
        <input type="text" name="contactInfo" required><br><br>

        <label>User Type:</label><br>
        <select name="userType" required>
            <option value="pharmacist">Pharmacist</option>
            <option value="patient">Patient</option>
        </select><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
