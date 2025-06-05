<?php
require_once "../includes/db.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        header("Location: /files/" . $user['role'] . "/dashboard.php"); // Sửa lại dòng này
        exit;
    } else {
        $error = "Sai email hoặc mật khẩu!";
    }
}
?>
<h3>Đăng nhập</h3>
<?php if(isset($_GET['reg'])): ?><div class="alert alert-success">Đăng ký thành công! Đăng nhập ngay.</div><?php endif; ?>
<?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<form method="post">
    <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
    </div>
    <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
    </div>
    <button type="submit" class="btn btn-primary">Đăng nhập</button>
</form>
<p>Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
<?php require_once "../includes/footer.php"; ?>