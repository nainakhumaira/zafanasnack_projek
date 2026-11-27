<?php
require_once __DIR__ . '/config/koneksi.php';

if (!is_logged_in()) {
    set_flash('info', 'Silakan masuk terlebih dahulu untuk mengakses profil Anda.');
    header("Location: " . BASE_URL . "login.php?redirect=profil.php");
    exit;
}

$user = current_user();
$id_user = $user['id_user'];
$success_msg = '';
$error_msg = '';

// Ambil data user terbaru dari DB
$stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$user_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Update Data Profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $alamat_lengkap = trim($_POST['alamat_lengkap'] ?? '');
    $password_baru = $_POST['password_baru'] ?? '';

    if (empty($nama_lengkap) || empty($no_hp)) {
        $error_msg = 'Nama lengkap dan nomor WhatsApp tidak boleh kosong!';
    } else {
        if (!empty($password_baru)) {
            if (strlen($password_baru) < 6) {
                $error_msg = 'Password baru minimal harus 6 karakter!';
            } else {
                $hashed = password_hash($password_baru, PASSWORD_BCRYPT);
                $stmt_up = mysqli_prepare($koneksi, "UPDATE users SET nama_lengkap = ?, no_hp = ?, alamat_lengkap = ?, password = ? WHERE id_user = ?");
                mysqli_stmt_bind_param($stmt_up, "ssssi", $nama_lengkap, $no_hp, $alamat_lengkap, $hashed, $id_user);
                mysqli_stmt_execute($stmt_up);
                $success_msg = 'Profil dan kata sandi berhasil diperbarui!';
            }
        } else {
            $stmt_up = mysqli_prepare($koneksi, "UPDATE users SET nama_lengkap = ?, no_hp = ?, alamat_lengkap = ? WHERE id_user = ?");
            mysqli_stmt_bind_param($stmt_up, "sssi", $nama_lengkap, $no_hp, $alamat_lengkap, $id_user);
            mysqli_stmt_execute($stmt_up);
            $success_msg = 'Informasi profil Anda berhasil diperbarui!';
        }

        if (empty($error_msg)) {
            // Update session
            $_SESSION['user']['nama_lengkap'] = $nama_lengkap;
            $_SESSION['user']['no_hp'] = $no_hp;
            $_SESSION['user']['alamat_lengkap'] = $alamat_lengkap;

            // Refresh user_data
            $user_data['nama_lengkap'] = $nama_lengkap;
            $user_data['no_hp'] = $no_hp;
            $user_data['alamat_lengkap'] = $alamat_lengkap;
        }
    }
}

// Ambil Statistik Belanja User
$q_stats = mysqli_query($koneksi, "SELECT COUNT(*) as total_order, 
                                          COALESCE(SUM(total_bayar), 0) as total_bayar 
                                   FROM pesanan WHERE id_user = $id_user");
$stats = mysqli_fetch_assoc($q_stats);

$page_title = "Profil Saya - ZAFANA SNACK";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div style="background-color: var(--soft-gray); padding: 30px 0; border-bottom: 1px solid var(--border-color);">
  <div class="container">
    <h1 style="font-size: 26px; color: var(--navy); font-weight: 800; margin-bottom: 4px;">Profil Pelanggan</h1>
    <p style="color: var(--navy-light);">Kelola data pribadi, kontak, alamat pengiriman default, dan keamanan akun Anda.</p>
  </div>
</div>

<div class="container" style="padding: 40px 20px 80px;">
  <?php if (!empty($success_msg)): ?>
    <div class="alert alert-success"><?= $success_msg ?></div>
  <?php endif; ?>

  <?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger"><?= $error_msg ?></div>
  <?php endif; ?>

  <div class="profile-layout-grid scroll-animate animate-fade-up">
    
    <!-- Kolom Kiri: Kartu Identitas & Statistik Belanja -->
    <div>
      <div class="card" style="text-align: center; padding: 30px 20px;">
        <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--light-blue); color: var(--primary-blue); display: flex; align-items: center; justify-content: center; font-size: 34px; margin: 0 auto 15px; border: 2px solid var(--light-blue-hover);">
          <i class="fa-solid fa-user"></i>
        </div>
        <h3 style="font-size: 18px; color: var(--navy); margin-bottom: 4px;"><?= htmlspecialchars($user_data['nama_lengkap']) ?></h3>
        <p style="color: var(--navy-light); font-size: 13px; margin-bottom: 16px;"><?= htmlspecialchars($user_data['email']) ?></p>

        <span style="background: #DCFCE7; color: #16A34A; font-weight: 700; font-size: 11px; padding: 4px 12px; border-radius: 20px; display: inline-block;">
          Member Pelanggan Aktif
        </span>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border-color);">
          <div style="background: var(--soft-gray); padding: 12px; border-radius: var(--radius-sm);">
            <div style="font-size: 18px; font-weight: 800; color: var(--primary-blue);"><?= (int)$stats['total_order'] ?></div>
            <div style="font-size: 11px; color: var(--navy-light);">Total Pesanan</div>
          </div>
          <div style="background: var(--soft-gray); padding: 12px; border-radius: var(--radius-sm);">
            <div style="font-size: 15px; font-weight: 800; color: var(--primary-blue);"><?= rupiah($stats['total_bayar']) ?></div>
            <div style="font-size: 11px; color: var(--navy-light);">Total Belanja</div>
          </div>
        </div>

        <a href="<?= BASE_URL ?>riwayat_pesanan.php" class="btn btn-primary btn-block btn-sm" style="margin-top: 20px;">
          <i class="fa-solid fa-receipt"></i> Cek Riwayat Pesanan
        </a>
      </div>
    </div>

    <!-- Kolom Kanan: Form Edit Profil -->
    <div class="card">
      <div class="card-header">
        <h3><i class="fa-solid fa-user-pen text-blue"></i> Perbarui Data Diri</h3>
      </div>

      <form action="<?= BASE_URL ?>profil.php" method="POST">
        <div class="form-group">
          <label class="form-label">Nama Lengkap *</label>
          <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user_data['nama_lengkap']) ?>" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
          <div class="form-group">
            <label class="form-label">Alamat Email (Tidak dapat diubah)</label>
            <input type="email" class="form-control" value="<?= htmlspecialchars($user_data['email']) ?>" readonly style="background-color: var(--soft-gray); cursor: not-allowed;">
          </div>

          <div class="form-group">
            <label class="form-label">Nomor WhatsApp Aktif *</label>
            <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($user_data['no_hp']) ?>" required>
            <small style="color: var(--navy-light); font-size: 11px;">Digunakan untuk konfirmasi pesanan & resi.</small>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Alamat Lengkap Pengiriman Default</label>
          <textarea name="alamat_lengkap" class="form-control" rows="3" placeholder="Contoh: Jl. Kenanga No. 4, RT 02/RW 03, Kel. Melati, Bandung, 40123"><?= htmlspecialchars($user_data['alamat_lengkap'] ?? '') ?></textarea>
          <small style="color: var(--navy-light); font-size: 11px;">Alamat ini akan otomatis terisi saat Anda melakukan checkout camilan.</small>
        </div>

        <div class="form-group" style="margin-top: 25px; padding-top: 20px; border-top: 1px dashed var(--border-color);">
          <label class="form-label">Kata Sandi Baru (Opsional)</label>
          <input type="password" name="password_baru" class="form-control" placeholder="Biarkan kosong jika tidak ingin mengganti kata sandi saat ini">
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="margin-top: 10px;">
          <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Profil
        </button>
      </form>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
