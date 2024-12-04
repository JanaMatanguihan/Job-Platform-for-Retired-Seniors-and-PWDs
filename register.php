<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'jobplatform');

    if ($conn->connect_error) {
        echo "Connection failed: " . $conn->connect_error;
        exit;
    }

    // Input handling
    $fullName = $_POST['fullName'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $userType = $_POST['userType'] ?? null;

    // Validate inputs
    if (empty($fullName) || empty($email) || empty($password) || empty($userType)) {
        echo "All fields are required!";
        exit;
    }

    // Prepared statement
    $stmt = $conn->prepare("INSERT INTO registration (fullName, email, password, userType) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo "Failed to prepare statement: " . $conn->error;
        exit;
    }

    // Bind and execute
    $stmt->bind_param("ssss", $fullName, $email, $password, $userType);
    if (!$stmt->execute()) {
        echo "Failed to execute statement: " . $stmt->error;
        exit;
    }

    // Successful registration, redirect to homepage
    header("Location: homepage.html");
    exit;
}
?>

<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="register.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <title> Register to GABAY </title>
    </head>

<body>

    <div class="application">
        <form action="register.php" method="POST">
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

</body>

</html>
