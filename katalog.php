<?php
$page_title = "Katalog Camilan & Snack Pilihan - ZAFANA SNACK";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Filter Parameter
$kategori_slug = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : '';
$filter_mode = isset($_GET['filter']) ? trim($_GET['filter']) : '';

// 1. Ambil Semua Kategori dengan memprioritaskan 4 Kategori Utama
$query_kategori = "SELECT * FROM kategori ORDER BY 
  CASE 
    WHEN slug = 'original-gurih' THEN 1
    WHEN slug = 'peyek-kacang' THEN 2
    WHEN slug = 'peyek-udang' THEN 3
    WHEN slug = 'peyek-kacang-kedelai' THEN 4
    ELSE 5
  END, id_kategori ASC";
$result_kategori = mysqli_query($koneksi, $query_kategori);

// Ambil nama kategori aktif jika ada
$current_kat_name = '';
if (!empty($kategori_slug)) {
    $q_cur = mysqli_query($koneksi, "SELECT nama_kategori FROM kategori WHERE slug = '" . mysqli_real_escape_string($koneksi, $kategori_slug) . "'");
    if ($q_cur && $d_cur = mysqli_fetch_assoc($q_cur)) {
        $current_kat_name = $d_cur['nama_kategori'];
    }
}

// 2. Susun Query Produk
$sql = "SELECT p.*, k.nama_kategori, k.slug AS kat_slug 
        FROM produk p 
        JOIN kategori k ON p.id_kategori = k.id_kategori 
        WHERE p.status = 'aktif'";

$types = "";
$params = [];

if (!empty($kategori_slug)) {
    $sql .= " AND k.slug = ?";
    $types .= "s";
    $params[] = $kategori_slug;
}

if ($filter_mode === 'promo') {
    $sql .= " AND (p.is_promo = 1 OR p.harga_coret > p.harga)";
} elseif ($filter_mode === 'bestseller') {
    $sql .= " AND p.is_best_seller = 1";
}

if (!empty($cari)) {
    $sql .= " AND (p.nama_produk LIKE ? OR p.deskripsi LIKE ?)";
    $types .= "ss";
    $params[] = "%$cari%";
    $params[] = "%$cari%";
}

// Sorting SQL fallback
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY p.harga ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.harga DESC";
        break;
    case 'bestseller':
        $sql .= " ORDER BY p.terjual DESC";
        break;
    case 'rating':
        $sql .= " ORDER BY p.rating DESC";
        break;
    default:
        $sql .= " ORDER BY p.id_produk DESC";
        break;
}

$stmt = mysqli_prepare($koneksi, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result_produk = mysqli_stmt_get_result($stmt);
$total_ditemukan = mysqli_num_rows($result_produk);
?>

<!-- Header Banner Katalog -->
<div style="background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%); padding: 45px 0 30px; border-bottom: 1px solid var(--border-color);">
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
      <div>
        <span class="section-tag"><i class="fa-solid fa-cookie-bite text-blue"></i> Pilihan Lengkap</span>
        <h1 style="font-size: 30px; color: var(--navy); font-weight: 800; margin-top: 4px;">Katalog Camilan Renyah</h1>
        <p style="color: var(--navy-light); font-size: 15px; margin: 0;">Temukan snack lezat teman santai, nugas, atau kumpul bersama.</p>
      </div>

      <!-- Live Search Box Form (Mendukung kombinasi filter kategori & pencarian) -->
      <form action="<?= BASE_URL ?>katalog.php" method="GET" style="width: 100%; max-width: 440px; display: flex; gap: 8px;">
        <?php if (!empty($kategori_slug)): ?>
          <input type="hidden" name="kategori" value="<?= htmlspecialchars($kategori_slug) ?>">
        <?php endif; ?>
        <?php if (!empty($sort)): ?>
          <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
        <?php endif; ?>
        <div style="position: relative; flex: 1;">
          <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--navy-light);"></i>
          <input type="text" name="cari" id="liveSearchInput" class="form-control" style="padding-left: 44px; border-radius: var(--radius-full); box-shadow: var(--shadow-sm);" placeholder="Cari nama camilan pilihan..." value="<?= htmlspecialchars($cari) ?>">
        </div>
        <button type="submit" class="btn btn-primary" style="border-radius: var(--radius-full); padding: 8px 18px; font-weight: 700;">
          Cari
        </button>
      </form>
    </div>
  </div>
