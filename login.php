<?php
session_start();
include("db_connect.php");

$login_error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $login_error = true;
        }
    } else {
        $login_error = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Giriş Yap</title>
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
        .btn {
            border: none;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1055;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="form-wrapper p-4 mt-5 rounded shadow mx-auto" style="max-width: 400px;">
        <h2 class="text-center text-primary"><i class="fas fa-lock"></i> Giriş Yap</h2>
        <form method="post">
            <div class="mb-3">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Mail adresinizi girin" required>
            </div>
            <div class="mb-3">
                <label for="password"><i class="fas fa-key"></i> Şifre</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Şifrenizi girin" required>
            </div>
            <input type="submit" class="btn btn-primary w-100" value="Giriş Yap">
        </form>
        <div class="text-center mt-3">
            <a href="register.php" class="text-decoration-none"><i class="fas fa-user-plus"></i> Hesabın yok mu? Kayıt ol</a>
        </div>
    </div>
</div>

<?php if ($login_error): ?>
<div class="toast-container">
    <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Hatalı şifre! Lütfen tekrar deneyin.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
