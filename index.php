<?php
$page_title = "ZAFANA SNACK - Teman Ngemil, Teman Bahagia!";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// 1. Ambil Kategori untuk Section Kategori (Prioritas 4 Kategori Utama)
$query_kategori = "SELECT k.*, COUNT(p.id_produk) AS total_produk 
                   FROM kategori k 
                   LEFT JOIN produk p ON k.id_kategori = p.id_kategori AND p.status = 'aktif'
                   GROUP BY k.id_kategori 
                   ORDER BY 
                     CASE 
                       WHEN k.slug = 'original-gurih' THEN 1
                       WHEN k.slug = 'peyek-kacang' THEN 2
                       WHEN k.slug = 'peyek-udang' THEN 3
                       WHEN k.slug = 'peyek-kacang-kedelai' THEN 4
                       ELSE 5
                     END, k.id_kategori ASC";
$result_kategori = mysqli_query($koneksi, $query_kategori);

// 2. Ambil Produk Terlaris (is_best_seller = 1 atau diurutkan berdasarkan terjual DESC)
$query_bestseller = "SELECT p.*, k.nama_kategori, k.slug AS kat_slug 
                     FROM produk p 
                     JOIN kategori k ON p.id_kategori = k.id_kategori 
                     WHERE p.status = 'aktif' AND (p.is_best_seller = 1 OR p.terjual > 0) 
                     ORDER BY p.is_best_seller DESC, p.terjual DESC, p.id_produk DESC LIMIT 4";
$result_bestseller = mysqli_query($koneksi, $query_bestseller);

// Fallback jika belum ada yang ditandai best seller
if (!$result_bestseller || mysqli_num_rows($result_bestseller) === 0) {
    $result_bestseller = mysqli_query($koneksi, "SELECT p.*, k.nama_kategori, k.slug AS kat_slug FROM produk p JOIN kategori k ON p.id_kategori = k.id_kategori WHERE p.status = 'aktif' ORDER BY p.id_produk DESC LIMIT 4");
}

// 3. Ambil Produk Promo / Diskon (is_promo = 1 atau harga_coret > harga)
$query_promo = "SELECT p.*, k.nama_kategori, k.slug AS kat_slug 
                FROM produk p 
                JOIN kategori k ON p.id_kategori = k.id_kategori 
                WHERE p.status = 'aktif' AND (p.is_promo = 1 OR p.harga_coret > p.harga) 
                ORDER BY p.id_produk DESC LIMIT 4";
$result_promo = mysqli_query($koneksi, $query_promo);

// Fallback jika belum ada yang diset promo
if (!$result_promo || mysqli_num_rows($result_promo) === 0) {
    $result_promo = mysqli_query($koneksi, "SELECT p.*, k.nama_kategori, k.slug AS kat_slug FROM produk p JOIN kategori k ON p.id_kategori = k.id_kategori WHERE p.status = 'aktif' ORDER BY p.harga ASC LIMIT 4");
}

// 4. Ambil Produk Terbaru
$query_terbaru = "SELECT p.*, k.nama_kategori, k.slug AS kat_slug 
                  FROM produk p 
                  JOIN kategori k ON p.id_kategori = k.id_kategori 
                  WHERE p.status = 'aktif' 
                  ORDER BY p.id_produk DESC LIMIT 12";
$result_terbaru = mysqli_query($koneksi, $query_terbaru);

// Kategori untuk tab filter Produk Terbaru Beranda
$q_kat_terbaru = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY 
  CASE 
    WHEN slug = 'original-gurih' THEN 1
    WHEN slug = 'peyek-kacang' THEN 2
    WHEN slug = 'peyek-udang' THEN 3
    WHEN slug = 'peyek-kacang-kedelai' THEN 4
    ELSE 5
  END, id_kategori ASC");
?>

