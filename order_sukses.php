<?php
require_once __DIR__ . '/config/koneksi.php';

$kode_pesanan = trim($_GET['kode'] ?? '');

$stmt = mysqli_prepare($koneksi, "SELECT p.*, u.email 
                                  FROM pesanan p 
                                  JOIN users u ON p.id_user = u.id_user 
                                  WHERE p.kode_pesanan = ?");
mysqli_stmt_bind_param($stmt, "s", $kode_pesanan);
mysqli_stmt_execute($stmt);
$pesanan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$pesanan) {
    header("Location: " . BASE_URL);
    exit;
}

// Ambil item detail pesanan
$stmt_detail = mysqli_prepare($koneksi, "SELECT d.*, p.nama_produk 
                                         FROM detail_pesanan d 
                                         JOIN produk p ON d.id_produk = p.id_produk 
                                         WHERE d.id_pesanan = ?");
mysqli_stmt_bind_param($stmt_detail, "i", $pesanan['id_pesanan']);
mysqli_stmt_execute($stmt_detail);
$items = mysqli_stmt_get_result($stmt_detail);

// Siapkan pesan otomatis untuk WhatsApp
$wa_text = "Halo Admin ZAFANA SNACK,\n";
$wa_text .= "Saya telah membuat pesanan dengan rincian berikut:\n";
$wa_text .= "• *Kode Pesanan*: " . $pesanan['kode_pesanan'] . "\n";
$wa_text .= "• *Nama Penerima*: " . $pesanan['nama_penerima'] . "\n";
$wa_text .= "• *Total Pembayaran*: " . rupiah($pesanan['total_bayar']) . "\n";
$wa_text .= "• *Metode*: " . $pesanan['metode_pembayaran'] . "\n\n";
$wa_text .= "Mohon diproses ya, terima kasih!";
$wa_url = "https://wa.me/" . WA_STORE_NUMBER . "?text=" . urlencode($wa_text);

$page_title = "Pesanan Berhasil Dibuat";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container" style="padding: 50px 20px 90px;">
  <div style="max-width: 680px; margin: 0 auto; background: var(--white); border-radius: 16px; border: 1px solid var(--border-color); padding: 40px 30px; box-shadow: var(--shadow-md); text-align: center;">
    
    <div style="width: 70px; height: 70px; background-color: #ECFDF5; color: #10B981; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 34px; margin: 0 auto 20px;">
      <i class="fa-solid fa-check"></i>
    </div>

    <h1 style="font-size: 26px; color: var(--navy); font-weight: 800; margin-bottom: 8px;">Terima Kasih Atas Pesananmu!</h1>
    <p style="color: var(--navy-light); font-size: 15px; margin-bottom: 25px;">
      Pesanan berhasil dicatat di sistem. Segera selesaikan pembayaran dan konfirmasi melalui WhatsApp agar camilanmu lekas dikirim.
    </p>

    <div style="background-color: var(--light-blue); border-radius: 12px; padding: 18px; margin-bottom: 25px; border: 1px solid #BFDBFE;">
      <div style="font-size: 13px; color: var(--navy-light); text-transform: uppercase; letter-spacing: 1px; font-weight: 700;">Kode Pesanan Anda</div>
      <div style="font-size: 24px; font-weight: 800; color: var(--primary-blue); margin-top: 4px;"><?= htmlspecialchars($pesanan['kode_pesanan']) ?></div>
    </div>

    <!-- Ringkasan Info -->
    <div style="text-align: left; background-color: var(--bg-soft); border-radius: 12px; padding: 20px; margin-bottom: 25px; border: 1px solid var(--border-color);">
      <h3 style="font-size: 16px; color: var(--navy); margin-bottom: 12px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
        Informasi Pembayaran
      </h3>

      <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
        <span style="color: var(--navy-light);">Nama Penerima:</span>
        <strong style="color: var(--navy);"><?= htmlspecialchars($pesanan['nama_penerima']) ?> (<?= htmlspecialchars($pesanan['no_hp_penerima']) ?>)</strong>
      </div>

      <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
        <span style="color: var(--navy-light);">Alamat Pengiriman:</span>
        <span style="color: var(--navy); max-width: 60%; text-align: right;"><?= htmlspecialchars($pesanan['alamat_kirim']) ?></span>
      </div>

      <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px;">
        <span style="color: var(--navy-light);">Metode Pembayaran:</span>
        <strong style="color: var(--navy);"><?= htmlspecialchars($pesanan['metode_pembayaran']) ?></strong>
      </div>

      <div style="display: flex; justify-content: space-between; padding-top: 10px; border-top: 1px dashed var(--border-color); font-size: 16px;">
        <span style="font-weight: 700; color: var(--navy);">Total yang Harus Dibayar:</span>
        <strong style="font-size: 18px; color: var(--primary-blue);"><?= rupiah($pesanan['total_bayar']) ?></strong>
      </div>

      <div style="margin-top: 16px; padding: 12px; background: var(--white); border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px;">
        <strong style="color: var(--navy);"><i class="fa-solid fa-building-columns"></i> Rekening Pembayaran Toko:</strong><br>
        • Bank BCA: <strong>123-456-7890</strong> a.n. ZAFANA SNACK<br>
        • Bank Mandiri: <strong>987-654-3210</strong> a.n. ZAFANA SNACK<br>
        • QRIS: Tersedia via chat WhatsApp admin
      </div>
    </div>

    <!-- Tombol Aksi WhatsApp -->
    <a href="<?= $wa_url ?>" target="_blank" class="btn btn-primary btn-lg btn-block" style="background-color: #25D366; border-color: #25D366; margin-bottom: 12px; font-size: 16px;">
      <i class="fa-brands fa-whatsapp"></i> Konfirmasi Pesanan ke WhatsApp Admin
    </a>

    <div style="display: flex; gap: 10px; justify-content: center; margin-top: 15px;">
      <a href="<?= BASE_URL ?>riwayat_pesanan.php" class="btn btn-outline btn-sm">
        <i class="fa-solid fa-clock-rotate-left"></i> Lihat Riwayat Pesanan
      </a>
      <a href="<?= BASE_URL ?>" class="btn btn-light btn-sm">
        <i class="fa-solid fa-house"></i> Kembali ke Beranda
      </a>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
