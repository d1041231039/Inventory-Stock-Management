<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($username === '' || $email === '' || $password === '' || $password2 === '') {
        echo "<script>alert('All fields must be filled!'); window.history.back();</script>";
        exit();
    }

    if ($password !== $password2) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    if (strlen($password) < 8) {
        echo "<script>alert('Password must be at least 8 characters long!'); window.history.back();</script>";
        exit();
    }
    if (!preg_match('/[A-Z]/', $password)) {
        echo "<script>alert('Password must contain at least one uppercase letter!'); window.history.back();</script>";
        exit();
    }
    if (!preg_match('/[a-z]/', $password)) {
        echo "<script>alert('Password must contain at least one lowercase letter!'); window.history.back();</script>";
        exit();
    }

    $stmt = $conn->prepare("SELECT ID_User FROM user WHERE Email = ?");
    if (!$stmt) {
        echo "<script>alert('Database error (prepare).'); window.history.back();</script>";
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        echo "<script>alert('Email is already registered!'); window.history.back();</script>";
        exit();
    }
    $stmt->close();

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $ins = $conn->prepare("INSERT INTO user (Username, Email, Password) VALUES (?, ?, ?)");
    if (!$ins) {
        echo "<script>alert('Database error (prepare insert).'); window.history.back();</script>";
        exit();
    }
    $ins->bind_param("sss", $username, $email, $password_hash);

    if ($ins->execute()) {
        $ins->close();
        echo "<script>alert('Account created successfully. Please login.'); window.location.href='login.php';</script>";
        exit();
    } else {
        $ins->close();
        echo "<script>alert('Failed to create account.'); window.history.back();</script>";
        exit();
    }
}
?>

<html>
    <head>
        <title>SIGMA</title>
        <link rel="stylesheet" href="register.css">
    </head>

    <body>
        <div class="container">
            <p class="title">Sign up</p>
            <form method="post" action="register.php">
                <input class="input" name="username" type="text" placeholder="Enter your name" id="username">
                <br>
                <input class="input" name="email" type="text" placeholder="Enter your email" id="email">
                <br>
                <input class="input" name="password" type="password" placeholder="Create password" id="password">
                <br>
                <input class="input" name="password2" type="password" placeholder="Confirm password" id="password2">
                <br>
                <button class="button" type="submit">
                    Create Account
                </button>

                <p class="additional">Have an account? <a href="login.php">Login</a></p>
            </form>
        </div> 
    </body>
</html>