<!-- 1. HERO SECTION -->
<section class="hero-section">
  <div class="container">
    <div class="hero-grid">
      <div class="hero-content scroll-animate animate-slide-left">
        <div class="hero-tag">
          <i class="fa-solid fa-sparkles text-warning"></i>
          <span>100% Renyah, Gurih & Terpercaya</span>
        </div>
        
        <h1 class="hero-title">
          Teman Ngemil Paling Asyik, Bikin Hari Penuh <span>Bahagia!</span>
        </h1>
        
        <p class="hero-desc">
          ZAFANA SNACK menghadirkan aneka camilan gurih, basreng pedas daun jeruk, keripik singkong renyah, dan cookies cokelat lumer. Nikmati sensasi kriuk istimewa di setiap gigitan!
        </p>

        <div class="hero-cta-group">
          <a href="<?= BASE_URL ?>katalog.php" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-bag-shopping"></i> Belanja Sekarang
          </a>
          <a href="https://wa.me/<?= WA_STORE_NUMBER ?>?text=<?= urlencode('Halo Admin Zafana Snack, saya ingin tanya promo camilan hari ini!') ?>" target="_blank" class="btn btn-outline btn-lg">
            <i class="fa-brands fa-whatsapp text-success"></i> Chat WhatsApp
          </a>
        </div>

        <div class="hero-features-list">
          <div class="hero-feature-item">
            <i class="fa-solid fa-circle-check"></i>
            <span>Bahan Alami & Higienis</span>
          </div>
          <div class="hero-feature-item">
            <i class="fa-solid fa-truck-fast"></i>
            <span>Kirim Seluruh Indonesia</span>
          </div>
          <div class="hero-feature-item">
            <i class="fa-solid fa-star"></i>
            <span>Rating 4.9 dari 1.000+ Pembeli</span>
          </div>
        </div>
      </div>

      <!-- Hero Featured Showcase Card -->
      <div class="hero-image-wrapper scroll-animate animate-slide-right">
        <div class="hero-card-featured">
          <div class="hero-card-img-box">
            <span class="badge-top-selling">
              <i class="fa-solid fa-fire"></i> Best Seller Hari Ini
            </span>
            <img src="<?= get_product_image_url('basreng_pedas.jpg') ?>" 
                 alt="Basreng Pedas Daun Jeruk Zafana"
                 loading="lazy"
                 onerror="this.onerror=null; this.src='<?= BASE_URL ?>assets/images/products/default_snack.jpg';">
          </div>
          <div class="hero-card-body">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
              <span class="badge-tag tag-category">Makaroni & Basreng</span>
              <div class="stars">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <strong style="color: var(--navy); font-size: 12px; margin-left: 4px;">5.0</strong>
              </div>
            </div>
            <h3 style="font-size: 18px; font-weight: 800; color: var(--navy); margin-bottom: 4px;">
              Basreng Pedas Daun Jeruk
            </h3>
            <p style="font-size: 13px; color: var(--navy-light); margin-bottom: 15px;">
              Bakso ikan tenggiri goreng renyah dengan cabai rawit pedas & wangi daun jeruk segar.
            </p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <div>
                <span style="font-size: 13px; color: #EF4444; text-decoration: line-through; display: block;">Rp 20.000</span>
                <span style="font-size: 20px; font-weight: 900; color: var(--primary-blue);">Rp 17.000</span>
              </div>
              <button type="button" class="btn btn-primary btn-sm btn-quick-add" data-id="3">
                <i class="fa-solid fa-cart-plus"></i> Tambah
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 2. USP TICKER SECTION -->
<section class="usp-ticker-section scroll-animate animate-fade-in">
  <div class="container">
    <div class="usp-ticker-grid">
      <div class="usp-ticker-card scroll-animate animate-fade-up">
        <div class="usp-icon-wrap"><i class="fa-solid fa-cookie"></i></div>
        <div>
          <h4>Kerenyahan Terjamin</h4>
          <p>Kemasan zipper pouch kedap udara</p>
        </div>
      </div>
      <div class="usp-ticker-card scroll-animate animate-fade-up">
        <div class="usp-icon-wrap"><i class="fa-solid fa-shield-halved"></i></div>
        <div>
          <h4>100% Halal & Bersih</h4>
          <p>Tanpa bahan kimia pengawet berbahaya</p>
        </div>
      </div>
      <div class="usp-ticker-card scroll-animate animate-fade-up">
        <div class="usp-icon-wrap"><i class="fa-solid fa-box-open"></i></div>
        <div>
          <h4>Packing Ekstra Aman</h4>
          <p>Bubble wrap & kardus tebal gratis</p>
        </div>
      </div>
      <div class="usp-ticker-card scroll-animate animate-fade-up">
        <div class="usp-icon-wrap"><i class="fa-solid fa-wallet"></i></div>
        <div>
          <h4>Bisa COD & Transfer</h4>
          <p>BCA, Mandiri, BNI, dan QRIS</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 3. KATEGORI SNACK SECTION -->
