<?php
session_start();
require_once 'PharmacyDatabase.php';

$pharmacyDB = new PharmacyDatabase();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userName = $_POST['userName'];
    $password = $_POST['password'];

    $stmt = $pharmacyDB->conn->prepare("SELECT * FROM Users WHERE userName = ?");
    $stmt->bind_param("s", $userName);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['userId'] = $user['userId'];
        $_SESSION['userName'] = $user['userName'];
        $_SESSION['userType'] = $user['userType'];

        // Redirect based on role
        if ($user['userType'] == 'pharmacist') {
            header("Location: pharmacist_dashboard.php");
        } else {
            header("Location: patient_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Pharmacy Portal</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="">
        <label>Username:</label><br>
        <input type="text" name="userName" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</body>
</html>
