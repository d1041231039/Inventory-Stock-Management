<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.html");
    exit();
}

$id_user = $_SESSION['id_user'];

$query = $conn->query("SELECT * FROM user WHERE ID_User = $id_user");
$user = $query->fetch_assoc();

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['update_profile'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        if ($conn->query("UPDATE user SET Username='$username', Email='$email' WHERE ID_User=$id_user")) {
            $_SESSION['username'] = $username;
            $success = "Profile updated successfully!";
        } else {
            $error = "Failed to update profile!";
        }
    }

    if (isset($_POST['update_password'])) {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $passQuery = $conn->query("SELECT Password FROM user WHERE ID_User=$id_user");
    $dbPass = $passQuery->fetch_assoc()['Password'];

    if (!password_verify($old, $dbPass)) {
        $error = "Old password is incorrect.";
    }
    elseif ($new !== $confirm) {
        $error = "New password does not match.";
    }
    elseif (strlen($new) < 8) {
        $error = "New password must be at least 8 characters long.";
    }
    elseif (!preg_match('/[A-Z]/', $new)) {
        $error = "New password must contain at least one uppercase letter.";
    }
    elseif (!preg_match('/[a-z]/', $new)) {
        $error = "New password must contain at least one lowercase letter.";
    }
    else {
        $hashedNew = password_hash($new, PASSWORD_DEFAULT);

        $conn->query("UPDATE user SET Password='$hashedNew' WHERE ID_User=$id_user");
        $success = "Password changed successfully!";
    }
    }

    if (isset($_POST['update_picture'])) {

        $file = $_FILES['picture'];
        $name = $file['name'];
        $tmp = $file['tmp_name'];

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = ['png','jpg','jpeg','gif'];

        if (!in_array($ext, $allowed)) {
            $error = "Invalid file type!";
        } else {
            $newName = "user_" . $id_user . "." . $ext;
            $dest = "picture/" . $newName;

            move_uploaded_file($tmp, $dest);

            $conn->query("UPDATE user SET Picture='$newName' WHERE ID_User=$id_user");
            $success = "Profile photo updated successfully!";
            $user['Picture'] = $newName;
        }
    }
}
?>

<html>

<head>
    <title>SIGMA - Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>

<body>
    <header>
        <?php include 'header.php'; ?>
    </header>

    <nav class="sidebar">
        <ul class="sidebar-up">
            <li class="mark"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#1f1f1f">
                    <path
                        d="M120-520v-320h320v320H120Zm0 400v-320h320v320H120Zm400-400v-320h320v320H520Zm0 400v-320h320v320H520ZM200-600h160v-160H200v160Zm400 0h160v-160H600v160Zm0 400h160v-160H600v160Zm-400 0h160v-160H200v160Zm400-400Zm0 240Zm-240 0Zm0-240Z" />
                </svg><a href="home.php">Dashboard</a></li>
            <li><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#1f1f1f">
                    <path
                        d="M200-640v440h560v-440H640v320l-160-80-160 80v-320H200Zm0 520q-33 0-56.5-23.5T120-200v-499q0-14 4.5-27t13.5-24l50-61q11-14 27.5-21.5T250-840h460q18 0 34.5 7.5T772-811l50 61q9 11 13.5 24t4.5 27v499q0 33-23.5 56.5T760-120H200Zm16-600h528l-34-40H250l-34 40Zm184 80v190l80-40 80 40v-190H400Zm-200 0h560-560Z" />
                </svg><a href="inventory.php">Inventory</a></li>
            <li><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#1f1f1f">
                    <path
                        d="m720-80 120-120-28-28-72 72v-164h-40v164l-72-72-28 28L720-80ZM480-800 243-663l237 137 237-137-237-137ZM120-321v-318q0-22 10.5-40t29.5-29l280-161q10-5 19.5-8t20.5-3q11 0 21 3t19 8l280 161q19 11 29.5 29t10.5 40v159h-80v-116L479-434 200-596v274l240 139v92L160-252q-19-11-29.5-29T120-321ZM720 0q-83 0-141.5-58.5T520-200q0-83 58.5-141.5T720-400q83 0 141.5 58.5T920-200q0 83-58.5 141.5T720 0ZM480-491Z" />
                </svg> <a href="additem.php">Add Item</a></li>
            <li><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#1f1f1f">
                    <path
                        d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h240v80H200v560h560v-240h80v240q0 33-23.5 56.5T760-120H200Zm440-400v-120H520v-80h120v-120h80v120h120v80H720v120h-80Z" />
                </svg><a href="update.php">Update Stock</a></li>
            <li><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#1f1f1f">
                    <path
                        d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z" />
                </svg><a href="profile.php">Profile</a></li>
        </ul>
        <ul class="sidebar-down">
            <li class="logout"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                    width="24px" fill="#1f1f1f">
                    <path
                        d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z" />
                </svg><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="profile-page">
        <h1>Your Profile</h1>

        <?php if ($success): ?>
            <p class="success"><?= $success ?></p>
        <?php elseif ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <div class="profile-container">
            <div class="profile-photo-box">
                <img src="picture/<?= $user['Picture'] ?? 'default.png'; ?>" alt="Profile Photo">
            </div>

            <div class="form-box">
                <h2>Update Picture</h2>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="update_picture">

                    <label>Choose Image</label>
                    <input type="file" name="picture" accept="image/*" required>

                    <button type="submit" class="btn">Upload Photo</button>
                </form>
            </div>

            <div class="form-box">
                <h2>Update Profile</h2>

                <form method="POST">
                    <input type="hidden" name="update_profile">

                    <label>Username</label>
                    <input type="text" name="username" value="<?= $user['Username']; ?>" required>

                    <label>Email</label>
                    <input type="email" name="email" value="<?= $user['Email']; ?>" required>

                    <button type="submit" class="btn">Save Changes</button>
                </form>
            </div>

            <div class="form-box">
                <h2>Change Password</h2>

                <form method="POST">
                    <input type="hidden" name="update_password">

                    <label>Old Password</label>
                    <input type="password" name="old_password" required>

                    <label>New Password</label>
                    <input type="password" name="new_password" required>

                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>

                    <button type="submit" class="btn danger">Update Password</button>
                </form>
            </div>

        </div>
    </main>
</body>
</html>