<section class="category-section">
  <div class="container">
    <div class="section-title scroll-animate animate-fade-up">
      <span class="section-tag"><i class="fa-solid fa-layer-group"></i> Aneka Rasa Pilihan</span>
      <h2>Kategori Camilan Favorit</h2>
      <p>Pilih rasa yang paling pas untuk teman nonton, nongkrong, atau belajar</p>
    </div>

    <div class="category-grid">
      <?php if ($result_kategori && mysqli_num_rows($result_kategori) > 0): ?>
        <?php while ($kat = mysqli_fetch_assoc($result_kategori)): 
          $ikon = !empty($kat['ikon']) ? $kat['ikon'] : 'fa-cookie';
        ?>
          <a href="<?= BASE_URL ?>katalog.php?kategori=<?= urlencode($kat['slug']) ?>" class="category-card scroll-animate animate-zoom-in">
            <div class="category-icon-box">
              <i class="fa-solid <?= htmlspecialchars($ikon) ?>"></i>
            </div>
            <div class="category-name"><?= htmlspecialchars($kat['nama_kategori']) ?></div>
            <span class="category-count"><?= (int)$kat['total_produk'] ?> varian snack</span>
          </a>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- 4. PRODUK TERLARIS (BEST SELLER) -->
<section style="padding: 60px 0; background-color: var(--soft-gray);">
  <div class="container">
    <div class="scroll-animate animate-fade-up" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 35px; flex-wrap: wrap; gap: 15px;">
      <div>
        <span class="section-tag"><i class="fa-solid fa-fire text-danger"></i> Paling Banyak Dicari</span>
        <h2 style="font-size: 28px; font-weight: 800; color: var(--navy); margin-top: 4px;">🔥 Produk Terlaris</h2>
        <p style="color: var(--navy-light); font-size: 14px; margin: 0;">Snack paling diminati pelanggan ZAFANA SNACK dengan pesanan tertinggi.</p>
      </div>
      <a href="<?= BASE_URL ?>katalog.php?sort=bestseller" class="btn btn-outline btn-sm">
        Lihat Semua <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>

    <div class="product-grid">
      <?php if ($result_bestseller && mysqli_num_rows($result_bestseller) > 0): ?>
        <?php while ($p = mysqli_fetch_assoc($result_bestseller)): 
          $foto_url = get_product_image_url($p['foto'] ?? '');
          $harga = (int)($p['harga'] ?? 0);
          $harga_coret = (int)($p['harga_coret'] ?? 0);
          $terjual = (int)($p['terjual'] ?? 0);
          $rating = isset($p['rating']) && (float)$p['rating'] > 0 ? (float)$p['rating'] : 0;
          $berat = (int)($p['berat_gram'] ?? 150);
          $stok = (int)($p['stok'] ?? 0);
        ?>
          <div class="product-card scroll-animate animate-fade-up" data-title="<?= htmlspecialchars($p['nama_produk'] ?? '') ?>" data-category="<?= htmlspecialchars($p['nama_kategori'] ?? '') ?>">
            <div class="badge-row">
              <span class="badge-tag tag-category"><?= htmlspecialchars($p['nama_kategori'] ?? 'Snack') ?></span>
              <span class="badge-tag tag-bestseller"><i class="fa-solid fa-fire"></i> Best Seller</span>
            </div>

            <div class="product-thumb">
              <a href="<?= BASE_URL ?>detail_produk.php?id=<?= (int)$p['id_produk'] ?>">
                <img src="<?= $foto_url ?>" 
                     alt="<?= htmlspecialchars($p['nama_produk'] ?? 'Camilan') ?>"
                     loading="lazy"
                     onerror="this.onerror=null; this.src='<?= BASE_URL ?>assets/images/products/default_snack.jpg';">
              </a>
            </div>

            <div class="product-body">
              <?php if ($rating > 0 || $terjual > 0): ?>
                <div class="product-rating-row">
                  <?php if ($rating > 0): ?>
                    <div class="stars">
                      <i class="fa-solid fa-star"></i>
                      <strong style="color: var(--navy);"><?= number_format($rating, 1) ?></strong>
                    </div>
                  <?php endif; ?>
                  <?php if ($rating > 0 && $terjual > 0): ?>
                    <span>•</span>
                  <?php endif; ?>
                  <?php if ($terjual > 0): ?>
                    <span>Terjual <?= $terjual ?> pcs</span>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <h3 class="product-title">
                <a href="<?= BASE_URL ?>detail_produk.php?id=<?= (int)$p['id_produk'] ?>">
                  <?= htmlspecialchars($p['nama_produk'] ?? '') ?>
                </a>
              </h3>

              <div class="product-meta-row">
                <span><i class="fa-solid fa-weight-scale"></i> <?= $berat ?> gr</span>
                <span>•</span>
                <span>Stok: <?= $stok ?></span>
              </div>

              <div class="product-footer-row" style="margin-top: 10px; display: flex; justify-content: space-between; align-items: baseline;">
                <div class="product-price-box">
                  <?php if ($harga_coret > $harga): ?>
                    <span class="price-strike"><?= rupiah($harga_coret) ?></span>
                  <?php endif; ?>
                  <span class="price-current"><?= rupiah($harga) ?></span>
                </div>
              </div>

              <div class="product-card-btns" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 12px;">
                <a href="<?= BASE_URL ?>detail_produk.php?id=<?= (int)$p['id_produk'] ?>" class="btn btn-sm btn-outline" style="font-size: 12px; padding: 7px 8px; text-align: center; justify-content: center; border-radius: var(--radius-sm); font-weight: 700;">
                  <i class="fa-regular fa-eye"></i> Detail
                </a>
                <button type="button" class="btn btn-sm btn-primary btn-quick-add" data-id="<?= (int)$p['id_produk'] ?>" style="font-size: 12px; padding: 7px 8px; justify-content: center; border-radius: var(--radius-sm); font-weight: 700;" title="Tambah ke Keranjang">
                  <i class="fa-solid fa-cart-plus"></i> +Keranjang
                </button>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px; background: var(--soft-gray); border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
          <div style="font-size: 42px; color: #94A3B8; margin-bottom: 12px;">
            <i class="fa-solid fa-fire text-danger"></i>
          </div>
          <h3 style="font-size: 18px; color: var(--navy); margin-bottom: 6px;">Belum Ada Produk Terlaris</h3>
          <p style="font-size: 14px; color: var(--navy-light); margin: 0;">Pesanan favorit pelanggan akan segera ditampilkan di sini.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- 5. PROMO & DISKON SPESIAL BANNER -->
