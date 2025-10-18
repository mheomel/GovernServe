<?php
session_start();
$activeForm = $_SESSION['active_form'] ?? 'login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Page</title>
  <link rel="stylesheet" href="../css/style.css">

  <style>
    .error { 
    color: red; 
    margin-bottom: 10px; 
    }
    .success { 
        color: green; 
        margin-bottom: 10px; 
    }
    .card-box { 
        display: none; 
    }
    .card-box.active { 
        display: block; 
    }
    </style>

</head>
<body>
  <div class="container">

    <!-- LOGIN -->
    <div class="card-box <?= $activeForm === 'login' ? 'active' : '' ?>" id="login">
      <?php if (isset($_SESSION['login_error'])): ?>
        <p class="error"><?= $_SESSION['login_error']; ?></p>
        <?php unset($_SESSION['login_error']); ?>
      <?php endif; ?>

      <?php if (isset($_SESSION['register_success'])): ?>
        <p class="success"><?= $_SESSION['register_success']; ?></p>
        <?php unset($_SESSION['register_success']); ?>
      <?php endif; ?>

      <form action="login_register.php" method="post">
        <h2>Log in</h2>
        <div class="input-box">
          <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="input-box">
          <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" name="login">Login</button>
        <div class="links">
          <p>Don’t have an account? <a href="#" onclick="showForm('register')">Create</a></p>
        </div>
      </form>
    </div>

    <!-- REGISTER -->
    <div class="card-box <?= $activeForm === 'register' ? 'active' : '' ?>" id="register">
      <?php if (isset($_SESSION['register_error'])): ?>
        <p class="error"><?= $_SESSION['register_error']; ?></p>
        <?php unset($_SESSION['register_error']); ?>
      <?php endif; ?>

      <form action="login_register.php" method="post">
        <h2>Register</h2>
        <div class="input-box">
          <input type="text" name="firstname" placeholder="First Name" required>
        </div>
        <div class="input-box">
          <input type="text" name="lastname" placeholder="Last Name" required>
        </div>
        <div class="input-box">
          <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="input-box">
          <input type="password" name="password1" placeholder="Create Password" required>
        </div>
        <div class="input-box">
          <input type="password" name="password2" placeholder="Confirm Password" required>
        </div>
        <button type="submit" name="register">Register</button>
        <div class="links">
          <p>Already have an account? <a href="#" onclick="showForm('login')">Login</a></p>
        </div>
      </form>
    </div>
  </div>

  <script>
    function showForm(form) {
      document.getElementById('login').classList.remove('active');
      document.getElementById('register').classList.remove('active');
      document.getElementById(form).classList.add('active');
    }
  </script>
</body>
</html>
<?php unset($_SESSION['active_form']); ?>
