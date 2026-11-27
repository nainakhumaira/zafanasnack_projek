<?php
require_once __DIR__ . '/config/koneksi.php';

// Jika sudah login, redirect sesuai role
if (is_logged_in()) {
    if (is_admin()) {
        header("Location: " . BASE_URL . "admin/");
    } else {
        header("Location: " . BASE_URL);
    }
    exit;
}

$error = '';
$redirect = $_GET['redirect'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi!';
    } else {
        $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($user && password_verify($password, $user['password'])) {
            // Set session login
            $_SESSION['user'] = [
                'id_user' => $user['id_user'],
                'nama_lengkap' => $user['nama_lengkap'],
                'email' => $user['email'],
                'no_hp' => $user['no_hp'],
                'alamat_lengkap' => $user['alamat_lengkap'],
                'role' => $user['role']
            ];

            if ($user['role'] === 'admin') {
                header("Location: " . BASE_URL . "admin/");
                exit;
            }

            if (!empty($redirect) && file_exists(__DIR__ . '/' . $redirect)) {
                header("Location: " . BASE_URL . $redirect);
            } else {
                header("Location: " . BASE_URL);
            }
            exit;
        } else {
            $error = 'Email atau kata sandi yang Anda masukkan salah!';
        }
    }
}

$page_title = "Masuk Akun";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container" style="padding: 60px 20px 100px;">
  <div style="max-width: 440px; margin: 0 auto; background: var(--white); border-radius: 16px; border: 1px solid var(--border-color); padding: 35px 30px; box-shadow: var(--shadow-md);">
    
    <div style="text-align: center; margin-bottom: 28px;">
      <div style="display: inline-flex; align-items: center; justify-content: center; width: 60px; height: 60px; background-color: var(--light-blue); color: var(--primary-blue); border-radius: 50%; font-size: 26px; margin-bottom: 12px;">
        <i class="fa-solid fa-cookie-bite"></i>
      </div>
      <h2 style="font-size: 24px; color: var(--navy); font-weight: 800;">Masuk Akun</h2>
      <p style="color: var(--navy-light); font-size: 14px; margin-top: 4px;">Selamat datang kembali di ZAFANA SNACK</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?= get_flash() ?>

    <form action="<?= BASE_URL ?>login.php<?= !empty($redirect) ? '?redirect=' . urlencode($redirect) : '' ?>" method="POST">
      <div class="form-group">
        <label class="form-label">Alamat Email</label>
        <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label">Kata Sandi</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>

      <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 10px;">
        <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Sekarang
      </button>
    </form>

    <div style="text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color); font-size: 14px; color: var(--navy-light);">
      Belum memiliki akun? <a href="<?= BASE_URL ?>register.php" style="font-weight: 700; color: var(--primary-blue);">Daftar di sini</a>
    </div>

    <!-- Info Demo Akun untuk Penguji / Dosen -->
    <div style="margin-top: 25px; background: var(--bg-soft); border: 1px dashed var(--border-color); border-radius: 8px; padding: 12px; font-size: 12px; color: var(--navy-light);">
      <strong style="color: var(--navy);">Demo Login:</strong><br>
      • Admin: <code>admin@zafanasnack.com</code> / <code>admin123</code><br>
      • Pelanggan: <code>sinta@gmail.com</code> / <code>pelanggan123</code>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
