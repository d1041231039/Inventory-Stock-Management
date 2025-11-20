<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_stock'], $_POST['perubahan'], $_POST['aksi'])) {
    $id_stock = intval($_POST['id_stock']);
    $perubahan = intval($_POST['perubahan']);
    $aksi = $_POST['aksi'] === 'tambah' ? 'tambah' : 'kurang';

    if ($perubahan <= 0) {
        $error = "Quantity must be greater than 0.";
    } else {
        $stmt = $conn->prepare("SELECT Jumlah_Barang FROM stock WHERE ID_Stock = ? AND ID_User = ?");
        $stmt->bind_param("ii", $id_stock, $id_user);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $row = $res->fetch_assoc();
            $stok_sekarang = intval($row['Jumlah_Barang']);

            if ($aksi === 'tambah') {
                $stok_baru = $stok_sekarang + $perubahan;
            } else {
                $stok_baru = $stok_sekarang - $perubahan;
                if ($stok_baru < 0)
                    $stok_baru = 0;
            }

            $upd = $conn->prepare("UPDATE stock SET Jumlah_Barang = ?, Tanggal_Pembaruan = NOW() WHERE ID_Stock = ? AND ID_User = ?");
            $upd->bind_param("iii", $stok_baru, $id_stock, $id_user);
            if ($upd->execute()) {
                $success = "Stock updated successfully.";
            } else {
                $error = "Failed to update stock. Please try again.";
            }
            $upd->close();
        } else {
            $error = "Item not found or you don't have permission.";
        }
        $stmt->close();
    }
}

$kategori_query = $conn->prepare("SELECT DISTINCT Kategori FROM stock WHERE ID_User = ? ORDER BY Kategori ASC");
$kategori_query->bind_param("i", $id_user);
$kategori_query->execute();
$kategori_result = $kategori_query->get_result();

$sql = "SELECT ID_Stock, Nama_Barang, Kategori, Jumlah_Barang, Satuan, Tanggal_Pembaruan 
        FROM stock 
        WHERE ID_User = $id_user
        ORDER BY Nama_Barang ASC";

$result = $conn->query($sql);
$conn->close();
?>

<html>

<head>
    <title>SIGMA - Update Stock</title>
    <link rel="stylesheet" href="update.css">
</head>

<body>
    <header>
        <?php include 'header.php'; ?>
    </header>

    <nav class="sidebar">
        <ul class="sidebar-up">
            <li><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#1f1f1f">
                    <path
                        d="M120-520v-320h320v320H120Zm0 400v-320h320v320H120Zm400-400v-320h320v320H520Zm0 400v-320h320v320H520ZM200-600h160v-160H200v160Zm400 0h160v-160H600v160Zm0 400h160v-160H600v160Zm-400 0h160v-160H200v160Zm400-400Zm0 240Zm-240 0Zm0-240Z" />
                </svg><a href="index.php">Dashboard</a></li>
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
            <li class="mark"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
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

    <main class="update-stock">
        <h2>Update Your Stock</h2>

        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <form class="filter-bar">
            <input type="text" name="search" placeholder="Search item...">

            <select name="filter" onsubmit="return false;">
                <option value="">All Categories</option>
                <?php
                if ($kategori_result->num_rows > 0) {
                    while ($row = $kategori_result->fetch_assoc()) {
                        echo "<option value='{$row['Kategori']}'>{$row['Kategori']}</option>";
                    }
                }
                ?>
            </select>
        </form>
        
        <div class="table-wrap">
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($r = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['Nama_Barang']) ?></td>
                                <td><?= htmlspecialchars($r['Kategori']) ?></td>
                                <td id="qty-<?= $r['ID_Stock'] ?>"><?= intval($r['Jumlah_Barang']) ?></td>
                                <td><?= htmlspecialchars($r['Satuan']) ?></td>
                                <td>
                                    <form method="POST" class="update-form" onsubmit="return confirmUpdate(this);">
                                        <input type="hidden" name="id_stock" value="<?= intval($r['ID_Stock']) ?>">
                                        <input type="number" name="perubahan" min="1" required placeholder="Qty"
                                            style="width:80px;">
                                        <select name="aksi" required>
                                            <option value="tambah">+ Add</option>
                                            <option value="kurang">− Subtract</option>
                                        </select>
                                        <button type="submit">Apply</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty">No stock available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="update.js" defer></script>
</body>

</html>