</div>

<div class="container" style="padding: 35px 20px 80px;">
  <!-- Baris Filter Kategori & Sort Control -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 18px; margin-bottom: 20px; padding-bottom: 18px; border-bottom: 1px solid var(--border-color);">
    
    <!-- Tombol Filter Kategori Utama & Pilihan Semua Produk -->
    <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
      <a href="<?= BASE_URL ?>katalog.php<?= !empty($cari) ? '?cari=' . urlencode($cari) : '' ?>" 
         class="btn btn-sm <?= (empty($kategori_slug) && empty($filter_mode)) ? 'btn-primary' : 'btn-light' ?>" 
         style="border-radius: var(--radius-full); font-weight: 700; padding: 7px 15px;">
        <i class="fa-solid fa-border-all"></i> Semua Produk
      </a>
      
      <?php while ($kat = mysqli_fetch_assoc($result_kategori)): 
        $url_kat = BASE_URL . 'katalog.php?kategori=' . urlencode($kat['slug']);
        if (!empty($cari)) $url_kat .= '&cari=' . urlencode($cari);
        if (!empty($sort)) $url_kat .= '&sort=' . urlencode($sort);
        $is_active = ($kategori_slug === $kat['slug']);
        $is_main_kat = in_array($kat['slug'], ['original-gurih', 'peyek-kacang', 'peyek-udang', 'peyek-kacang-kedelai']);
      ?>
        <a href="<?= $url_kat ?>" 
           class="btn btn-sm <?= $is_active ? 'btn-primary' : 'btn-light' ?>" 
           style="border-radius: var(--radius-full); font-weight: 700; padding: 7px 15px; <?= $is_main_kat ? 'border: 1px solid #BFDBFE;' : '' ?>">
          <i class="fa-solid <?= htmlspecialchars($kat['ikon'] ?? 'fa-cookie') ?>"></i>
          <?= htmlspecialchars($kat['nama_kategori']) ?>
        </a>
      <?php endwhile; ?>

      <a href="<?= BASE_URL ?>katalog.php?filter=promo" class="btn btn-sm <?= ($filter_mode === 'promo') ? 'btn-primary' : 'btn-light' ?>" style="border-radius: var(--radius-full); color: #DC2626 !important; font-weight: 700; padding: 7px 15px;">
        <i class="fa-solid fa-bolt"></i> Promo Spesial
      </a>
    </div>

    <!-- Dropdown Sorting -->
    <div style="display: flex; align-items: center; gap: 10px;">
      <label for="sortSelect" style="font-size: 13px; font-weight: 700; color: var(--navy-light); white-space: nowrap;">Urutkan:</label>
      <select id="sortSelect" class="form-control" style="width: auto; padding: 6px 14px; font-size: 13px; font-weight: 600;">
        <option value="default" <?= ($sort == '' || $sort == 'default') ? 'selected' : '' ?>>Paling Sesuai</option>
        <option value="bestseller" <?= ($sort == 'bestseller') ? 'selected' : '' ?>>Paling Laris</option>
        <option value="price_asc" <?= ($sort == 'price_asc') ? 'selected' : '' ?>>Harga: Rendah ke Tinggi</option>
        <option value="price_desc" <?= ($sort == 'price_desc') ? 'selected' : '' ?>>Harga: Tinggi ke Rendah</option>
        <option value="rating" <?= ($sort == 'rating') ? 'selected' : '' ?>>Rating Tertinggi</option>
      </select>
    </div>
  </div>

  <!-- Ringkasan Filter & Pencarian Aktif -->
  <?php if (!empty($kategori_slug) || !empty($cari) || !empty($filter_mode)): ?>
    <div style="margin-bottom: 24px; padding: 12px 20px; background: #EFF6FF; border-radius: var(--radius-sm); border: 1px solid #BFDBFE; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; font-size: 13px;">
      <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <?php if (!empty($kategori_slug)): ?>
          <span style="display: inline-flex; align-items: center; gap: 6px; background: #fff; padding: 4px 12px; border-radius: 20px; border: 1px solid #93C5FD; color: var(--navy);">
            <i class="fa-solid fa-tag text-blue"></i> Kategori: <strong style="color: var(--primary-blue);"><?= htmlspecialchars($current_kat_name ?: $kategori_slug) ?></strong>
          </span>
        <?php endif; ?>
        <?php if (!empty($cari)): ?>
          <span style="display: inline-flex; align-items: center; gap: 6px; background: #fff; padding: 4px 12px; border-radius: 20px; border: 1px solid #93C5FD; color: var(--navy);">
            <i class="fa-solid fa-magnifying-glass text-blue"></i> Kata Kunci: <strong>"<?= htmlspecialchars($cari) ?>"</strong>
          </span>
        <?php endif; ?>
        <?php if ($filter_mode === 'promo'): ?>
          <span style="display: inline-flex; align-items: center; gap: 6px; background: #FEE2E2; color: #DC2626; padding: 4px 12px; border-radius: 20px;">
            <i class="fa-solid fa-bolt"></i> Promo Spesial
          </span>
        <?php endif; ?>
        <span style="color: var(--navy-light); font-weight: 600;">(<?= $total_ditemukan ?> camilan ditemukan)</span>
      </div>
      <a href="<?= BASE_URL ?>katalog.php" class="btn btn-sm btn-outline" style="border-color: #93C5FD; color: var(--primary-blue); font-size: 12px; padding: 5px 12px; background: #fff;">
        <i class="fa-solid fa-rotate-left"></i> Reset Filter
      </a>
    </div>
  <?php endif; ?>

  <!-- Grid Produk -->
  <div class="product-grid" id="catalogProductGrid">
    <?php if ($total_ditemukan > 0): ?>
      <?php while ($p = mysqli_fetch_assoc($result_produk)): 
        $foto_url = get_product_image_url($p['foto'] ?? '');
        $harga = (int)($p['harga'] ?? 0);
        $harga_coret = (int)($p['harga_coret'] ?? 0);
        $is_best_seller = !empty($p['is_best_seller']);
        $terjual = (int)($p['terjual'] ?? 0);
        $rating = isset($p['rating']) && (float)$p['rating'] > 0 ? (float)$p['rating'] : 0;
        $berat = (int)($p['berat_gram'] ?? 150);
        $stok = (int)($p['stok'] ?? 0);
      ?>
        <div class="product-card scroll-animate animate-fade-up" 
             data-title="<?= htmlspecialchars($p['nama_produk'] ?? '') ?>" 
             data-category="<?= htmlspecialchars($p['nama_kategori'] ?? '') ?>"
             data-price="<?= $harga ?>"
             data-sold="<?= $terjual ?>"
             data-rating="<?= $rating ?>">
          
          <div class="badge-row">
            <span class="badge-tag tag-category"><?= htmlspecialchars($p['nama_kategori'] ?? 'Snack') ?></span>
            <?php if ($harga_coret > $harga): ?>
              <?php $hemat_persen = round((($harga_coret - $harga) / $harga_coret) * 100); ?>
              <span class="badge-tag tag-discount">-<?= $hemat_persen ?>%</span>
            <?php elseif ($is_best_seller): ?>
              <span class="badge-tag tag-bestseller"><i class="fa-solid fa-fire"></i> Laris</span>
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
    <?php endif; ?>
  </div>

  <!-- Box Ketika Produk Tidak Ditemukan -->
  <div id="noResultBox" style="display: <?= ($total_ditemukan == 0) ? 'block' : 'none' ?>; text-align: center; padding: 70px 20px; background: var(--white); border-radius: 16px; border: 1px dashed var(--border-color); max-width: 550px; margin: 40px auto 0;">
    <i class="fa-solid fa-cookie-bite" style="font-size: 50px; color: var(--light-blue-hover); margin-bottom: 16px;"></i>
    <h3 style="color: var(--navy); margin-bottom: 8px;">Camilan Tidak Ditemukan</h3>
    <p style="color: var(--navy-light); margin-bottom: 20px;">Coba gunakan kata kunci pencarian yang lain atau jelajahi kategori snack kami.</p>
    <a href="<?= BASE_URL ?>katalog.php" class="btn btn-primary btn-sm">Reset Pencarian</a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
