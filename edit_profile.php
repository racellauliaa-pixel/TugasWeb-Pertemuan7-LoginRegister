<?php
require_once __DIR__ . '/includes/config.php';

requireLogin();

$user   = findUserById($_SESSION['user_id']);
$errors = [];
$old    = ['nama' => $user['nama'], 'email' => $user['email']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = htmlspecialchars(trim($_POST['nama'] ?? ''), ENT_QUOTES, 'UTF-8');
    $emailRaw = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';

    $old['nama']  = $nama;
    $old['email'] = $emailRaw;

    if ($nama === '') {
        $errors[] = 'Nama tidak boleh kosong.';
    }

    if ($emailRaw === '') {
        $errors[] = 'Email tidak boleh kosong.';
    } elseif (!filter_var($emailRaw, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    } else {
        // Cek duplikasi, tapi kecualikan email milik diri sendiri
        $existing = findUserByEmail($emailRaw);
        if ($existing && $existing['id'] !== $user['id']) {
            $errors[] = 'Email ini sudah dipakai akun lain.';
        }
    }

    // Ganti password itu opsional di form ini
    if ($newPassword !== '' && strlen($newPassword) < 6) {
        $errors[] = 'Kata sandi baru minimal 6 karakter.';
    }

    if (empty($errors)) {
        $email = htmlspecialchars($emailRaw, ENT_QUOTES, 'UTF-8');

        $updateData = ['nama' => $nama, 'email' => $email];
        if ($newPassword !== '') {
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        updateUser($user['id'], $updateData);

        // Session juga harus ikut diupdate, supaya nama/email yang tampil langsung berubah
        $_SESSION['user_nama']  = $nama;
        $_SESSION['user_email'] = $email;

        setFlash('success', 'Profil berhasil diperbarui.');
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profil</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="board">
  <div class="paper">
    <div class="pin"></div>

    <h1 class="page-title">Edit Profil</h1>
    <p class="page-subtitle">Kosongkan kata sandi kalau tidak ingin menggantinya</p>

    <?php if (!empty($errors)): ?>
      <ul class="error-list">
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form method="POST" action="edit_profile.php" novalidate>
      <label class="field-label">
        Nama Lengkap
        <input type="text" name="nama" value="<?= htmlspecialchars($old['nama'], ENT_QUOTES, 'UTF-8') ?>" required>
      </label>

      <label class="field-label">
        Email
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'], ENT_QUOTES, 'UTF-8') ?>" required>
      </label>

      <label class="field-label">
        Kata Sandi Baru (opsional)
        <input type="password" name="new_password" minlength="6" placeholder="Biarkan kosong jika tidak diganti">
      </label>

      <div class="btn-row">
        <button type="submit" class="stamp-btn">Simpan</button>
        <a href="dashboard.php" class="stamp-btn stamp-btn--ink" style="text-align:center;text-decoration:none;">Batal</a>
      </div>
    </form>
  </div>
</div>

</body>
</html>