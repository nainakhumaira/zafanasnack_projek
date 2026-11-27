<?php
require_once __DIR__ . '/config/koneksi.php';

unset($_SESSION['user']);
set_flash('info', 'Anda telah berhasil keluar akun.');
header("Location: " . BASE_URL);
exit;
