<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE Email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['Password'])) {
            session_start();
            $_SESSION['id_user'] = $user['ID_User'];
            $_SESSION['username'] = $user['Username'];
            header("Location: home.php");
            exit();
        } else {
            echo "<script>alert('Email or password wrong!'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Email or password wrong!'); window.history.back();</script>";
        exit();
    }
}

$conn->close();
?>

<html>
    <head>
        <title>SIGMA</title>
        <link rel="stylesheet" href="login.css">
    </head>
    <body>
        <div class="container">
            <p class="title">Login</p>
            <form method="post" action="login.php">
                <input class="input" name="email" type="text" placeholder="Enter your email" id="email" required>
                <br>
                <input class="input" name="password" type="password" placeholder="Enter your password" id="password" required>
                <br>
                <button class="button" type="submit">
                    Login
                </button>
            </form>
            <p class="additional">Don't have an account? <a href="register.php">Sign up</a></p>
        </div>
    </body>
</html>