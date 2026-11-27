<?php
require_once __DIR__ . '/config/koneksi.php';

// Wajib login untuk melihat riwayat pesanan
if (!is_logged_in()) {
    set_flash('info', 'Silakan masuk untuk melihat daftar pesanan Anda.');
    header("Location: " . BASE_URL . "login.php?redirect=riwayat_pesanan.php");
    exit;
}

$user = current_user();

// Ambil riwayat pesanan user
$stmt = mysqli_prepare($koneksi, "SELECT * FROM pesanan WHERE id_user = ? ORDER BY id_pesanan DESC");
mysqli_stmt_bind_param($stmt, "i", $user['id_user']);
mysqli_stmt_execute($stmt);
$result_pesanan = mysqli_stmt_get_result($stmt);

$page_title = "Riwayat Pesanan Saya";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

function badge_status($status) {
    switch ($status) {
        case 'menunggu_pembayaran':
            return '<span style="background: #FEF3C7; color: #92400E; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;"><i class="fa-solid fa-clock"></i> Menunggu Bayar</span>';
        case 'diproses':
            return '<span style="background: #DBEAFE; color: #1E40AF; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;"><i class="fa-solid fa-spinner"></i> Diproses</span>';
        case 'dikirim':
            return '<span style="background: #E0E7FF; color: #3730A3; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;"><i class="fa-solid fa-truck"></i> Sedang Dikirim</span>';
        case 'selesai':
            return '<span style="background: #D1FAE5; color: #065F46; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;"><i class="fa-solid fa-circle-check"></i> Selesai</span>';
        case 'dibatalkan':
            return '<span style="background: #FEE2E2; color: #991B1B; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;"><i class="fa-solid fa-circle-xmark"></i> Dibatalkan</span>';
        default:
            return htmlspecialchars($status);
    }
}
?>

<div style="background-color: var(--bg-soft); padding: 30px 0; border-bottom: 1px solid var(--border-color);">
  <div class="container">
    <h1 style="font-size: 26px; color: var(--navy); font-weight: 800; margin-bottom: 4px;">Riwayat Pesanan Saya</h1>
    <p style="color: var(--navy-light);">Pantau status pengiriman camilan dan rekam jejak belanjamu.</p>
  </div>
</div>

<div class="container" style="padding: 40px 20px 80px;">
  <?= get_flash() ?>

  <?php if ($result_pesanan && mysqli_num_rows($result_pesanan) > 0): ?>
    <div style="display: flex; flex-direction: column; gap: 20px;">
      <?php while ($row = mysqli_fetch_assoc($result_pesanan)): 
        // Ambil item produk untuk pesanan ini
        $stmt_items = mysqli_prepare($koneksi, "SELECT d.*, p.nama_produk, p.foto 
                                                FROM detail_pesanan d 
                                                JOIN produk p ON d.id_produk = p.id_produk 
                                                WHERE d.id_pesanan = ?");
        mysqli_stmt_bind_param($stmt_items, "i", $row['id_pesanan']);
        mysqli_stmt_execute($stmt_items);
        $res_items = mysqli_stmt_get_result($stmt_items);
      ?>
        <div class="card scroll-animate animate-fade-up" style="margin-bottom: 0;">
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; padding-bottom: 14px; border-bottom: 1px solid var(--border-color); margin-bottom: 16px;">
            <div>
              <span style="font-weight: 800; color: var(--primary-blue); font-size: 16px;">
                <?= htmlspecialchars($row['kode_pesanan']) ?>
              </span>
              <span style="font-size: 13px; color: var(--navy-light); margin-left: 10px;">
                <i class="fa-regular fa-calendar"></i> <?= date('d M Y, H:i', strtotime($row['tanggal_pesanan'])) ?> WIB
              </span>
            </div>
            <div>
              <?= badge_status($row['status_pesanan']) ?>
            </div>
          </div>

          <!-- Daftar Produk -->
          <div style="margin-bottom: 16px;">
            <?php while ($item = mysqli_fetch_assoc($res_items)): ?>
              <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 10px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                  <img src="<?= get_product_image_url($item['foto'] ?? '') ?>" 
                       style="width: 44px; height: 44px; border-radius: 8px; border: 1px solid var(--border-color); object-fit: cover; background: var(--bg-soft);"
                       loading="lazy"
                       onerror="this.onerror=null; this.src='<?= BASE_URL ?>assets/images/products/default_snack.jpg';">
                  <div>
                    <strong style="color: var(--navy); font-size: 14px;"><?= htmlspecialchars($item['nama_produk']) ?></strong>
                    <div style="color: var(--navy-light); font-size: 12px;"><?= $item['jumlah'] ?> pcs × <?= rupiah($item['harga_satuan']) ?></div>
                  </div>
                </div>
                <div style="font-weight: 700; color: var(--navy); font-size: 14px;">
                  <?= rupiah($item['subtotal']) ?>
                </div>
              </div>
            <?php endwhile; ?>
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding-top: 14px; border-top: 1px dashed var(--border-color);">
            <div style="font-size: 13px; color: var(--navy-light);">
              Penerima: <strong style="color: var(--navy);"><?= htmlspecialchars($row['nama_penerima']) ?></strong> (<?= htmlspecialchars($row['no_hp_penerima']) ?>)
            </div>

            <div style="display: flex; align-items: center; gap: 15px;">
              <div style="text-align: right;">
                <div style="font-size: 12px; color: var(--navy-light);">Total Tagihan:</div>
                <div style="font-size: 18px; font-weight: 800; color: var(--primary-blue);"><?= rupiah($row['total_bayar']) ?></div>
              </div>

              <a href="https://wa.me/<?= WA_STORE_NUMBER ?>?text=<?= urlencode('Halo Admin Zafana Snack, saya ingin tanya status pesanan saya dengan kode: ' . $row['kode_pesanan']) ?>" target="_blank" class="btn btn-sm btn-outline" style="border-color: #25D366; color: #16A34A !important;">
                <i class="fa-brands fa-whatsapp"></i> Chat Admin
              </a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <div style="text-align: center; padding: 70px 20px; background: var(--white); border-radius: 16px; border: 1px dashed var(--border-color); max-width: 550px; margin: 0 auto;">
      <i class="fa-solid fa-bag-shopping" style="font-size: 48px; color: var(--navy-light); margin-bottom: 16px;"></i>
      <h3 style="color: var(--navy); margin-bottom: 8px;">Belum Ada Riwayat Pesanan</h3>
      <p style="color: var(--navy-light); margin-bottom: 24px;">Kamu belum pernah melakukan pemesanan snack di sini.</p>
      <a href="<?= BASE_URL ?>katalog.php" class="btn btn-primary">Mulai Belanja Camilan</a>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