<section class="container scroll-animate animate-fade-up">
  <div class="promo-section">
    <div class="container">
      <div class="promo-wrapper">
        <div class="promo-content">
          <span class="promo-badge"><i class="fa-solid fa-bolt"></i> Flash Promo Minggu Ini</span>
          <h2>Ngemil Enak Lebih Hemat Hingga 20%!</h2>
          <p>Dapatkan potongan harga spesial untuk varian keripik pisang cokelat, basreng daun jeruk, dan cookies melt pilihan. Buruan sebelum kehabisan stok!</p>
          
          <div class="promo-timer">
            <div class="timer-box">
              <div class="timer-num">02</div>
              <div class="timer-label">Hari</div>
            </div>
            <div class="timer-box">
              <div class="timer-num">14</div>
              <div class="timer-label">Jam</div>
            </div>
            <div class="timer-box">
              <div class="timer-num">45</div>
              <div class="timer-label">Menit</div>
            </div>
            <div class="timer-box">
              <div class="timer-num">18</div>
              <div class="timer-label">Detik</div>
            </div>
          </div>

          <a href="<?= BASE_URL ?>katalog.php?filter=promo" class="btn btn-white btn-lg">
            <i class="fa-solid fa-tags"></i> Ambil Promo Sekarang
          </a>
        </div>

        <div style="text-align: center;">
          <img src="<?= get_product_image_url('keripik_pisang.jpg') ?>" 
               alt="Promo Keripik Pisang Cokelat" 
               style="border-radius: 16px; max-height: 320px; margin: 0 auto; box-shadow: 0 15px 35px rgba(0,0,0,0.3); object-fit: cover;"
               loading="lazy"
               onerror="this.onerror=null; this.src='<?= BASE_URL ?>assets/images/products/default_snack.jpg';">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 6. PRODUK TERBARU -->
