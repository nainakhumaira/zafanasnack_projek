<?php
require_once __DIR__ . '/config/koneksi.php';

// Inisialisasi keranjang di session jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 1. TAMBAH KE KERANJANG
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $id_produk = (int)($_POST['id_produk'] ?? 0);
    $jumlah = max(1, (int)($_POST['jumlah'] ?? 1));

    // Ambil data produk dari database
    $stmt = mysqli_prepare($koneksi, "SELECT id_produk, nama_produk, harga, stok, foto, berat_gram FROM produk WHERE id_produk = ? AND status = 'aktif'");
    mysqli_stmt_bind_param($stmt, "i", $id_produk);
    mysqli_stmt_execute($stmt);
    $p = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($p) {
        if ($p['stok'] <= 0) {
            set_flash('danger', 'Maaf, stok produk tersebut sudah habis.');
        } else {
            // Cek apakah produk sudah ada di keranjang
            if (isset($_SESSION['cart'][$id_produk])) {
                $new_qty = $_SESSION['cart'][$id_produk]['qty'] + $jumlah;
                if ($new_qty > $p['stok']) {
                    $new_qty = $p['stok'];
                    set_flash('warning', 'Jumlah disesuaikan dengan sisa stok yang tersedia (' . $p['stok'] . ' bungkus).');
                } else {
                    set_flash('success', 'Jumlah camilan berhasil diperbarui di keranjang.');
                }
                $_SESSION['cart'][$id_produk]['qty'] = $new_qty;
            } else {
                $qty = min($jumlah, $p['stok']);
                $_SESSION['cart'][$id_produk] = [
                    'id_produk' => $p['id_produk'],
                    'nama_produk' => $p['nama_produk'],
                    'harga' => $p['harga'],
                    'berat_gram' => $p['berat_gram'],
                    'foto' => $p['foto'],
                    'qty' => $qty,
                    'stok' => $p['stok']
                ];
                set_flash('success', 'Produk berhasil dimasukkan ke keranjang belanja!');
            }
        }
    }
    header("Location: " . BASE_URL . "keranjang.php");
    exit;
}

// 2. UPDATE JUMLAH ITEM
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    if (isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $id => $qty) {
            $id = (int)$id;
            $qty = (int)$qty;
            if (isset($_SESSION['cart'][$id])) {
                if ($qty <= 0) {
                    unset($_SESSION['cart'][$id]);
                } else {
                    // Cek batasan stok
                    $max = $_SESSION['cart'][$id]['stok'];
                    $_SESSION['cart'][$id]['qty'] = min($qty, $max);
                }
            }
        }
        set_flash('success', 'Keranjang belanja berhasil diperbarui.');
    }
    header("Location: " . BASE_URL . "keranjang.php");
    exit;
}

// 3. HAPUS SATU ITEM
if (isset($_GET['action']) && $_GET['action'] === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
        set_flash('success', 'Produk dihapus dari keranjang.');
    }
    header("Location: " . BASE_URL . "keranjang.php");
    exit;
}

// 4. KOSONGKAN KERANJANG
if (isset($_GET['action']) && $_GET['action'] === 'kosongkan') {
    $_SESSION['cart'] = [];
    set_flash('info', 'Keranjang belanja dikosongkan.');
    header("Location: " . BASE_URL . "keranjang.php");
    exit;
}

$page_title = "Keranjang Belanja";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$total_belanja = 0;
$total_berat = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_belanja += ($item['harga'] * $item['qty']);
    $total_berat += ($item['berat_gram'] * $item['qty']);
}
?>

<div style="background-color: var(--bg-soft); padding: 30px 0; border-bottom: 1px solid var(--border-color);">
  <div class="container">
    <h1 style="font-size: 26px; color: var(--navy); font-weight: 800; margin-bottom: 4px;">Keranjang Belanja</h1>
    <p style="color: var(--navy-light);">Periksa kembali pesanan camilan favoritmu sebelum melakukan checkout.</p>
  </div>
</div>

