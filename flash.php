<?php
/**
 * flash.php — pesan sekali-tampil (flash message).
 *
 * Pola yang dipakai: Post → Redirect → Get.
 * Setelah form diproses, kita redirect ke halaman lain (bukan langsung
 * nge-print HTML di halaman yang sama). Supaya pesan "Berhasil!" tetap
 * bisa ditampilkan walau sudah pindah halaman, pesannya dititip sebentar
 * di session, lalu diambil & dihapus begitu halaman tujuan dibuka.
 */

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']); // dihapus supaya tidak muncul lagi kalau halaman di-refresh
        return $flash;
    }
    return null;
}