<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$error = '';
$success = '';

$sql = "SELECT * FROM tbl_user WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$sql_foto = "SELECT foto FROM tbl_foto WHERE idUser = ? LIMIT 1";
$stmt_foto = $conn->prepare($sql_foto);
$stmt_foto->bind_param("i", $user['idUser']);
$stmt_foto->execute();
$result_foto = $stmt_foto->get_result();
$foto = $result_foto->fetch_assoc();
$stmt_foto->close();

if (isset($_POST['upload'])) {
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $new_filename = "profile_" . $user['idUser'] . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $new_filename;
    
        $check = getimagesize($_FILES['foto']['tmp_name']);
        if ($check !== false) {
            if (!empty($foto['foto']) && file_exists($foto['foto'])) {
                unlink($foto['foto']);
            }
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                if ($result_foto->num_rows > 0) {
                    $sql_update = "UPDATE tbl_foto SET foto = ? WHERE idUser = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("si", $target_file, $user['idUser']);
                    $stmt_update->execute();
                    $stmt_update->close();
                } else {
                    $sql_insert = "INSERT INTO tbl_foto (idUser, foto) VALUES (?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param("is", $user['idUser'], $target_file);
                    $stmt_insert->execute();
                    $stmt_insert->close();
                }
                $success = "Foto profil berhasil diunggah!";
                header("Location: editprofile.php");
                exit();
            } else {
                $error = "Maaf, terjadi kesalahan saat mengunggah file.";
            }
        } else {
            $error = "File yang diunggah bukan gambar.";
        }
    } else {
        $error = "Silakan pilih file gambar untuk diunggah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .profile-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
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
                        <a class="nav-link active" href="editprofile.php">Edit Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="profile-container">
            <h2 class="text-center mb-4">Edit Profil</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success; ?></div>
            <?php endif; ?>
            
            <div class="text-center">
                <?php if (!empty($foto['foto'])): ?>
                    <img src="<?= $foto['foto']; ?>" alt="Foto Profil" class="profile-img">
                <?php else: ?>
                    <img src="https://via.placeholder.com/150" alt="Foto Profil" class="profile-img">
                <?php endif; ?>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="foto" class="form-label">Unggah Foto Profil</label>
                    <input class="form-control" type="file" id="foto" name="foto" accept="image/*">
                </div>
                <button type="submit" name="upload" class="btn btn-primary w-100">Unggah Foto</button>
            </form>
            <hr>
            <div class="user-info">
                <h4>Informasi Akun</h4>
                <p><strong>Username:</strong> <?= htmlspecialchars($user['username']); ?></p>
                <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($user['namalengkap']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
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