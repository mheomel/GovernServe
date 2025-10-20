<?php
session_start();
header('Content-Type: application/json');

//  Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo json_encode(["status" => "no_profile"]);
    exit();
}

//  Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "userdb";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

$email = $_SESSION['email'];

//  Fetch user data from `userinfo`
$sql = "SELECT firstname, lastname, phone, city, barangay, street, house_number 
        FROM userinfo 
        WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "no_profile"]);
    exit();
}

$user = $result->fetch_assoc();

//  Check if all required fields are filled
$required = ['firstname', 'lastname', 'phone', 'city', 'barangay', 'street', 'house_number'];
$incomplete = false;

foreach ($required as $field) {
    if (empty($user[$field])) {
        $incomplete = true;
        break;
    }
}

if ($incomplete) {
    echo json_encode(["status" => "incomplete"]);
} else {
    echo json_encode(["status" => "complete"]);
}

$conn->close();
?>
