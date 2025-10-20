<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login/login.php");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "userdb";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get logged-in email
$email = $_SESSION['email'];

// Get form POST data
$date = $_POST['date'] ?? NULL;
$location = $_POST['location'] ?? NULL;
$address = $_POST['address'] ?? NULL;
$category = $_POST['category'] ?? NULL;
$report = $_POST['report'] ?? NULL;

// Handle file upload
$file_name = NULL;
if (isset($_FILES['supporting_files']) && $_FILES['supporting_files']['error'] == 0) {
    $target_dir = "../uploads/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    $file_name = basename($_FILES["supporting_files"]["name"]);
    move_uploaded_file($_FILES["supporting_files"]["tmp_name"], $target_dir . $file_name);
}

// Insert complaint using email as foreign key
$sql = "INSERT INTO complaints (email, date, location, address, category, report, file)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $email, $date, $location, $address, $category, $report, $file_name);

if ($stmt->execute()) {
    echo "<script>alert('Complaint submitted successfully!'); window.location.href='Reports.php';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
