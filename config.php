<?php
/**
 * config.php — titik masuk tunggal untuk semua halaman.
 * Cukup require file ini di atas tiap halaman; semua helper lain
 * (users.php, flash.php, auth.php) otomatis ikut ter-load.
 */

session_start();

// Lokasi file JSON tempat data user disimpan (di root project, sejajar index.php)
define('DATA_FILE', __DIR__ . '/../users.json');

// Nama cookie untuk fitur "Remember Me"
define('REMEMBER_COOKIE', 'remember_token');

require_once __DIR__ . '/users.php';
require_once __DIR__ . '/flash.php';
require_once __DIR__ . '/auth.php';

// Kalau ada cookie "remember me" yang valid tapi session sudah habis,
// otomatis login-kan lagi user tanpa perlu isi form.
attemptRememberLogin();