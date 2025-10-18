<?php
session_start();

// Step 1: Redirect if not logged in
if (!isset($_SESSION['email'])) {
  header("Location: ../login/login.php");
  exit();
}

// Step 2: Connect to the database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "userdb";

$conn = new mysqli($host, $user, $pass, $db);

// Step 3: Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Step 4: Get data of the logged-in user
$email = $_SESSION['email'];
$sql = "SELECT firstname, lastname, email,phone FROM userinfo WHERE email = '$email'";
$result = $conn->query($sql);

// Step 5: Fetch and store in variables
if ($result && $result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $firstname = $row['firstname'];
  $lastname = $row['lastname'];
  $email = $row['email'];
  $phone = $row['phone'];
} else {
  $firstname = "";
  $lastname = "";
  $email = "";
  $phone = "";
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <title>Customer Complaint Form</title>
  
  <link rel="stylesheet" href="../css/report.css">
</head>
<body>

  <div class="container">
    <h2>Complaint Form</h2>
    <p class="subtitle">Please provide us with details of your complaint so that we can address it promptly.</p>

    <form action="submit_compliant.php" method="POST" enctype="multipart/form-data">

      <div class="row">
        <div class="col">
          <label>First Name</label>
          <input type="text" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>"  required>
        </div>
        <div class="col">
          <label>Last Name</label>
          <input type="text" name="lastname" value="<?php echo htmlspecialchars($lastname); ?>"  required>
        </div>
      </div>

      <div class="row">
        <div class="col">
          <label>Email Address</label>
          <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="col">
          <label>Phone Number</label>
          <input type="tel" name="phone" placeholder="+63 912 345 6789" value="<?php echo htmlspecialchars($phone); ?>" required>
        </div>
      </div>

      <div class="col">
        <label>Date of Complaint</label>
        <input type="date" name="date" required>
      </div>

      <div class="form-group">
        <label for="location">Choose Location</label>
        <div id="map"></div>
        <input type="text" id="location" name="location" placeholder="Selected coordinates" required>

        <label for="address">Address</label>
        <input type="text" id="address" name="address" placeholder="Address" readonly>
      </div>

      <label>Attach your photo or video</label>
      <div class="file-upload">
        <input type="file" name="supporting_files" required>
      </div>

      <div class="col">
        <label for="category">Category</label>
        <div class="custom-select">
          <select name="category" id="category" required>
            <option value="" disabled selected>Select Category</option>
            <option value="road">Road Damage</option>
            <option value="lights">Street Lights Broken</option>
            <option value="flood">Flood</option>
            <option value="agriculture">Agricultural</option>
            <option value="agriculture">Waste Management</option>
            <option value="others">Others</option>
          </select>
        </div>
      </div>

      <label>Tell us what happened</label>
      <textarea name="report" placeholder="Describe the incident in detail" required></textarea>

      <button type="submit" class="submit-btn">Submit</button>
    </form>
  </div>

   <!-- MODAL -->
    <div class="popup center">
        <div class="icon">
            <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile" class="profile-pic">
        </div>
        <div class="title">You need to verify your profile!</div>
        <div class="description">Please complete your profile before you can submit a report.</div>
        <div class="verify-btn">
            <button id="verify-popup-btn">Okay</button>
        </div>
    </div>

  <script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>
  <script src="user.js"></script>
</body>
</html>
