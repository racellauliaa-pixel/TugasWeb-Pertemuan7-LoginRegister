<?php
require_once __DIR__ . '/includes/config.php';

// Halaman ini cuma "penunjuk arah": sudah login -> dashboard, belum -> login.
header('Location: ' . (isLoggedIn() ? 'dashboard.php' : 'login.php'));
exit;