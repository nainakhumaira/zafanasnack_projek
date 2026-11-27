<?php
$page_title = "Hubungi Kami - ZAFANA SNACK";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $subjek = trim($_POST['subjek'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    if (empty($nama) || empty($email) || empty($no_hp) || empty($pesan)) {
        $error_msg = 'Mohon lengkapi seluruh kolom yang bertanda bintang (*) !';
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO kontak_pesan (nama, email, no_hp, subjek, pesan) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssss", $nama, $email, $no_hp, $subjek, $pesan);

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = 'Terima kasih Kak ' . htmlspecialchars($nama) . '! Pesan Anda telah terkirim ke tim Zafana Snack. Kami akan segera menghubungi Anda kembali.';
        } else {
            $error_msg = 'Terjadi kesalahan sistem saat mengirim pesan. Silakan hubungi kami via WhatsApp.';
        }
    }
}
?>

<!-- Header Banner -->
<div style="background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%); padding: 50px 0 35px; border-bottom: 1px solid var(--border-color); text-align: center;">
  <div class="container">
    <span class="section-tag"><i class="fa-solid fa-headset text-blue"></i> Selalu Siap Membantu</span>
    <h1 style="font-size: 34px; color: var(--navy); font-weight: 900; margin-top: 6px;">Hubungi ZAFANA SNACK</h1>
    <p style="color: var(--navy-light); font-size: 16px; max-width: 650px; margin: 8px auto 0;">
      Punya pertanyaan seputar rasa, pesanan khusus acara, atau peluang kemitraan reseller? Kami siap melayani Anda.
    </p>
  </div>
</div>

<div class="container" style="padding: 50px 20px 90px;">
  <div class="contact-layout-grid">
    
    <!-- Kolom Kiri: Informasi Kontak & WhatsApp -->
    <div class="scroll-animate animate-slide-left">
      <div class="card" style="margin-bottom: 25px; border-left: 4px solid var(--primary-blue);">
        <h3 style="font-size: 18px; color: var(--navy); margin-bottom: 16px;">
          <i class="fa-solid fa-address-book text-blue"></i> Kontak Layanan Pelanggan
        </h3>

        <ul style="list-style: none; display: flex; flex-direction: column; gap: 18px;">
          <li style="display: flex; gap: 14px; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: var(--radius-sm); background: #DCFCE7; color: #16A34A; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
              <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
              <strong style="color: var(--navy); font-size: 14px;">WhatsApp Order & CS</strong>
              <div style="color: var(--navy-light); font-size: 13px;"><?= STORE_PHONE ?> (Respon Cepat)</div>
              <a href="https://wa.me/<?= WA_STORE_NUMBER ?>?text=<?= urlencode('Halo Admin Zafana Snack, saya ingin berkonsultasi seputar pemesanan.') ?>" target="_blank" style="font-size: 12px; font-weight: 700; color: #16A34A; display: inline-block; margin-top: 4px;">
                Chat Langsung Sekarang &raquo;
              </a>
            </div>
          </li>

          <li style="display: flex; gap: 14px; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: var(--radius-sm); background: var(--light-blue); color: var(--primary-blue); display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
              <i class="fa-solid fa-envelope"></i>
            </div>
            <div>
              <strong style="color: var(--navy); font-size: 14px;">Email Resmi</strong>
              <div style="color: var(--navy-light); font-size: 13px;">order@zafanasnack.com</div>
              <div style="color: var(--navy-light); font-size: 13px;">kerjasama@zafanasnack.com</div>
            </div>
          </li>

          <li style="display: flex; gap: 14px; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: var(--radius-sm); background: var(--soft-gray); color: var(--navy); display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
              <i class="fa-solid fa-location-dot"></i>
            </div>
            <div>
              <strong style="color: var(--navy); font-size: 14px;">Dapur & Workshop</strong>
              <div style="color: var(--navy-light); font-size: 13px; line-height: 1.5;">
                <?= STORE_ADDRESS ?>
              </div>
            </div>
          </li>

          <li style="display: flex; gap: 14px; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: var(--radius-sm); background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
              <i class="fa-solid fa-clock"></i>
            </div>
            <div>
              <strong style="color: var(--navy); font-size: 14px;">Jam Operasional</strong>
              <div style="color: var(--navy-light); font-size: 13px;">Senin - Sabtu: 08.00 - 21.00 WIB</div>
              <div style="color: var(--navy-light); font-size: 12px; color: #16A34A;">(Order online 24 jam tetap dilayani)</div>
            </div>
          </li>
        </ul>
      </div>

      <!-- Kotak Direct WhatsApp Card -->
      <div style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: #fff; padding: 25px; border-radius: var(--radius-md); box-shadow: var(--shadow-md);">
        <h4 style="font-size: 18px; color: #fff; margin-bottom: 6px;"><i class="fa-brands fa-whatsapp"></i> Mau Respon Cepat?</h4>
        <p style="font-size: 13px; color: #E6FFFA; line-height: 1.6; margin-bottom: 16px;">
          Ingin pesan partai besar, tanya diskon reseller, atau butuh bantuan order langsung? Hubungi admin via WhatsApp sekarang.
        </p>
        <a href="https://wa.me/<?= WA_STORE_NUMBER ?>?text=<?= urlencode('Halo Admin Zafana Snack, saya ingin memesan snack sekarang.') ?>" target="_blank" class="btn btn-white btn-block" style="color: #065F46 !important; font-weight: 700;">
          <i class="fa-brands fa-whatsapp"></i> Buka Chat WhatsApp
        </a>
      </div>
    </div>

    <!-- Kolom Kanan: Formulir Kirim Pesan -->
    <div class="card scroll-animate animate-slide-right">
      <div class="card-header">
        <h3><i class="fa-solid fa-paper-plane text-blue"></i> Kirim Pesan Online</h3>
      </div>

      <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success"><?= $success_msg ?></div>
      <?php endif; ?>

      <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger"><?= $error_msg ?></div>
      <?php endif; ?>

      <form action="<?= BASE_URL ?>kontak.php" method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
          <div class="form-group">
            <label class="form-label">Nama Anda *</label>
            <input type="text" name="nama" class="form-control" placeholder="Nama lengkap" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
          </div>

          <div class="form-group">
            <label class="form-label">Email Anda *</label>
            <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
          <div class="form-group">
            <label class="form-label">Nomor WhatsApp *</label>
            <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>" required>
          </div>

          <div class="form-group">
            <label class="form-label">Subjek Pesan</label>
            <input type="text" name="subjek" class="form-control" placeholder="Contoh: Pemesanan Reseller / Info Rasa" value="<?= htmlspecialchars($_POST['subjek'] ?? '') ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Pesan Anda *</label>
          <textarea name="pesan" class="form-control" rows="5" placeholder="Tuliskan pertanyaan, saran, atau permintaan Anda secara lengkap..." required><?= htmlspecialchars($_POST['pesan'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block">
          <i class="fa-solid fa-paper-plane"></i> Kirim Pesan Sekarang
        </button>
      </form>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
