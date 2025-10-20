<?php
session_start();

include '../login/config.php'; // adjust path if needed

$totalUsers = 0;
$sql = "SELECT COUNT(*) as total FROM userinfo"; // assuming your users table is named 'users'
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $totalUsers = $row['total'];
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login/login_register.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | GovernServe</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="css/admin-style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo">
      <h2><span class="govern">Govern</span><span class="serve">Serve</span></h2>
    </div>
    <ul class="menu">
      <li class="active"><a href="#"><i class="fa-solid fa-grip"></i> Dashboard</a></li>
      <li><a href="users.php"><i class="fa-solid fa-users"></i> Users</a></li>
      <li><a href="reports.php"><i class="fa-solid fa-pen"></i> Reports</a></li>
      <li><a href="map.php"><i class="fa-solid fa-map-location"></i> Map</a></li>
    </ul>
    <div class="bottom-menu">
      <a href="#"><i class="fa-solid fa-gear"></i> Settings</a>
      <a href="../login/logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <header class="topbar">
      <div class="title-container">
        <h1>Dashboard</h1>
      </div>
      <div class="profile">
        <img src="images/admin-avatar.jpg" alt="Admin">
        <div>
          <p class="name">Admin</p>
          <p class="role">Admin</p>
        </div>
      </div>
    </header>

    <!-- Dashboard Cards -->
    <section class="cards">
      <div class="card purple">
        <h3>Total Users</h3>
         <p><?php echo $totalUsers; ?></p>
      </div>
      <div class="card green">
        <h3>Completed</h3>
        <p>259</p>
      </div>
      <div class="card red">
        <h3>Rejected</h3>
        <p>45</p>
      </div>
      <div class="card yellow">
        <h3>Total Pending</h3>
        <p>105</p>
      </div>
    </section>

    <!-- Chart Section -->
    <section class="chart-section">
      <h2>Report Details</h2>
      <canvas id="reportChart" width="1000" height="350"></canvas>
    </section>
  </main>

  <script>
    const ctx = document.getElementById('reportChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Pending', 'Completed', 'Rejected'],
        datasets: [{
          label: 'Reports',
          data: [105, 259, 45],
          backgroundColor: ['#f3c623', '#3cb371', '#ff6b6b'],
        }]
      },
      options: {
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  </script>

</body>
</html>
