<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // use pdo for connecting to the database
        $conn = new PDO('mysql:host=localhost;dbname=jobplatformdb', 'root', '');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // for inpute handling
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        // to validate input
        if (empty($email) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required!']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid email format!']);
            exit;
        }

        // Check if the email exists in the registration table
        $stmt = $conn->prepare("SELECT id FROM registration WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'User does not exist. Please register first.']);
            exit;
        }

        // Check if the id already exists in the login table
        $checkStmt = $conn->prepare("SELECT id FROM login WHERE id = :id");
        $checkStmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
        $checkStmt->execute();
        $existingLogin = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$existingLogin) {
            // Insert into login table
            $stmt = $conn->prepare("
                INSERT INTO login (id, email, password) 
                VALUES (:id, :email, :password)
            ");
            $stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->execute();
        }

        // Return a success response
        echo json_encode(['status' => 'success', 'message' => 'Login successful!']);
        exit;

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
        exit;
    }
}



?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 library -->
    <title> Login to GABAY </title>
</head>

<body>

    <div class="logo">
        <img src="white-logo.png" alt="GABAY" />
    </div>

    <div class="application">
        <form action="login.php" method="post">
            <h1>Login</h1>

            <div class="input-box">
                <input type="text" name="email" placeholder="Email" required>
                <i class="bi bi-person-fill"></i>
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class="bi bi-lock-fill"></i>
            </div>

            <div class="remember-forgot">
                <label>
                    <input type="checkbox"> Remember me
                </label>
                <a href="#">Forgot Password?</a>
            </div>

            <div class="login">
                <button type="submit" class="button">Login</button>
            </div>

            <div class="register-link">
                <p>Don't have an account yet? <a href="register.php"> Register </a></p>
            </div>
        </form>
    </div>
</body>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch('login.php', {
            method: 'POST',
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500,
                    }).then(() => {
                        Swal.fire({
                            title: 'Redirecting...',
                            html: 'Please wait while we take you to the homepage.',
                            timer: 2000,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        }).then(() => {
                            window.location.href = 'homepage.html';
                        });
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message,
                    });
                }
            })
            .catch((error) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong. Please try again later.',
                });
            });
    });
});

</script>


</html>
