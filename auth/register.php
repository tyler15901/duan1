<?php
require_once "../includes/db.php";
require_once "../includes/functions.php";
require_once "../includes/header.php";
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $error = "Email đã tồn tại!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (email, password, name, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$email, $password, $name, $role]);
        header("Location: login.php?reg=1");
        exit;
    }
}
?>
<h3>Đăng ký tài khoản</h3>
<?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<form method="post">
    <div class="mb-3">
        <input type="text" name="name" class="form-control" placeholder="Họ và tên" required>
    </div>
    <div class="mb-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
    </div>
    <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
    </div>
    <div class="mb-3">
        <select name="role" class="form-control" required>
            <option value="candidate">Ứng viên</option>
            <option value="recruiter">Nhà tuyển dụng</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Đăng ký</button>
</form>
<p>Bạn đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
<?php require_once "../includes/footer.php"; ?>