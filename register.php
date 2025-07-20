<?php
require_once 'config.php';
session_start();
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
$error = '';
if (isset($_POST['register'])) {
    $namalengkap = $_POST['namalengkap'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if (empty($namalengkap) || empty($email) || empty($username) || empty($password)) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Password tidak sama!";
    } elseif (strlen($username) > 15) {
        $error = "Username maksimal 15 karakter!";
    } else {
        // Cek apakah username sudah ada
        $sql_check = "SELECT username FROM tbl_user WHERE username = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO tbl_user (namalengkap, email, username, password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $namalengkap, $email, $username, $hashed_password);
            if ($stmt->execute()) {
                $_SESSION['register_success'] = "Pendaftaran berhasil! Silakan login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Gagal mendaftar: " . $stmt->error;
            }
            $stmt->close();
        }
        $stmt_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 15px;
        }
        .register-box {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 320px;
            padding: 25px;
        }
        .register-title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.3rem;
            font-weight: 500;
        }
        .form-control {
            margin-bottom: 15px;
            height: 40px;
            font-size: 0.9rem;
        }
        .btn-register {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
            font-size: 0.9rem;
        }
        .alert {
            font-size: 0.85rem;
            padding: 8px 12px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="register-box">
        <h3 class="register-title">Daftar Akun</h3>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-2">
                <input type="text" class="form-control" id="namalengkap" name="namalengkap" required placeholder="Nama Lengkap">
            </div>
            <div class="mb-2">
                <input type="email" class="form-control" id="email" name="email" required placeholder="Email">
            </div>
            <div class="mb-2">
                <input type="text" class="form-control" id="username" name="username" required placeholder="Username" maxlength="15">
            </div>
            <div class="mb-2">
                <input type="password" class="form-control" id="password" name="password" required placeholder="Password">
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Konfirmasi Password">
            </div>
            <button type="submit" name="register" class="btn btn-primary btn-register">Daftar</button>
        </form>
        <div class="login-link">
            <p class="text-muted">Sudah punya akun? <a href="login.php">Login disini</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>