<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "userdb";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check login session
$email = $_SESSION['email'] ?? '';
if (empty($email)) {
    header("Location: login.php");
    exit;
}

// Handle form submission (when user clicks Save)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname  = $_POST['first_name'] ?? '';
    $lastname   = $_POST['last_name'] ?? '';
    $phone      = $_POST['phone'] ?? '';
    $city       = $_POST['city'] ?? '';
    $barangay   = $_POST['barangay'] ?? '';
    $street     = $_POST['street'] ?? '';
    $house_no   = $_POST['house_no'] ?? '';
    $email      =$_POST['email'] ?? '';
    $profile_image = null;

    // If new image is uploaded, get its binary data
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $profile_image = file_get_contents($_FILES['profile_image']['tmp_name']);
    }

    // If image is uploaded, include it in the query
    if ($profile_image !== null) {
        $sql = "UPDATE userinfo 
                SET firstname=?, lastname=?, phone=?, city=?, barangay=?, street=?, house_number=?, profile_image=?, email=?
                WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss", $firstname, $lastname, $phone, $city, $barangay, $street, $house_no, $profile_image, $email);
    } else {
        $sql = "UPDATE userinfo 
                SET firstname=?, lastname=?, phone=?, city=?, barangay=?, street=?, house_number=?, email=?
                WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $firstname, $lastname, $phone, $city, $barangay, $street, $house_no, $email);
    }

    if ($stmt->execute()) {
        $msg = " Profile updated successfully!";
    } else {
        $msg = " Failed to update profile.";
    }
}

// Fetch user info (including profile image)
$sql = "SELECT firstname, lastname, email, phone, city, barangay, street, house_number, profile_image 
        FROM userinfo WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Store fields in variables
$firstname = $user['firstname'] ?? '';
$lastname  = $user['lastname'] ?? '';
$email     = $user['email'] ?? '';
$phone     = $user['phone'] ?? '';
$city      = $user['city'] ?? '';
$barangay  = $user['barangay'] ?? '';
$street    = $user['street'] ?? '';
$house_no  = $user['house_number'] ?? '';
$profile_image = $user['profile_image'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Account Settings</title>
  <link rel="stylesheet" href="../css/profile.css">
</head>
<body>
<div class="container">
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Settings</h2>
    <ul>
      <li class="active" onclick="showSection('account', event)">Account</li>
      <li onclick="showSection('notifications', event)">Notifications</li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main">
    <h1>Account Settings</h1>
    <?php if (isset($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

    <div id="account" class="section">
      <h2>My Profile</h2>

      <form method="POST" enctype="multipart/form-data">
        <div class="profile-pic">
          <?php if ($profile_image): ?>
            <img id="profileImage" 
                 src="data:image/jpeg;base64,<?php echo base64_encode($profile_image); ?>" 
                 alt="Profile Picture" width="120">
          <?php else: ?>
            <img id="profileImage" src="../img/default.png" alt="Profile Picture" width="120">
          <?php endif; ?>

          <div>
            <input type="file" id="uploadInput" name="profile_image" accept="image/*" style="display:none">
            <button type="button" class="btn-change" onclick="document.getElementById('uploadInput').click()">Change Image</button>
          </div>
        </div>

        <label>First Name</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($firstname); ?>" required>

        <label>Last Name</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($lastname); ?>" required>

        <label>Phone Number</label>
        <input type="tel" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>

        <h3>Address</h3>
        <label>City</label>
        <input type="text" name="city" value="<?php echo htmlspecialchars($city); ?>" required>

        <label>Barangay</label>
        <input type="text" name="barangay" value="<?php echo htmlspecialchars($barangay); ?>" required>

        <label>Street</label>
        <input type="text" name="street" value="<?php echo htmlspecialchars($street); ?>" required>

        <label>House Number</label>
        <input type="text" name="house_no" value="<?php echo htmlspecialchars($house_no); ?>" required>

        <button class="btn" id="save-profile" type="submit">Save Profile</button>
      </form>
    </div>

        <!-- Security -->
    <div id="security" class="section">
      <h2>Account Security</h2>

      <label>Email</label>
      <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
      <button class="btn">Change Email</button>

      <label>Change Password</label>
      <input type="password" placeholder="********">

      <label>Confirm Password</label>
      <input type="password" placeholder="********">
      <button class="btn">Change Password</button>
    </div>

    <div id="notifications" class="section" style="display:none;">
      <h2>Notifications</h2>
      <p>HELLO WORLD</p>
    </div>
  </div>
</div>

<script>
function showSection(section, event) {
  document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
  document.getElementById(section).style.display = 'block';
  document.querySelectorAll('.sidebar li').forEach(li => li.classList.remove('active'));
  event.target.classList.add('active');
}
</script>
</body>
</html>
