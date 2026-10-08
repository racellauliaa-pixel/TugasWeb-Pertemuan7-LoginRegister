<?php
/**
 * auth.php — semua yang berhubungan dengan status login (session) dan
 * fitur "Remember Me" lewat cookie.
 */

/**
 * Cek apakah user sedang login.
 */
function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

/**
 * Panggil fungsi ini di baris paling atas halaman yang WAJIB login
 * (contoh: dashboard.php). Kalau belum login, otomatis dilempar ke login.php.
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        header('Location: login.php');
        exit;
    }
}

/**
 * Jalankan proses login: simpan data user ke session,
 * dan kalau "remember me" dicentang, buat juga cookie jangka panjang.
 */
function loginUser(array $user, bool $remember = false): void
{
    // Regenerasi session id setiap login = mencegah "session fixation attack"
    session_regenerate_id(true);

    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_nama']  = $user['nama'];
    $_SESSION['user_email'] = $user['email'];

    if ($remember) {
        $plainToken   = bin2hex(random_bytes(32));      // token acak yang dikirim ke browser
        $hashedToken  = password_hash($plainToken, PASSWORD_DEFAULT); // versi hash disimpan di server

        updateUser($user['id'], ['remember_token' => $hashedToken]);

        // Cookie isinya "id_user:token_asli", berlaku 30 hari.
        // httponly=true supaya cookie tidak bisa dibaca lewat JavaScript (lebih aman dari XSS).
        setcookie(
            REMEMBER_COOKIE,
            $user['id'] . ':' . $plainToken,
            time() + (30 * 24 * 60 * 60),
            '/',
            '',
            false,
            true
        );
    }
}

/**
 * Jalankan proses logout: hapus session DAN cookie remember-me (kalau ada).
 */
function logoutUser(): void
{
    $userId = $_SESSION['user_id'] ?? null;
    if ($userId) {
        updateUser($userId, ['remember_token' => null]); // cabut token di server juga
    }

    $_SESSION = [];
    session_destroy();

    if (isset($_COOKIE[REMEMBER_COOKIE])) {
        setcookie(REMEMBER_COOKIE, '', time() - 3600, '/'); // kadaluwarsakan cookie di browser
    }
}

/**
 * Dipanggil otomatis dari config.php di setiap halaman.
 * Kalau user belum login TAPI punya cookie remember-me yang valid,
 * login-kan dia ulang secara diam-diam.
 */
function attemptRememberLogin(): void
{
    if (isLoggedIn() || empty($_COOKIE[REMEMBER_COOKIE])) {
        return;
    }

    $parts = explode(':', $_COOKIE[REMEMBER_COOKIE], 2);
    if (count($parts) !== 2) {
        return;
    }

    [$userId, $plainToken] = $parts;
    $user = findUserById($userId);

    if ($user && !empty($user['remember_token']) && password_verify($plainToken, $user['remember_token'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_nama']  = $user['nama'];
        $_SESSION['user_email'] = $user['email'];
    }
}