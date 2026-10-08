<?php
require_once __DIR__ . '/includes/config.php';

// Baris ini yang "memproteksi" halaman ini: kalau belum login, langsung
// dilempar ke login.php (lihat requireLogin() di includes/auth.php).
requireLogin();

$flash = getFlash();
$user  = findUserById($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="board">
  <div class="paper">
    <div class="pin"></div>

    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Halaman ini hanya bisa dilihat setelah login</p>

    <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <span class="stamp-badge">✓ Terverifikasi</span>

    <div class="id-card">
      <div class="id-card__row">
        <span class="id-card__label">Nama</span>
        <span class="id-card__value"><?= $user['nama'] /* sudah di-escape saat disimpan */ ?></span>
      </div>
      <div class="id-card__row">
        <span class="id-card__label">Email</span>
        <span class="id-card__value"><?= $user['email'] ?></span>
      </div>
      <div class="id-card__row">
        <span class="id-card__label">Terdaftar sejak</span>
        <span class="id-card__value"><?= $user['created_at'] ?></span>
      </div>
    </div>

    <div class="btn-row">
      <a href="edit_profile.php" class="stamp-btn stamp-btn--ink" style="text-align:center;text-decoration:none;">Edit Profil</a>
      <a href="logout.php" class="stamp-btn" style="text-align:center;text-decoration:none;">Logout</a>
    </div>
  </div>
</div>

</body>
</html>