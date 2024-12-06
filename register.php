<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Database connection with PDO
        $conn = new PDO('mysql:host=localhost;dbname=jobplatformdb', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Input handling
        $fullName = $_POST['fullName'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $userType = $_POST['userType'] ?? null;

        // Validate inputs
        if (empty($fullName) || empty($email) || empty($password) || empty($userType)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required!']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid email format!']);
            exit;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepared statement with PDO
        $stmt = $conn->prepare("
            INSERT INTO registration (fullName, email, password, userType) 
            VALUES (:fullName, :email, :password, :userType)
        ");

        // Bind parameters
        $stmt->bindParam(':fullName', $fullName, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':userType', $userType, PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Return success JSON
        echo json_encode(['status' => 'success', 'message' => 'Registration successful!']);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="register.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title> Register to GABAY </title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<div class="logo">
    <img src="white-logo.png" alt="GABAY" />
</div>

<div class="application">
    <form id="registrationForm" action="register.php" method="POST">
        <h1> Register </h1>
        <div class="input-box">
            <input type="text" name="fullName" placeholder="Enter Your Name" required>
        </div>

        <div class="input-box">
            <input type="text" name="email" placeholder="Email" required>
        </div>

        <div class="input-box">
            <input type="password" name="password" placeholder="Enter Password" required>
        </div>

        <div class="user-type">
            <label>
                <input type="checkbox" name="userType" value="Employer"> Employer
            </label>
            <label>
                <input type="checkbox" name="userType" value="Employee"> Employee
            </label>
        </div>

        <div class="remember-me">
            <label>
                <input type="checkbox"> Remember me
            </label>
        </div>

        <div class="register">
            <button type="submit" class="button"> Register </button>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        fetch('register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    Swal.fire({
                        title: 'Logging in...',
                        html: 'Please wait while we redirect you to the homepage',
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    }).then(() => {
                        window.location.href = 'homepage.html';
                    });
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!'
            });
        });
    });
});
</script>

</body>

</html>
