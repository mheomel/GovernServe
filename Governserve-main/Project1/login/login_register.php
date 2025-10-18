<?php
session_start();
require_once 'config.php';

// REGISTER
if (isset($_POST['register'])) {
    $firstname = $_POST['firstname'];
    $lastname  = $_POST['lastname'];
    $email     = $_POST['email'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];

    if ($password1 !== $password2) {
        $_SESSION['register_error'] = " Passwords do not match.";
        $_SESSION['active_form'] = 'register';
        header("Location: login.php");
        exit();
    }

    // check email duplicate
    $checkmail = $conn->query("SELECT email FROM userinfo WHERE email = '$email'");
    if ($checkmail->num_rows > 0) {
        $_SESSION['register_error'] = " Email already registered.";
        $_SESSION['active_form'] = 'register';
        header("Location: login.php");
        exit();
    }

    // insert plain password
    $conn->query("INSERT INTO userinfo (firstname, lastname, email, password) 
                  VALUES ('$firstname', '$lastname', '$email', '$password1')");

    $_SESSION['register_success'] = " Registered successfully. Please log in.";
    $_SESSION['active_form'] = 'login';  // go back to login form
    header("Location: login.php");
    exit();
}

// LOGIN
if (isset($_POST['login'])) {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM userinfo WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($password === $user['password']) {
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role']; // Save role in session

            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../user/users.php");
            }
            exit();
        } else {
            $_SESSION['login_error'] = " Wrong password.";
        }
    } else {
        $_SESSION['login_error'] = " No user found.";
    }

    $_SESSION['active_form'] = 'login';
    header("Location: login.php");
    exit();
}
?>
