<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['tambah'])) {
    $npm = $_POST['npm'];
    $nama = $_POST['nama'];
    $prodi = $_POST['prodi'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];

    // Insert ke tbl_mahasiswa
    $sql = "INSERT INTO tbl_mahasiswa (npm, nama, prodi, email, alamat) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $npm, $nama, $prodi, $email, $alamat);
    $stmt->execute();
    $idMhs = $stmt->insert_id;
    $stmt->close();


    $username = $npm;
    $password = password_hash($npm, PASSWORD_DEFAULT);

    // Insert ke tbl_user
    $sql_user = "INSERT INTO tbl_user (namalengkap, email, username, password, idMhs) VALUES (?, ?, ?, ?, ?)";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("ssssi", $nama, $email, $username, $password, $idMhs);
    $stmt_user->execute();
    $stmt_user->close();

    $_SESSION['pesan'] = "Data mahasiswa berhasil ditambahkan!";
    header("Location: index.php");
    exit();
}

if (isset($_GET['hapus'])) {
    $idMhs = $_GET['hapus'];
    $sql = "DELETE FROM tbl_mahasiswa WHERE idMhs = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idMhs);
    $stmt->execute();
    $stmt->close();
    $_SESSION['pesan'] = "Data mahasiswa berhasil dihapus!";
    header("Location: index.php");
    exit();
}
$sql = "SELECT * FROM tbl_mahasiswa";
$result = $conn->query($sql);
$mahasiswa = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $mahasiswa[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
        padding: 20px;
        background-color: #f8f9fa;
        }
        .form-container {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        }
        .table-container {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
        font-weight: bold;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Tambah Data Mahasiswa</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="editprofile.php">Edit Profil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
            </ul>
    </div>
    </div>
</nav>

<div class="container">
    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['pesan']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php unset($_SESSION['pesan']); ?>
<?php endif; ?>
<div class="form-container">
    <h2>Tambah Data Mahasiswa</h2>
        <form method="POST">
<div class="row mb-3">
<div class="col-md-6">
    <label for="npm" class="form-label">NPM</label>
        <input type="text" class="form-control" id="npm" name="npm" required maxlength="8">
</div>
    <div class="col-md-6">
    <label for="nama" class="form-label">Nama</label>
        <input type="text" class="form-control" id="nama" name="nama" required maxlength="60">
    </div>
</div>
    <div class="row mb-3">
    <div class="col-md-6">
    <label for="prodi" class="form-label">Program Studi</label>
        <input type="text" class="form-control" id="prodi" name="prodi" required maxlength="25">
    </div>
    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" maxlength="30">
        </div>
    </div>
    <div class="mb-3">
        <label for="alamat" class="form-label">Alamat</label>
        <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
    </div>
    <button type="submit" name="tambah" class="btn btn-primary">Tambah Data</button>
    </form>
</div>
    <div class="table-container">
        <h2>Daftar Mahasiswa</h2>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>NPM</th>
            <th>Nama</th>
            <th>Prodi</th>
            <th>Email</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>
    </thead>
        <tbody>
    <?php foreach ($mahasiswa as $mhs): ?>
        <tr>
            <td><?= htmlspecialchars($mhs['npm']); ?></td>
            <td><?= htmlspecialchars($mhs['nama']); ?></td>
            <td><?= htmlspecialchars($mhs['prodi']); ?></td>
            <td><?= htmlspecialchars($mhs['email']); ?></td>
            <td><?= htmlspecialchars($mhs['alamat']); ?></td>
            <td>
                <a href="edit.php?id=<?= $mhs['idMhs']; ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="index.php?hapus=<?= $mhs['idMhs']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
            </td>
        </tr>
<?php endforeach; ?>
        </tbody>
    </table>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>