<div class="container" style="padding: 40px 20px 80px;">
  <?= get_flash() ?>

  <?php if (!empty($_SESSION['cart'])): ?>
    <form action="<?= BASE_URL ?>keranjang.php" method="POST">
      <input type="hidden" name="action" value="update">

      <div class="cart-layout-grid scroll-animate animate-fade-up">
        
        <!-- Tabel Item Keranjang -->
        <div class="card" style="padding: 0; overflow: hidden;">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th style="width: 80px;">Produk</th>
                  <th>Nama Camilan</th>
                  <th>Harga</th>
                  <th style="width: 120px; text-align: center;">Jumlah</th>
                  <th>Subtotal</th>
                  <th style="text-align: center; width: 50px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['cart'] as $id => $item): 
                  $subtotal = $item['harga'] * $item['qty'];
                ?>
                  <tr>
                    <td>
                      <img src="<?= get_product_image_url($item['foto'] ?? '') ?>" 
                           alt="<?= htmlspecialchars($item['nama_produk']) ?>" 
                           style="width: 60px; height: 60px; object-fit: cover; background: var(--bg-soft); border-radius: 8px; border: 1px solid var(--border-color);"
                           loading="lazy"
                           onerror="this.onerror=null; this.src='<?= BASE_URL ?>assets/images/products/default_snack.jpg';">
                    </td>
                    <td>
                      <a href="<?= BASE_URL ?>detail_produk.php?id=<?= $id ?>" style="font-weight: 700; color: var(--navy);">
                        <?= htmlspecialchars($item['nama_produk']) ?>
                      </a>
                      <div style="font-size: 12px; color: var(--navy-light);">Berat: <?= $item['berat_gram'] ?> gr/pcs</div>
                    </td>
                    <td style="font-weight: 600;"><?= rupiah($item['harga']) ?></td>
                    <td style="text-align: center;">
                      <input type="number" name="qty[<?= $id ?>]" value="<?= $item['qty'] ?>" min="1" max="<?= $item['stok'] ?>" 
                             style="width: 65px; text-align: center; padding: 6px; border: 1px solid var(--border-color); border-radius: 6px; font-weight: 700;">
                    </td>
                    <td style="font-weight: 800; color: var(--primary-blue);"><?= rupiah($subtotal) ?></td>
                    <td style="text-align: center;">
                      <a href="<?= BASE_URL ?>keranjang.php?action=hapus&id=<?= $id ?>" class="btn-confirm-delete" style="color: #EF4444;" title="Hapus Item">
                        <i class="fa-solid fa-trash-can"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div style="padding: 16px 20px; background-color: var(--bg-soft); display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color);">
            <a href="<?= BASE_URL ?>keranjang.php?action=kosongkan" class="btn btn-sm btn-outline btn-confirm-delete" style="border-color: #EF4444; color: #EF4444 !important;">
              <i class="fa-solid fa-trash"></i> Kosongkan Keranjang
            </a>
            <button type="submit" class="btn btn-sm btn-light">
              <i class="fa-solid fa-rotate"></i> Update Keranjang
            </button>
          </div>
        </div>

        <!-- Ringkasan Belanja -->
        <div class="card">
          <div class="card-header">
            <h3>Ringkasan Belanja</h3>
          </div>

          <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
            <span style="color: var(--navy-light);">Total Item:</span>
            <span style="font-weight: 700; color: var(--navy);"><?= total_cart_items() ?> bungkus</span>
          </div>

          <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px;">
            <span style="color: var(--navy-light);">Total Berat Paket:</span>
            <span style="font-weight: 700; color: var(--navy);"><?= number_format($total_berat / 1000, 2) ?> kg</span>
          </div>

          <div style="display: flex; justify-content: space-between; margin-bottom: 20px; padding-top: 14px; border-top: 1px dashed var(--border-color); font-size: 16px;">
            <span style="font-weight: 700; color: var(--navy);">Total Harga:</span>
            <span style="font-size: 20px; font-weight: 800; color: var(--primary-blue);"><?= rupiah($total_belanja) ?></span>
          </div>

          <a href="<?= BASE_URL ?>checkout.php" class="btn btn-primary btn-block btn-lg" style="margin-bottom: 12px;">
            <i class="fa-solid fa-credit-card"></i> Lanjut ke Checkout
          </a>

          <a href="<?= BASE_URL ?>katalog.php" class="btn btn-outline btn-block btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Tambah Camilan Lain
          </a>
        </div>

      </div>
    </form>
  <?php else: ?>
    <div style="text-align: center; padding: 70px 20px; background: var(--white); border-radius: 16px; border: 1px dashed var(--border-color); max-width: 600px; margin: 0 auto;">
      <i class="fa-solid fa-cart-arrow-down" style="font-size: 50px; color: var(--primary-blue); margin-bottom: 16px;"></i>
      <h2 style="font-size: 22px; color: var(--navy); margin-bottom: 8px;">Keranjang Belanjamu Masih Kosong</h2>
      <p style="color: var(--navy-light); margin-bottom: 24px;">Yuk, pilih camilan renyah dan gurih favoritmu di katalog Zafana Snack!</p>
      <a href="<?= BASE_URL ?>katalog.php" class="btn btn-primary btn-lg">
        <i class="fa-solid fa-cookie"></i> Belanja Camilan Sekarang
      </a>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
