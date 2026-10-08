<?php
/**
 * users.php — semua fungsi yang berurusan langsung dengan users.json.
 * File ini berperan sebagai pengganti "database" sederhana.
 */

/**
 * Baca semua user dari users.json.
 * Kalau file belum ada, otomatis dibuat kosong dulu.
 */
function getUsers(): array
{
    if (!file_exists(DATA_FILE)) {
        file_put_contents(DATA_FILE, json_encode([], JSON_PRETTY_PRINT));
    }

    $json = file_get_contents(DATA_FILE);
    $data = json_decode($json, true);

    return is_array($data) ? $data : [];
}

/**
 * Tulis ulang seluruh isi users.json.
 * LOCK_EX mencegah data rusak kalau ada 2 request nulis bersamaan.
 */
function saveUsers(array $users): bool
{
    $json = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents(DATA_FILE, $json, LOCK_EX) !== false;
}

/**
 * Cari 1 user berdasarkan email (dipakai untuk login & cek duplikasi saat registrasi).
 */
function findUserByEmail(string $email): ?array
{
    foreach (getUsers() as $user) {
        if (strcasecmp($user['email'], $email) === 0) {
            return $user;
        }
    }
    return null;
}

/**
 * Cari 1 user berdasarkan id (dipakai setelah login, lewat session).
 */
function findUserById(string $id): ?array
{
    foreach (getUsers() as $user) {
        if ($user['id'] === $id) {
            return $user;
        }
    }
    return null;
}

/**
 * Tambah user baru ke users.json. Password yang masuk ke sini
 * HARUS sudah di-hash (lihat password_hash() di register.php).
 */
function addUser(string $nama, string $email, string $hashedPassword): array
{
    $users = getUsers();

    $newUser = [
        'id'              => uniqid('user_', true),
        'nama'            => $nama,
        'email'           => $email,
        'password'        => $hashedPassword,
        'remember_token'  => null, // dipakai fitur "Remember Me"
        'created_at'      => date('Y-m-d H:i:s'),
    ];

    $users[] = $newUser;
    saveUsers($users);

    return $newUser;
}

/**
 * Update sebagian data user (dipakai untuk edit profil & simpan/hapus remember_token).
 */
function updateUser(string $id, array $newData): bool
{
    $users = getUsers();

    foreach ($users as &$user) {
        if ($user['id'] === $id) {
            $user = array_merge($user, $newData);
            return saveUsers($users);
        }
    }
    unset($user);

    return false;
}