<section style="padding: 40px 0 70px; background-color: var(--white);">
  <div class="container">
    <div class="scroll-animate animate-fade-up" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 35px; flex-wrap: wrap; gap: 15px;">
      <div>
        <span class="section-tag"><i class="fa-solid fa-sparkles text-blue"></i> Koleksi Segar</span>
        <h2 style="font-size: 28px; font-weight: 800; color: var(--navy); margin-top: 4px;">✨ Produk Terbaru</h2>
        <p style="color: var(--navy-light); font-size: 14px; margin: 0;">Camilan yang baru saja siap dari dapur produksi ZAFANA SNACK.</p>
      </div>
      <a href="<?= BASE_URL ?>katalog.php" class="btn btn-primary btn-sm">
        Lihat Seluruh Produk <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>

    <!-- Filter Kategori Cepat di Produk Terbaru Beranda -->
    <div class="scroll-animate animate-fade-up" style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 25px;">
      <button type="button" class="btn btn-sm btn-primary home-kat-btn active" data-slug="all" style="border-radius: var(--radius-full); font-weight: 700; padding: 6px 14px;">
        <i class="fa-solid fa-border-all"></i> Semua Terbaru
      </button>
      <?php while ($kt = mysqli_fetch_assoc($q_kat_terbaru)): 
        $is_main = in_array($kt['slug'], ['original-gurih', 'peyek-kacang', 'peyek-udang', 'peyek-kacang-kedelai']);
      ?>
        <button type="button" class="btn btn-sm btn-light home-kat-btn" data-slug="<?= htmlspecialchars($kt['slug']) ?>" style="border-radius: var(--radius-full); font-weight: 700; padding: 6px 14px; <?= $is_main ? 'border: 1px solid #BFDBFE;' : '' ?>">
          <i class="fa-solid <?= htmlspecialchars($kt['ikon'] ?? 'fa-cookie') ?>"></i> <?= htmlspecialchars($kt['nama_kategori']) ?>
        </button>
      <?php endwhile; ?>
    </div>

    <div class="product-grid" id="homeLatestGrid">
      <?php if ($result_terbaru && mysqli_num_rows($result_terbaru) > 0): ?>
        <?php while ($p = mysqli_fetch_assoc($result_terbaru)): 
          $foto_url = get_product_image_url($p['foto'] ?? '');
          $harga = (int)($p['harga'] ?? 0);
          $harga_coret = (int)($p['harga_coret'] ?? 0);
          $terjual = (int)($p['terjual'] ?? 0);
          $rating = isset($p['rating']) && (float)$p['rating'] > 0 ? (float)$p['rating'] : 0;
          $berat = (int)($p['berat_gram'] ?? 150);
          $stok = (int)($p['stok'] ?? 0);
        ?>
          <div class="product-card scroll-animate animate-fade-up" data-title="<?= htmlspecialchars($p['nama_produk'] ?? '') ?>" data-category="<?= htmlspecialchars($p['nama_kategori'] ?? '') ?>" data-category-slug="<?= htmlspecialchars($p['kat_slug'] ?? '') ?>">
            <div class="badge-row">
              <span class="badge-tag tag-category"><?= htmlspecialchars($p['nama_kategori'] ?? 'Snack') ?></span>
              <span class="badge-tag tag-new"><i class="fa-solid fa-sparkles"></i> Baru</span>
              <?php if (!empty($p['is_best_seller'])): ?>
                <span class="badge-tag tag-bestseller"><i class="fa-solid fa-fire"></i> Laris</span>
              <?php endif; ?>
              <?php if ($harga_coret > $harga): ?>
                <?php $hemat_persen = round((($harga_coret - $harga) / $harga_coret) * 100); ?>
                <span class="badge-tag tag-discount">-<?= $hemat_persen ?>%</span>
              <?php endif; ?>
            </div>

            <div class="product-thumb">
              <a href="<?= BASE_URL ?>detail_produk.php?id=<?= (int)$p['id_produk'] ?>">
                <img src="<?= $foto_url ?>" 
                     alt="<?= htmlspecialchars($p['nama_produk'] ?? 'Camilan') ?>"
                     loading="lazy"
                     onerror="this.onerror=null; this.src='<?= BASE_URL ?>assets/images/products/default_snack.jpg';">
              </a>
            </div>

            <div class="product-body">
              <?php if ($rating > 0 || $terjual > 0): ?>
                <div class="product-rating-row">
                  <?php if ($rating > 0): ?>
                    <div class="stars">
                      <i class="fa-solid fa-star"></i>
                      <strong style="color: var(--navy);"><?= number_format($rating, 1) ?></strong>
                    </div>
                  <?php endif; ?>
                  <?php if ($rating > 0 && $terjual > 0): ?>
                    <span>•</span>
                  <?php endif; ?>
                  <?php if ($terjual > 0): ?>
                    <span>Terjual <?= $terjual ?> pcs</span>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <h3 class="product-title">
                <a href="<?= BASE_URL ?>detail_produk.php?id=<?= (int)$p['id_produk'] ?>">
                  <?= htmlspecialchars($p['nama_produk'] ?? '') ?>
                </a>
              </h3>

              <div class="product-meta-row">
                <span><i class="fa-solid fa-weight-scale"></i> <?= $berat ?> gr</span>
                <span>•</span>
                <span>Stok: <?= $stok ?></span>
              </div>

              <div class="product-footer-row" style="margin-top: 10px; display: flex; justify-content: space-between; align-items: baseline;">
                <div class="product-price-box">
                  <?php if ($harga_coret > $harga): ?>
                    <span class="price-strike"><?= rupiah($harga_coret) ?></span>
                  <?php endif; ?>
                  <span class="price-current"><?= rupiah($harga) ?></span>
                </div>
              </div>

              <div class="product-card-btns" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 12px;">
                <a href="<?= BASE_URL ?>detail_produk.php?id=<?= (int)$p['id_produk'] ?>" class="btn btn-sm btn-outline" style="font-size: 12px; padding: 7px 8px; text-align: center; justify-content: center; border-radius: var(--radius-sm); font-weight: 700;">
                  <i class="fa-regular fa-eye"></i> Detail
                </a>
                <button type="button" class="btn btn-sm btn-primary btn-quick-add" data-id="<?= (int)$p['id_produk'] ?>" style="font-size: 12px; padding: 7px 8px; justify-content: center; border-radius: var(--radius-sm); font-weight: 700;" title="Tambah ke Keranjang">
                  <i class="fa-solid fa-cart-plus"></i> +Keranjang
                </button>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px; background: var(--soft-gray); border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
          <div style="font-size: 42px; color: #94A3B8; margin-bottom: 12px;">
            <i class="fa-solid fa-cookie-bite"></i>
          </div>
          <h3 style="font-size: 18px; color: var(--navy); margin-bottom: 6px;">Belum Ada Produk Terbaru</h3>
          <p style="font-size: 14px; color: var(--navy-light); margin: 0;">Produk segar sedang dipersiapkan di dapur produksi ZAFANA SNACK.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Notice jika filter kategori menghasilkan 0 item di Beranda -->
    <div id="homeLatestEmptyNotice" style="display: none; text-align: center; padding: 50px 20px; background: var(--soft-gray); border-radius: var(--radius-md); border: 1px dashed var(--border-color); margin-top: 20px;">
      <i class="fa-solid fa-cookie-bite" style="font-size: 40px; color: #94A3B8; margin-bottom: 10px;"></i>
      <h3 style="font-size: 16px; color: var(--navy); margin-bottom: 4px;">Belum Ada Produk Pada Kategori Ini</h3>
      <p style="font-size: 13px; color: var(--navy-light); margin: 0 auto 15px; max-width: 400px;">Produk untuk kategori ini sedang disiapkan.</p>
      <a href="<?= BASE_URL ?>katalog.php" class="btn btn-sm btn-primary">Lihat Seluruh Katalog</a>
    </div>

    <script>
      document.querySelectorAll('.home-kat-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          document.querySelectorAll('.home-kat-btn').forEach(b => {
            b.classList.remove('btn-primary');
            b.classList.add('btn-light');
          });
          this.classList.remove('btn-light');
          this.classList.add('btn-primary');
          
          const slug = this.getAttribute('data-slug');
          const cards = document.querySelectorAll('#homeLatestGrid .product-card');
          let visibleCount = 0;
          cards.forEach(card => {
            if (slug === 'all' || card.getAttribute('data-category-slug') === slug) {
              card.style.display = '';
              visibleCount++;
            } else {
              card.style.display = 'none';
            }
          });
          
          const emptyNotice = document.getElementById('homeLatestEmptyNotice');
          if (emptyNotice) {
            emptyNotice.style.display = (visibleCount === 0) ? 'block' : 'none';
          }
        });
      });
    </script>
  </div>
