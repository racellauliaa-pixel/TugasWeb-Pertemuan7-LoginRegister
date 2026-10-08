<?php
require_once __DIR__ . '/includes/config.php';

logoutUser(); // session_destroy() + hapus cookie remember-me, lihat includes/auth.php

setFlash('success', 'Kamu berhasil logout. Sampai jumpa lagi!');
header('Location: login.php');
exit;