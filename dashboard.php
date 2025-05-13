<?php
session_start();
include("db_connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $site = $_POST['site'];
    $password = base64_encode($_POST['password']);

    // Aynı şifre ve site daha önce eklenmiş mi kontrol et
    $check = $conn->prepare("SELECT * FROM passwords WHERE user_id = ? AND site = ? AND password = ?");
    $check->bind_param("iss", $user_id, $site, $password);
    $check->execute();
    $exists = $check->get_result();

    if ($exists->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO passwords (site, password, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $site, $password, $user_id);
        $stmt->execute();
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM passwords WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $site = $_POST['site'];
    $password = base64_encode($_POST['password']);
    $stmt = $conn->prepare("UPDATE passwords SET site = ?, password = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $site, $password, $id, $user_id);
    $stmt->execute();
}

$stmt = $conn->prepare("SELECT * FROM passwords WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
    function togglePassword(btn, inputId) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            btn.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            input.type = "password";
            btn.innerHTML = '<i class="fas fa-eye"></i>';
        }
    }
    </script>
    <style>
        body { background-color: #1f1f1f; color: #f8f9fa; }
        .form-control { background-color: #2c2c2c; color: #fff; border: 1px solid #555; }
        .form-control::placeholder { color: #aaa; }
        .btn-dark { background-color: #444; border: none; }
        .btn-dark:hover { background-color: #555; }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="card p-4 shadow-lg bg-dark text-light">
        <h2 class="text-center text-primary mb-4">Merhaba, <?php echo $_SESSION['username']; ?>!</h2>
        <h4>Şifrelerini Listele</h4>
        <div class="row">
        <?php $index = 0; while ($row = $result->fetch_assoc()): $index++; ?>
            <div class="col-md-6 mb-3">
                <div class="card bg-secondary text-white p-3">
                    <form method="post">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <div class="mb-2">
                            <label>Site:</label>
                            <input type="text" class="form-control" name="site" value="<?php echo htmlspecialchars($row['site']); ?>" required>
                        </div>
                        <div class="mb-2">
                            <label>Şifre:</label>
                            <input type="password" class="form-control" id="passwordField<?php echo $index; ?>" name="password" value="<?php echo htmlspecialchars(base64_decode($row['password'])); ?>" required>
                            <button type="button" class="btn btn-sm btn-secondary mt-1" onclick="togglePassword(this, 'passwordField<?php echo $index; ?>')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-warning btn-sm" name="edit"><i class="fas fa-edit"></i> Güncelle</button>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğinize emin misiniz?')"><i class="fas fa-trash-alt"></i> Sil</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
        </div>

        <h4 class="mt-4">Yeni Şifre Ekle</h4>
        <form method="post" class="row g-3">
            <input type="hidden" name="add" value="1">
            <div class="col-md-6">
                <input type="text" class="form-control" name="site" placeholder="Site adı" required>
            </div>
            <div class="col-md-6">
                <input type="password" class="form-control" name="password" placeholder="Şifre" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-dark w-100"><i class="fas fa-plus-circle"></i> Ekle</button>
            </div>
        </form>

        <div class="text-center mt-4">
            <a href="logout.php" class="btn btn-outline-light btn-sm"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
        </div>
    </div>
</div>
</body>
</html>
