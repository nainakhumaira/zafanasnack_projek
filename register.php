<?php
require_once __DIR__ . '/config/koneksi.php';

if (is_logged_in()) {
    header("Location: " . BASE_URL);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat_lengkap = trim($_POST['alamat_lengkap'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    if (empty($nama_lengkap) || empty($email) || empty($no_hp) || empty($password)) {
        $error = 'Semua kolom bertanda bintang (*) wajib diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format alamat email tidak valid!';
    } elseif (strlen($password) < 6) {
        $error = 'Kata sandi minimal harus 6 karakter!';
    } elseif ($password !== $konfirmasi_password) {
        $error = 'Konfirmasi kata sandi tidak cocok!';
    } else {
        // Cek apakah email sudah terdaftar
        $stmt = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $check = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($check) > 0) {
            $error = 'Email ini sudah terdaftar! Silakan gunakan email lain atau masuk.';
        } else {
            // Hash password
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $role = 'pelanggan';

            $insert_stmt = mysqli_prepare($koneksi, "INSERT INTO users (nama_lengkap, email, password, no_hp, alamat_lengkap, role) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($insert_stmt, "ssssss", $nama_lengkap, $email, $hashed, $no_hp, $alamat_lengkap, $role);

            if (mysqli_stmt_execute($insert_stmt)) {
                $new_id = mysqli_insert_id($koneksi);

                // Auto login
                $_SESSION['user'] = [
                    'id_user' => $new_id,
                    'nama_lengkap' => $nama_lengkap,
                    'email' => $email,
                    'no_hp' => $no_hp,
                    'alamat_lengkap' => $alamat_lengkap,
                    'role' => $role
                ];

                set_flash('success', 'Pendaftaran akun berhasil! Selamat datang di ZAFANA SNACK.');
                header("Location: " . BASE_URL);
                exit;
            } else {
                $error = 'Terjadi kesalahan sistem saat mendaftar. Silakan coba lagi.';
            }
        }
    }
}

$page_title = "Daftar Akun Pelanggan";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container" style="padding: 50px 20px 80px;">
  <div style="max-width: 520px; margin: 0 auto; background: var(--white); border-radius: 16px; border: 1px solid var(--border-color); padding: 35px 30px; box-shadow: var(--shadow-md);">
    
    <div style="text-align: center; margin-bottom: 24px;">
      <h2 style="font-size: 24px; color: var(--navy); font-weight: 800;">Daftar Akun Baru</h2>
      <p style="color: var(--navy-light); font-size: 14px; margin-top: 4px;">Bergabung untuk kemudahan belanja & melacak pesanan snack</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>register.php" method="POST">
      <div class="form-group">
        <label class="form-label">Nama Lengkap *</label>
        <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: Budi Santoso" value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label">Alamat Email *</label>
        <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label">Nomor WhatsApp Aktif *</label>
        <input type="text" name="no_hp" class="form-control" placeholder="0896246614578" value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label">Alamat Lengkap Pengiriman</label>
        <textarea name="alamat_lengkap" class="form-control" placeholder="Nama Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan, Kota, Kode Pos"><?= htmlspecialchars($_POST['alamat_lengkap'] ?? '') ?></textarea>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div class="form-group">
          <label class="form-label">Kata Sandi *</label>
          <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
        </div>

        <div class="form-group">
          <label class="form-label">Ulangi Sandi *</label>
          <input type="password" name="konfirmasi_password" class="form-control" placeholder="Ketik ulang sandi" required>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 10px;">
        <i class="fa-solid fa-user-plus"></i> Daftar Sekarang
      </button>
    </form>

    <div style="text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color); font-size: 14px; color: var(--navy-light);">
      Sudah punya akun? <a href="<?= BASE_URL ?>login.php" style="font-weight: 700; color: var(--primary-blue);">Masuk di sini</a>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
