<?php
require_once __DIR__ . '/includes/config.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$oldEmail = '';
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    $oldEmail = $email;

    if ($email === '' || $password === '') {
        $errors[] = 'Email dan kata sandi wajib diisi.';
    } else {
        $user = findUserByEmail($email);

        // password_verify() membandingkan password yang diketik dengan hash di users.json.
        // Pesan error sengaja dibuat sama ("email atau kata sandi salah") baik email tidak
        // ditemukan maupun password salah, supaya orang luar tidak bisa menebak-nebak
        // email mana saja yang terdaftar.
        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Email atau kata sandi salah.';
        } else {
            loginUser($user, $remember);
            setFlash('success', 'Login berhasil. Selamat datang kembali, ' . $user['nama'] . '!');
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="board">
  <div class="paper">
    <div class="pin"></div>

    <h1 class="page-title">Sistem Login</h1>
    <p class="page-subtitle">PHP Native + JSON Storage</p>

    <div class="auth-tabs">
      <a href="login.php" class="auth-tab active">Masuk</a>
      <a href="register.php" class="auth-tab">Daftar</a>
    </div>

    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <ul class="error-list">
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form method="POST" action="login.php" novalidate>
      <label class="field-label">
        Email
        <input type="email" name="email" value="<?= htmlspecialchars($oldEmail, ENT_QUOTES, 'UTF-8') ?>" required>
      </label>

      <label class="field-label">
        Kata Sandi
        <input type="password" name="password" required>
      </label>

      <label class="checkbox-row">
        <input type="checkbox" name="remember">
        Ingat saya selama 30 hari
      </label>

      <button type="submit" class="stamp-btn">Masuk</button>
    </form>

    <p class="switch-text">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
  </div>
</div>

</body>
</html>