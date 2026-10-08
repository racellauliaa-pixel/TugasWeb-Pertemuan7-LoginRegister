<?php
require_once __DIR__ . '/includes/config.php';

// Kalau sudah login, tidak perlu lihat halaman register lagi
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$old = ['nama' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ===== 1. SANITASI INPUT (htmlspecialchars) =====
    // Dilakukan di awal, sebelum data dipakai/disimpan, supaya karakter
    // seperti <script> tidak bisa ikut tersimpan atau dieksekusi di browser.
    $nama      = htmlspecialchars(trim($_POST['nama'] ?? ''), ENT_QUOTES, 'UTF-8');
    $emailRaw  = trim($_POST['email'] ?? ''); // divalidasi dulu sebelum di-escape
    $password  = $_POST['password'] ?? '';          // password TIDAK di-htmlspecialchars,
    $passwordConfirm = $_POST['password_confirm'] ?? ''; // nanti cuma di-hash, bukan ditampilkan

    $old['nama']  = $nama;
    $old['email'] = $emailRaw;

    // ===== 2. VALIDASI =====
    if ($nama === '') {
        $errors[] = 'Nama tidak boleh kosong.';
    }

    if ($emailRaw === '') {
        $errors[] = 'Email tidak boleh kosong.';
    } elseif (!filter_var($emailRaw, FILTER_VALIDATE_EMAIL)) {
        // Validasi format email pakai filter_var()
        $errors[] = 'Format email tidak valid.';
    } elseif (findUserByEmail($emailRaw)) {
        // ===== 3. CEK DUPLIKASI EMAIL =====
        $errors[] = 'Email ini sudah terdaftar. Silakan login.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Kata sandi minimal 6 karakter.';
    } elseif ($password !== $passwordConfirm) {
        $errors[] = 'Konfirmasi kata sandi tidak cocok.';
    }

    // ===== 4. SIMPAN KALAU LOLOS SEMUA VALIDASI =====
    if (empty($errors)) {
        $email = htmlspecialchars($emailRaw, ENT_QUOTES, 'UTF-8');

        // Password di-HASH, bukan disimpan apa adanya
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        addUser($nama, $email, $hashedPassword);

        setFlash('success', 'Registrasi berhasil! Silakan login dengan akun barumu.');
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Akun</title>
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
      <a href="login.php" class="auth-tab">Masuk</a>
      <a href="register.php" class="auth-tab active">Daftar</a>
    </div>

    <?php if (!empty($errors)): ?>
      <ul class="error-list">
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form method="POST" action="register.php" novalidate>
      <label class="field-label">
        Nama Lengkap
        <input type="text" name="nama" value="<?= htmlspecialchars($old['nama'], ENT_QUOTES, 'UTF-8') ?>" required>
      </label>

      <label class="field-label">
        Email
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'], ENT_QUOTES, 'UTF-8') ?>" required>
      </label>

      <label class="field-label">
        Kata Sandi
        <input type="password" name="password" minlength="6" required>
      </label>

      <label class="field-label">
        Konfirmasi Kata Sandi
        <input type="password" name="password_confirm" minlength="6" required>
      </label>

      <button type="submit" class="stamp-btn">Daftar</button>
    </form>

    <p class="switch-text">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
  </div>
</div>

</body>
</html>