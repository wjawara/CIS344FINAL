<?php
class PharmacyDatabase {
    private $host = "localhost";       
    private $db_name = "pharmacy_portal_db";
    private $username = "root";         
    private $password = "";             
    public $conn;

    
    public function __construct() {
        $this->connectDB();
    }

   
    private function connectDB() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
    }

    public function addUser($userName, $contactInfo, $userType, $password) {
        $stmt = $this->conn->prepare("INSERT INTO Users (userName, contactInfo, userType, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $userName, $contactInfo, $userType, $password);
        return $stmt->execute();
    }
    

    
    public function addMedication($medicationName, $dosage, $manufacturer) {
        $stmt = $this->conn->prepare("INSERT INTO Medications (medicationName, dosage, manufacturer) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $medicationName, $dosage, $manufacturer);
        return $stmt->execute();
    }

    
    public function addPrescription($userId, $medicationId, $dosageInstructions, $quantity, $refillCount) {
        $stmt = $this->conn->prepare("INSERT INTO Prescriptions (userId, medicationId, prescribedDate, dosageInstructions, quantity, refillCount) VALUES (?, ?, NOW(), ?, ?, ?)");
        $stmt->bind_param("iisii", $userId, $medicationId, $dosageInstructions, $quantity, $refillCount);
        return $stmt->execute();
    }

    
    public function getUserDetails($userId) {
        $stmt = $this->conn->prepare("
            SELECT u.userName, u.contactInfo, u.userType, p.prescriptionId, p.medicationId, p.prescribedDate, p.quantity, p.refillCount
            FROM Users u
            LEFT JOIN Prescriptions p ON u.userId = p.userId
            WHERE u.userId = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }

    
    public function getMedicationInventory() {
        $query = "SELECT * FROM MedicationInventoryView";
        $result = $this->conn->query($query);
        return $result;
    }

    
    public function authenticateUser($userName, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM Users WHERE userName = ?");
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }

    
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
?>
