<?php
session_start();
include("db_connect.php");

function is_strong_password($password) {
    return strlen($password) >= 8 &&
           preg_match('/[A-Z]/', $password) &&
           preg_match('/[a-z]/', $password) &&
           preg_match('/[0-9]/', $password) &&
           preg_match('/[^a-zA-Z0-9]/', $password);
}

function is_common_password($password) {
    $common = ['123456', 'password', 'qwerty', '123456789', '111111', '12345678'];
    return in_array($password, $common);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (is_common_password($password)) {
        $error = "Çok yaygın bir şifre kullanıyorsunuz!";
    } elseif (!is_strong_password($password)) {
        $error = "Şifre yeterince güçlü değil!";
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        $stmt->execute();

        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kayıt Ol</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap + FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #1f1f1f;
            color: #f8f9fa;
        }
        .form-wrapper {
            background-color: #2c2c2c;
            color: #f8f9fa;
        }
        .form-control {
            background-color: #3a3a3a;
            color: #ffffff;
            border: 1px solid #555;
        }
        .form-control::placeholder {
            color: #ccc;
        }
        .btn-success {
            background-color: #444 !important;
            border-color: #444 !important;
        }
        .btn-success:hover {
            background-color: #555 !important;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="form-wrapper p-4 mt-5 rounded shadow mx-auto" style="max-width: 400px;">
        <h2 class="text-center text-primary"><i class="fas fa-user-plus"></i> Kayıt Ol</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label><i class="fas fa-user"></i> Kullanıcı Adı</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label><i class="fas fa-key"></i> Şifre</label>
                <input type="password" class="form-control" name="password" id="password" required>
                
            </div>
            <button type="submit" class="btn btn-success w-100"><i class="fas fa-user-plus"></i> Kayıt Ol</button>
        </form>
        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none"><i class="fas fa-sign-in-alt"></i> Zaten hesabın var mı? Giriş yap</a>
        </div>
    </div>
</div>

</body>
</html>