<?php
include 'koneksi.php';

$id_user = $_SESSION['id_user'] ?? null;

$photo = "picture/default.png";

if ($id_user) {
    $q = $conn->query("SELECT Picture FROM user WHERE ID_User=$id_user");
    $data = $q->fetch_assoc();

    if (!empty($data['Picture'])) {
        $photo = "picture/" . $data['Picture'];
    }
}
?>

<html>
<head>
    <link rel="stylesheet" href="header.css">
</head>

<body class="header">
    <div class="header-div">
        <img class="logo" src="sigma.png">
        <div class="info">
            <img class="profile" src="<?= $photo ?>">
        </div>
    </div>
</body>
</html>
