<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "userdb";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

$email = $_SESSION['email'] ?? '';
if (empty($email)) {
    echo json_encode(['status' => 'not_logged_in']);
    exit;
}

$firstname = $_POST['firstname'] ?? '';
$lastname = $_POST['lastname'] ?? '';
$phone = $_POST['phone'] ?? '';
$city = $_POST['city'] ?? '';
$barangay = $_POST['barangay'] ?? '';
$street = $_POST['street'] ?? '';
$house_number = $_POST['house_number'] ?? '';

$imagePath = null;

// Handle image upload
if (!empty($_FILES['profile_image']['name'])) {
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $imageName = time() . "_" . basename($_FILES["profile_image"]["name"]);
    $targetFile = $targetDir . $imageName;

    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFile)) {
        $imagePath = "uploads/" . $imageName;
    }
}

// Update the database
if ($imagePath) {
    $sql = "UPDATE userinfo 
            SET firstname=?, lastname=?, phone=?, city=?, barangay=?, street=?, house_number=?, profile_image=? 
            WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $firstname, $lastname, $phone, $city, $barangay, $street, $house_number, $imagePath, $email);
} else {
    $sql = "UPDATE userinfo 
            SET firstname=?, lastname=?, phone=?, city=?, barangay=?, street=?, house_number=? 
            WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $firstname, $lastname, $phone, $city, $barangay, $street, $house_number, $email);
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully', 'image' => $imagePath]);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}
?>
