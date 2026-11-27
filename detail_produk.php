<?php
require_once __DIR__ . '/config/koneksi.php';

$id_produk = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = mysqli_prepare($koneksi, "SELECT p.*, k.nama_kategori, k.slug AS kat_slug 
                                  FROM produk p 
                                  JOIN kategori k ON p.id_kategori = k.id_kategori 
                                  WHERE p.id_produk = ?");
mysqli_stmt_bind_param($stmt, "i", $id_produk);
mysqli_stmt_execute($stmt);
$produk = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$produk) {
    header("Location: " . BASE_URL . "katalog.php");
    exit;
}

// Ambil Produk Terkait dari Kategori yang Sama
$stmt_related = mysqli_prepare($koneksi, "SELECT p.*, k.nama_kategori 
                                         FROM produk p 
                                         JOIN kategori k ON p.id_kategori = k.id_kategori 
                                         WHERE p.id_kategori = ? AND p.id_produk != ? AND p.status = 'aktif' 
                                         ORDER BY RAND() LIMIT 4");
mysqli_stmt_bind_param($stmt_related, "ii", $produk['id_kategori'], $id_produk);
mysqli_stmt_execute($stmt_related);
$result_related = mysqli_stmt_get_result($stmt_related);

$page_title = $produk['nama_produk'] . " - ZAFANA SNACK";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$pesan_wa = urlencode("Halo Admin Zafana Snack, saya tertarik memesan snack *" . $produk['nama_produk'] . "* seharga " . rupiah($produk['harga']) . ". Apakah stoknya ready?");
?>

<!-- Breadcrumbs Bar -->
<div style="background-color: var(--soft-gray); padding: 16px 0; border-bottom: 1px solid var(--border-color);">
  <div class="container">
    <div style="font-size: 13px; color: var(--navy-light);">
      <a href="<?= BASE_URL ?>" style="color: var(--navy-light);"><i class="fa-solid fa-house"></i> Beranda</a> &raquo; 
      <a href="<?= BASE_URL ?>katalog.php" style="color: var(--navy-light);">Katalog</a> &raquo; 
      <a href="<?= BASE_URL ?>katalog.php?kategori=<?= urlencode($produk['kat_slug']) ?>" style="color: var(--navy-light);"><?= htmlspecialchars($produk['nama_kategori']) ?></a> &raquo; 
      <span style="color: var(--navy); font-weight: 700;"><?= htmlspecialchars($produk['nama_produk']) ?></span>
    </div>
  </div>
</div>

<div class="container" style="padding: 40px 20px 80px;">
  <?= get_flash() ?>

  <div class="detail-layout-grid scroll-animate animate-fade-up">
    
    <!-- Bagian Foto Produk -->
    <div>
      <div style="background-color: var(--soft-gray); border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border-color); height: 420px; display: flex; align-items: center; justify-content: center; position: relative; padding: 12px;">
        <img src="<?= get_product_image_url($produk['foto'] ?? '') ?>" 
             alt="<?= htmlspecialchars($produk['nama_produk']) ?>" 
             style="max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain;"
             loading="lazy"
             onerror="this.onerror=null; this.src='<?= BASE_URL ?>assets/images/products/default_snack.jpg';">
        
        <?php if (!empty($produk['harga_coret']) && $produk['harga_coret'] > $produk['harga']): ?>
          <?php $hemat_persen = round((($produk['harga_coret'] - $produk['harga']) / $produk['harga_coret']) * 100); ?>
          <span style="position: absolute; top: 16px; left: 16px; background: #EF4444; color: #fff; font-weight: 800; font-size: 13px; padding: 5px 12px; border-radius: 20px; box-shadow: var(--shadow-sm);">
            Diskon <?= $hemat_persen ?>%
          </span>
        <?php endif; ?>
      </div>

      <!-- Jaminan Keamanan Produk -->
      <div style="display: flex; gap: 15px; margin-top: 15px; justify-content: center; font-size: 12px; color: var(--navy-light);">
        <span><i class="fa-solid fa-shield-halved text-success"></i> Higienis & Halal</span>
        <span>•</span>
        <span><i class="fa-solid fa-box text-blue"></i> Kemasan Zipper Tebal</span>
        <span>•</span>
        <span><i class="fa-solid fa-truck-fast text-warning"></i> Ready Kirim</span>
      </div>
    </div>

    <!-- Bagian Informasi Produk & Form Order -->
    <div>
      <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
        <span class="badge-tag tag-category" style="position: static;">
          <?= htmlspecialchars($produk['nama_kategori']) ?>
        </span>
        <div class="stars">
          <i class="fa-solid fa-star"></i>
          <strong style="color: var(--navy);"><?= number_format($produk['rating'], 1) ?></strong>
        </div>
        <span style="font-size: 13px; color: var(--navy-light);">• Terjual <?= (int)$produk['terjual'] ?> bungkus</span>
      </div>

      <h1 style="font-size: 28px; color: var(--navy); font-weight: 800; line-height: 1.25; margin-bottom: 12px;">
        <?= htmlspecialchars($produk['nama_produk']) ?>
      </h1>

      <div style="display: flex; align-items: baseline; gap: 12px; margin-bottom: 20px;">
        <span style="font-size: 32px; font-weight: 900; color: var(--primary-blue); font-family: var(--font-heading);">
          <?= rupiah($produk['harga']) ?>
        </span>
        <?php if (!empty($produk['harga_coret']) && $produk['harga_coret'] > $produk['harga']): ?>
          <span style="font-size: 18px; color: var(--navy-light); text-decoration: line-through;">
            <?= rupiah($produk['harga_coret']) ?>
          </span>
        <?php endif; ?>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px; padding: 14px; background: var(--soft-gray); border-radius: var(--radius-sm); border: 1px solid var(--border-color); margin-bottom: 24px; font-size: 13px;">
        <div>
          <span style="color: var(--navy-light); display: block;">Berat Bersih:</span>
          <strong style="color: var(--navy); font-size: 14px;"><i class="fa-solid fa-weight-scale text-blue"></i> <?= (int)$produk['berat_gram'] ?> gram</strong>
        </div>
        <div>
          <span style="color: var(--navy-light); display: block;">Ketersediaan:</span>
          <?php if ($produk['stok'] > 0): ?>
            <strong style="color: #10B981; font-size: 14px;"><i class="fa-solid fa-circle-check"></i> Ready (<?= (int)$produk['stok'] ?> pcs)</strong>
          <?php else: ?>
            <strong style="color: #EF4444; font-size: 14px;"><i class="fa-solid fa-circle-xmark"></i> Stok Habis</strong>
          <?php endif; ?>
        </div>
        <div>
          <span style="color: var(--navy-light); display: block;">Kemasan:</span>
          <strong style="color: var(--navy); font-size: 14px;"><i class="fa-solid fa-box text-blue"></i> Standing Pouch</strong>
        </div>
      </div>

      <div style="margin-bottom: 26px;">
        <h4 style="font-size: 15px; font-weight: 700; color: var(--navy); margin-bottom: 8px;">Deskripsi Camilan:</h4>
        <div style="color: var(--navy); line-height: 1.7; font-size: 14px;">
          <?= nl2br(htmlspecialchars($produk['deskripsi'])) ?>
        </div>
      </div>

      <!-- Form Tambah ke Keranjang dengan Kuantitas -->
      <?php if ($produk['stok'] > 0): ?>
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
          <label style="font-weight: 700; font-size: 14px; color: var(--navy); margin: 0;">Jumlah:</label>
          <div style="display: flex; align-items: center; border: 1px solid var(--border-color); border-radius: var(--radius-sm); overflow: hidden; background: var(--white); box-shadow: var(--shadow-xs);">
            <button type="button" id="detailQtyMinus" style="width: 40px; height: 40px; background: var(--soft-gray); border: none; cursor: pointer; font-size: 18px; font-weight: 700; color: var(--navy);">-</button>
            <input type="number" id="detailQtyInput" value="1" min="1" max="<?= $produk['stok'] ?>" style="width: 55px; height: 40px; border: none; text-align: center; font-weight: 800; font-size: 15px; color: var(--navy);" readonly>
            <button type="button" id="detailQtyPlus" style="width: 40px; height: 40px; background: var(--soft-gray); border: none; cursor: pointer; font-size: 18px; font-weight: 700; color: var(--navy);">+</button>
          </div>
          <span style="font-size: 13px; color: var(--navy-light);">Maksimal <?= $produk['stok'] ?> pcs</span>
        </div>

        <div style="display: flex; gap: 14px; flex-wrap: wrap;">
          <button type="button" class="btn btn-primary btn-lg btn-submit-cart" data-id="<?= $produk['id_produk'] ?>" style="flex: 1.2;">
            <i class="fa-solid fa-cart-plus"></i> Tambah ke Keranjang
          </button>
          
          <a href="https://wa.me/<?= WA_STORE_NUMBER ?>?text=<?= $pesan_wa ?>" target="_blank" class="btn btn-outline btn-lg" style="border-color: #25D366; color: #16A34A !important; flex: 1;">
            <i class="fa-brands fa-whatsapp"></i> Beli via WhatsApp
          </a>
        </div>
      <?php else: ?>
        <div class="alert alert-danger" style="margin-bottom: 16px;">
          Maaf, stok snack ini sedang habis diproduksi. Kamu bisa tanya jadwal restock ke WhatsApp kami.
        </div>
        <a href="https://wa.me/<?= WA_STORE_NUMBER ?>?text=<?= $pesan_wa ?>" target="_blank" class="btn btn-primary btn-lg">
          <i class="fa-brands fa-whatsapp"></i> Tanya Restock via WhatsApp
        </a>
      <?php endif; ?>

    </div>
  </div>

  <!-- PRODUK TERKAIT LAINNYA -->
  <?php if ($result_related && mysqli_num_rows($result_related) > 0): ?>
    <div class="section-title scroll-animate animate-fade-up" style="text-align: left; margin-bottom: 25px;">
      <span class="section-tag"><i class="fa-solid fa-cookie"></i> Rasa Serupa</span>
      <h2>Kamu Mungkin Juga Suka</h2>
    </div>

    <div class="product-grid">
      <?php while ($rp = mysqli_fetch_assoc($result_related)): 
        $foto_related = get_product_image_url($rp['foto'] ?? '');
      ?>
        <div class="product-card scroll-animate animate-fade-up" data-title="<?= htmlspecialchars($rp['nama_produk']) ?>" data-category="<?= htmlspecialchars($rp['nama_kategori']) ?>">
          <div class="product-thumb">
            <a href="<?= BASE_URL ?>detail_produk.php?id=<?= $rp['id_produk'] ?>">
              <img src="<?= $foto_related ?>" 
                   alt="<?= htmlspecialchars($rp['nama_produk']) ?>"
                   loading="lazy"
                   onerror="this.onerror=null; this.src='<?= BASE_URL ?>assets/images/products/default_snack.jpg';">
            </a>
          </div>

          <div class="product-body">
            <h3 class="product-title">
              <a href="<?= BASE_URL ?>detail_produk.php?id=<?= $rp['id_produk'] ?>">
                <?= htmlspecialchars($rp['nama_produk']) ?>
              </a>
            </h3>

            <div class="product-footer-row">
              <div class="product-price-box">
                <span class="price-current"><?= rupiah($rp['harga']) ?></span>
              </div>
              <button type="button" class="btn-add-cart btn-quick-add" data-id="<?= $rp['id_produk'] ?>" title="Tambah ke Keranjang">
                <i class="fa-solid fa-cart-plus"></i>
              </button>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
