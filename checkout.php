<?php
require_once __DIR__ . '/config/koneksi.php';

// Cek apakah sudah login
if (!is_logged_in()) {
    set_flash('info', 'Silakan masuk atau daftar akun terlebih dahulu untuk melanjutkan proses checkout pesanan.');
    header("Location: " . BASE_URL . "login.php?redirect=checkout.php");
    exit;
}

// Cek apakah keranjang kosong
if (empty($_SESSION['cart'])) {
    set_flash('warning', 'Keranjang belanjamu kosong. Silakan pilih snack terlebih dahulu.');
    header("Location: " . BASE_URL . "katalog.php");
    exit;
}

$user = current_user();
$error = '';

$total_produk = 0;
$total_berat = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_produk += ($item['harga'] * $item['qty']);
    $total_berat += ($item['berat_gram'] * $item['qty']);
}

// Biaya Ongkir Flat Sederhana (Bagus untuk demo tugas kuliah & toko snack)
$ongkir = 10000;
$total_bayar = $total_produk + $ongkir;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_penerima = trim($_POST['nama_penerima'] ?? '');
    $no_hp_penerima = trim($_POST['no_hp_penerima'] ?? '');
    $alamat_kirim = trim($_POST['alamat_kirim'] ?? '');
    $catatan = trim($_POST['catatan'] ?? '');
    $metode_pembayaran = $_POST['metode_pembayaran'] ?? 'Transfer Bank / QRIS';

    if (empty($nama_penerima) || empty($no_hp_penerima) || empty($alamat_kirim)) {
        $error = 'Nama penerima, nomor WhatsApp, dan alamat pengiriman wajib diisi!';
    } else {
        // Buat Kode Pesanan Unik: ZF-20261120-123
        $kode_pesanan = 'ZF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $tanggal_pesanan = date('Y-m-d H:i:s');
        $status_pesanan = 'menunggu_pembayaran';

        // 1. Simpan ke tabel pesanan
        $stmt_order = mysqli_prepare($koneksi, "INSERT INTO pesanan (kode_pesanan, id_user, tanggal_pesanan, total_produk, ongkir, total_bayar, nama_penerima, no_hp_penerima, alamat_kirim, catatan, metode_pembayaran, status_pesanan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $id_user = $user['id_user'];
        mysqli_stmt_bind_param($stmt_order, "sisiisssssss", $kode_pesanan, $id_user, $tanggal_pesanan, $total_produk, $ongkir, $total_bayar, $nama_penerima, $no_hp_penerima, $alamat_kirim, $catatan, $metode_pembayaran, $status_pesanan);

        if (mysqli_stmt_execute($stmt_order)) {
            $id_pesanan = mysqli_insert_id($koneksi);

            // 2. Simpan rincian ke tabel detail_pesanan & kurangi stok produk
            $stmt_detail = mysqli_prepare($koneksi, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_satuan, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmt_stok = mysqli_prepare($koneksi, "UPDATE produk SET stok = GREATEST(0, stok - ?) WHERE id_produk = ?");

            foreach ($_SESSION['cart'] as $id_produk => $item) {
                $jumlah = $item['qty'];
                $harga_satuan = $item['harga'];
                $subtotal = $harga_satuan * $jumlah;

                // Insert detail
                mysqli_stmt_bind_param($stmt_detail, "iiiii", $id_pesanan, $id_produk, $jumlah, $harga_satuan, $subtotal);
                mysqli_stmt_execute($stmt_detail);

                // Update stok
                mysqli_stmt_bind_param($stmt_stok, "ii", $jumlah, $id_produk);
                mysqli_stmt_execute($stmt_stok);
            }

            // Kosongkan keranjang belanja
            $_SESSION['cart'] = [];

            // Arahkan ke halaman sukses
            header("Location: " . BASE_URL . "order_sukses.php?kode=" . urlencode($kode_pesanan));
            exit;
        } else {
            $error = 'Gagal menyimpan pesanan. Silakan coba kembali.';
        }
    }
}

$page_title = "Checkout Pembayaran";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div style="background-color: var(--bg-soft); padding: 30px 0; border-bottom: 1px solid var(--border-color);">
  <div class="container">
    <h1 style="font-size: 26px; color: var(--navy); font-weight: 800; margin-bottom: 4px;">Checkout Pesanan</h1>
    <p style="color: var(--navy-light);">Lengkapi alamat pengiriman dan konfirmasi pesanan camilanmu.</p>
  </div>
</div>

<div class="container" style="padding: 40px 20px 80px;">
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form action="<?= BASE_URL ?>checkout.php" method="POST">
    <div class="checkout-layout-grid scroll-animate animate-fade-up">
      
      <!-- Kolom Kiri: Form Alamat & Penerima -->
      <div class="card">
        <div class="card-header">
          <h3><i class="fa-solid fa-location-dot" style="color: var(--primary-blue);"></i> Informasi Pengiriman</h3>
        </div>

        <div class="form-group">
          <label class="form-label">Nama Lengkap Penerima *</label>
          <input type="text" name="nama_penerima" class="form-control" value="<?= htmlspecialchars($_POST['nama_penerima'] ?? $user['nama_lengkap']) ?>" required>
        </div>

        <div class="form-group">
          <label class="form-label">Nomor WhatsApp Penerima *</label>
          <input type="text" name="no_hp_penerima" class="form-control" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($_POST['no_hp_penerima'] ?? $user['no_hp']) ?>" required>
          <small style="color: var(--navy-light); font-size: 12px;">Untuk konfirmasi status pengiriman & update resi kurir.</small>
        </div>

        <div class="form-group">
          <label class="form-label">Alamat Lengkap Tujuan *</label>
          <textarea name="alamat_kirim" class="form-control" rows="3" placeholder="Contoh: Jl. Mawar No. 10 RT 02/RW 03, Kel. Melati, Kec. Sejahtera, Kota Bandung, 40123" required><?= htmlspecialchars($_POST['alamat_kirim'] ?? $user['alamat_lengkap']) ?></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Catatan Tambahan untuk Penjual (Opsional)</label>
          <input type="text" name="catatan" class="form-control" placeholder="Contoh: Tolong bungkus bubble wrap tebal / titip di satpam" value="<?= htmlspecialchars($_POST['catatan'] ?? '') ?>">
        </div>

        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label">Metode Pembayaran</label>
          <select name="metode_pembayaran" class="form-control">
            <option value="Transfer Bank BCA / Mandiri">Transfer Bank (BCA / Mandiri / BNI)</option>
            <option value="QRIS (GoPay / OVO / Dana / ShopeePay)">QRIS All E-Wallet</option>
            <option value="COD / Bayar di Tempat">COD / Bayar di Tempat (Khusus Area Sekitar Kampus)</option>
          </select>
        </div>
      </div>

      <!-- Kolom Kanan: Rincian Tagihan -->
      <div class="card">
        <div class="card-header">
          <h3>Rincian Camilan</h3>
        </div>

        <div style="max-height: 220px; overflow-y: auto; margin-bottom: 16px; padding-right: 5px;">
          <?php foreach ($_SESSION['cart'] as $item): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 13px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
              <div>
                <strong style="color: var(--navy);"><?= htmlspecialchars($item['nama_produk']) ?></strong>
                <div style="color: var(--navy-light);"><?= $item['qty'] ?> × <?= rupiah($item['harga']) ?></div>
              </div>
              <div style="font-weight: 700; color: var(--navy);">
                <?= rupiah($item['harga'] * $item['qty']) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
          <span style="color: var(--navy-light);">Subtotal Produk:</span>
          <span style="font-weight: 600; color: var(--navy);"><?= rupiah($total_produk) ?></span>
        </div>

        <div style="display: flex; justify-content: space-between; margin-bottom: 16px; font-size: 14px;">
          <span style="color: var(--navy-light);">Ongkos Kirim (Flat):</span>
          <span style="font-weight: 600; color: var(--navy);"><?= rupiah($ongkir) ?></span>
        </div>

        <div style="display: flex; justify-content: space-between; margin-bottom: 24px; padding-top: 14px; border-top: 2px solid var(--border-color); font-size: 16px;">
          <span style="font-weight: 800; color: var(--navy);">Total Pembayaran:</span>
          <span style="font-size: 20px; font-weight: 800; color: var(--primary-blue);"><?= rupiah($total_bayar) ?></span>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-bottom: 12px;">
          <i class="fa-solid fa-lock"></i> Buat Pesanan Sekarang
        </button>

        <a href="<?= BASE_URL ?>keranjang.php" class="btn btn-outline btn-block btn-sm">
          <i class="fa-solid fa-arrow-left"></i> Kembali ke Keranjang
        </a>
      </div>

    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
