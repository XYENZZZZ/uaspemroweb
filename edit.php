<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$idMhs = $_GET['id'];
$error = '';
$success = '';
$sql = "SELECT * FROM tbl_mahasiswa WHERE idMhs = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idMhs);
$stmt->execute();
$result = $stmt->get_result();
$mhs = $result->fetch_assoc();
$stmt->close();
if (!$mhs) {
    header("Location: index.php");
    exit();
}
if (isset($_POST['update'])) {
    $npm = $_POST['npm'];
    $nama = $_POST['nama'];
    $prodi = $_POST['prodi'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];

    $sql = "UPDATE tbl_mahasiswa SET npm = ?, nama = ?, prodi = ?, email = ?, alamat = ? WHERE idMhs = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $npm, $nama, $prodi, $email, $alamat, $idMhs);
    
    if ($stmt->execute()) {
        $_SESSION['pesan'] = "Data mahasiswa berhasil diperbarui!";
        header("Location: index.php");
        exit();
    } else {
        $error = "Gagal memperbarui data: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
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
            max-width: 800px;
            margin: 0 auto;
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
                        <a class="nav-link" href="index.php">Dashboard</a>
                    </li>
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
        <div class="form-container">
            <h2>Edit Data Mahasiswa</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="npm" class="form-label">NPM</label>
                        <input type="text" class="form-control" id="npm" name="npm" required 
                            value="<?= htmlspecialchars($mhs['npm']); ?>" maxlength="8">
                    </div>
                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" required 
                            value="<?= htmlspecialchars($mhs['nama']); ?>" maxlength="60">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="prodi" class="form-label">Program Studi</label>
                        <input type="text" class="form-control" id="prodi" name="prodi" required 
                            value="<?= htmlspecialchars($mhs['prodi']); ?>" maxlength="25">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                            value="<?= htmlspecialchars($mhs['email']); ?>" maxlength="30">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= htmlspecialchars($mhs['alamat']); ?></textarea>
                </div>
                <button type="submit" name="update" class="btn btn-primary">Update Data</button>
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>