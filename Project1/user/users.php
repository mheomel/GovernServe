<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../login/login.php");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "userdb";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];

// Fetch user info
$sql = "SELECT firstname, lastname, profile_image FROM userinfo WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$firstname = $user['firstname'] ?? '';
$lastname = $user['lastname'] ?? '';
$profileImage = !empty($user['profile_image']) ? "../" . $user['profile_image'] : "../img/default-avatar.png";
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Your CSS -->
    <link rel="stylesheet" href="../css/users.css">
    <link rel="stylesheet" href="../chatbot/chatbot.css">
    <title>User Dashboard</title>
</head>

<body>
    <header>
        <input type="checkbox" id="toggler">
        <label for="toggler" class="fas fa-bars"></label>
        <a href="#" class="logo">Govern<span>Serve</span></a>

        <nav class="nav-bar">
            <a href="#home">Home</a>
            <a href="#" id="report-btn">Report</a>
            <a href="#about">About</a>
            <a href="#services">Services</a>
        </nav>

       <div class="user-info">
            <span class="user-name"><?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></span>
            <a href="profile.php">
                <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Image" class="user-pic">
            </a>
       </div>
    </header>

    <!-- Home Section -->
    <section class="home" id="home">
        <div class="content">
            <h3>Smart Governance</h3>
            <span>From Reports to Results—Quick and Clear</span>
            <p>GovernServe is a smart public service request and response system <br>
                designed to improve transparency and efficiency in governance. <br>
                It empowers citizens to easily report community issues such as <br>
                waste management, road repairs, and utility concerns, while enabling<br>
                local governments to respond faster and more effectively.</p>
            <a href="#" class="btn" id="report-btn-main">Report an issue</a>
        </div>
    </section>

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

    <!-- About Section -->
    <section class="about" id="about">
        <h1 class="heading"><span>About</span> us</h1>

        <div class="row">
            <div class="video-container">
                <video src="../img/background-vid.mp4" loop autoplay muted></video>
            </div>

            <div class="content">
                <h3>Why choose us?</h3>
                <p>At GovernServe, we believe that good governance starts with listening
                    to the people. Our platform makes it simple, fast, and transparent for
                    citizens to report issues and for local governments to act on them.</p>
                <a href="#" class="btn">Learn more</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <h1 class="heading-services">Services</h1>
    <section class="icons-container" id="services">
        <div class="icons">
            <img src="../img/annual-budget.svg" alt="">
            <div class="info"><h3></h3></div>
        </div>

        <a href="../services/educational_assistance.html" class="icons">
            <img src="../img/biddings and projects.svg" alt="">
            <div class="info"><h3>Educational Assistance</h3></div>
        </a>

        <a href="../services/medical_assistance.html" class="icons">
            <img src="../img/medical.svg" alt="">
            <div class="info"><h3>Medical Assistance</h3></div>
        </a>

        <div class="icons">
            <img src="../img/agri.svg" alt="">
            <div class="info"><h3>Agricultural Assistance</h3></div>
        </div>

        <div class="icons">
            <img src="../img/handshake.svg" alt="">
            <div class="info"><h3>Job Offers</h3></div>
        </div>

        <div class="icons">
            <img src="../img/globe.svg" alt="">
            <div class="info"><h3>eServices</h3></div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footerContaioner">
            <div class="socialIcons">
                <a href=""><i class="fa-brands fa-facebook"></i></a>
                <a href=""><i class="fa-brands fa-instagram"></i></a>
                <a href=""><i class="fa-brands fa-twitter"></i></a>
                <a href=""><i class="fa-brands fa-google-plus"></i></a>
            </div>
            <div class="footerNav">
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="">Report</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#about">About</a></li>
                </ul>
            </div>
            <div class="footerBottom">
                <p>Copyright &copy;2025</p>
            </div>
        </div>
    </footer>

    <!-- JS -->
    <script>
    let isIncomplete = false;

    // Attach events to both Report buttons
    document.getElementById("report-btn").addEventListener("click", checkProfile);
    document.getElementById("report-btn-main").addEventListener("click", checkProfile);

    function checkProfile(e) {
        e.preventDefault();
        fetch("check_profile.php")
            .then(res => res.json())
            .then(data => {
                if (data.status === "complete") {
                    window.location.href = "Reports.php";
                } else {
                    isIncomplete = true;
                    document.querySelector(".popup").classList.add("active");
                }
            })
            .catch(err => console.error("Error:", err));
    }

    document.getElementById("verify-popup-btn").addEventListener("click", function() {
        document.querySelector(".popup").classList.remove("active");
        if (isIncomplete) {
            window.location.href = "profile.php";
        }
    });
    </script>
    <!-- ✅ Floating Chatbot -->
    <button id="chatToggle" class="chat-toggle">
        <img src="https://cdn-icons-png.flaticon.com/128/1370/1370907.png" alt="Chat" />
    </button>

    <div id="chatWrapper" class="chat-wrapper hidden">
        <main class="chat-container">
        <h1>Chat Bot</h1>
        <div id="chat" class="chat-window" aria-live="polite"></div>
        <form id="msgForm" class="input-row">
            <input id="message" type="text" placeholder="Ask something..." autocomplete="off" required />
            <button type="submit">Send</button>
        </form>
        </main>
    </div>

    <!-- Scripts -->
    <script src="../chatbot/chatbot.js"></script>
</body>
</html>

