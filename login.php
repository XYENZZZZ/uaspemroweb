<?php
require_once 'config.php';
session_start();

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['register_success'])) {
    $success = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}
$error = '';
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM tbl_user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['idUser'] = $user['idUser'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        .login-box {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 320px;
            padding: 25px;
        }
        .login-title {
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
        .btn-login {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        .register-link {
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
    <div class="login-box">
        <h3 class="login-title">Login</h3>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-2">
                <input type="text" class="form-control" name="username" required placeholder="Username">
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" required placeholder="Password">
            </div>
            <button type="submit" name="login" class="btn btn-primary btn-login">Login</button>
        </form>
        
        <div class="register-link">
            <p class="text-muted">Belum punya akun? <a href="register.php">Daftar disini</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>