</section>

<!-- 7. TESTIMONI PELANGGAN -->
<section class="testimonials-section">
  <div class="container">
    <div class="section-title scroll-animate animate-fade-up">
      <span class="section-tag"><i class="fa-solid fa-comments text-blue"></i> Cerita Pelanggan</span>
      <h2>Apa Kata Pecinta Snack Kami?</h2>
      <p>Ribuan bungkus camilan sudah menemani waktu santai, nugas, dan kumpul keluarga</p>
    </div>

    <div class="testimonials-grid">
      <div class="testi-card scroll-animate animate-fade-up">
        <div class="testi-stars">
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
        </div>
        <p class="testi-text">
          "Basreng pedas daun jeruknya juara banget! Bumbu cabainya nempel tebal dan daun jeruknya wangi semerbak. Tidak keras sama sekali pas digigit. Nagih pol!"
        </p>
        <div class="testi-user">
          <div class="testi-avatar">AM</div>
          <div class="testi-info">
            <h4>Anisa Maharani</h4>
            <span>Mahasiswi, Bandung • <strong class="text-success"><i class="fa-solid fa-circle-check"></i> Pembeli Terverifikasi</strong></span>
          </div>
        </div>
      </div>

      <div class="testi-card scroll-animate animate-fade-up">
        <div class="testi-stars">
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
        </div>
        <p class="testi-text">
          "Keripik pisang cokelat lumernya premium banget rasanya, cokelatnya manisnya pas dan keripiknya renyah krispi. Packing aman sampai rumah pakai bubble wrap tebal."
        </p>
        <div class="testi-user">
          <div class="testi-avatar" style="background: #DCFCE7; color: #16A34A;">RP</div>
          <div class="testi-info">
            <h4>Rian Pratama</h4>
            <span>Karyawan Swasta, Jakarta • <strong class="text-success"><i class="fa-solid fa-circle-check"></i> Pembeli Terverifikasi</strong></span>
          </div>
        </div>
      </div>

      <div class="testi-card scroll-animate animate-fade-up">
        <div class="testi-stars">
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
          <i class="fa-solid fa-star"></i>
        </div>
        <p class="testi-text">
          "Pesan buat stok camilan di kantor. Makaroni spiral dan kacang bawangnya cepat habis diserbu teman-teman kantor. Admin ramah dan proses konfirmasi via WhatsApp cepat sekali!"
        </p>
        <div class="testi-user">
          <div class="testi-avatar" style="background: #FEF3C7; color: #D97706;">DW</div>
          <div class="testi-info">
            <h4>Dina Wulandari</h4>
            <span>Wiraswasta, Surabaya • <strong class="text-success"><i class="fa-solid fa-circle-check"></i> Pembeli Terverifikasi</strong></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
