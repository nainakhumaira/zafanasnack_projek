<?php
$page_title = "Tentang Kami - ZAFANA SNACK";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!-- Header Banner -->
<div style="background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%); padding: 50px 0 35px; border-bottom: 1px solid var(--border-color); text-align: center;">
  <div class="container">
    <span class="section-tag"><i class="fa-solid fa-cookie-bite text-blue"></i> Kisah Kami</span>
    <h1 style="font-size: 34px; color: var(--navy); font-weight: 900; margin-top: 6px;">Tentang ZAFANA SNACK</h1>
    <p style="color: var(--navy-light); font-size: 16px; max-width: 650px; margin: 8px auto 0;">
      Berawal dari cinta terhadap camilan Nusantara, kami hadir untuk membuat setiap momen santai Anda lebih istimewa.
    </p>
  </div>
</div>

<div class="container" style="padding: 60px 20px 100px;">
  <!-- Grid Cerita & Gambar -->
  <div class="about-hero-grid">
    <div class="scroll-animate animate-slide-left">
      <span style="color: var(--primary-blue); font-weight: 800; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">Filosofi Kami</span>
      <h2 style="font-size: 30px; font-weight: 800; color: var(--navy); margin: 8px 0 18px; line-height: 1.25;">
        "Teman Ngemil, <span style="color: var(--primary-blue);">Teman Bahagia!"</span>
      </h2>
      <p style="color: var(--navy); line-height: 1.8; margin-bottom: 16px; font-size: 15px;">
        <strong>ZAFANA SNACK</strong> didirikan dengan satu misi sederhana: menghadirkan camilan khas Indonesia yang dibuat dari bahan-bahan segar berkualitas terbaik, bumbu rempah asli yang melimpah, dan proses pengolahan yang higienis.
      </p>
      <p style="color: var(--navy-light); line-height: 1.8; font-size: 14px;">
        Kami percaya bahwa setiap gigitan camilan renyah mampu mencairkan suasana, meredakan lelah saat nugas atau bekerja, serta mempererat kehangatan saat berkumpul bersama sahabat dan keluarga tercinta.
      </p>
      
      <div style="display: flex; gap: 30px; margin-top: 30px; flex-wrap: wrap;">
        <div>
          <div style="font-size: 32px; font-weight: 900; color: var(--primary-blue); font-family: var(--font-heading);">10.000+</div>
          <span style="font-size: 13px; color: var(--navy-light);">Bungkus Terjual</span>
        </div>
        <div>
          <div style="font-size: 32px; font-weight: 900; color: var(--primary-blue); font-family: var(--font-heading);">4.9 / 5.0</div>
          <span style="font-size: 13px; color: var(--navy-light);">Kepuasan Pembeli</span>
        </div>
        <div>
          <div style="font-size: 32px; font-weight: 900; color: var(--primary-blue); font-family: var(--font-heading);">100%</div>
          <span style="font-size: 13px; color: var(--navy-light);">Bahan Alami</span>
        </div>
      </div>
    </div>

    <div class="scroll-animate animate-slide-right">
      <div style="border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg); border: 1px solid var(--border-color); height: 400px;">
        <img src="<?= BASE_URL ?>assets/images/products/keripik_singkong.jpg" alt="Dapur Zafana Snack" style="width: 100%; height: 100%; object-fit: cover;">
      </div>
    </div>
  </div>

  <!-- 3 Nilai Utama -->
  <div class="section-title scroll-animate animate-fade-up">
    <span class="section-tag"><i class="fa-solid fa-award"></i> Komitmen Kami</span>
    <h2>Mengapa Memilih Zafana Snack?</h2>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
    <div class="card scroll-animate animate-fade-up" style="text-align: center; border-radius: var(--radius-md);">
      <div style="width: 60px; height: 60px; background: var(--light-blue); color: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 18px;">
        <i class="fa-solid fa-pepper-hot"></i>
      </div>
      <h3 style="font-size: 18px; margin-bottom: 10px; color: var(--navy);">Cabai & Bumbu Alami</h3>
      <p style="color: var(--navy-light); font-size: 14px; line-height: 1.6;">
        Kami menggunakan cabai rawit merah asli dan daun jeruk segar tanpa pewarna sintetis atau perisa buatan berlebihan.
      </p>
    </div>

    <div class="card scroll-animate animate-fade-up" style="text-align: center; border-radius: var(--radius-md);">
      <div style="width: 60px; height: 60px; background: var(--light-blue); color: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 18px;">
        <i class="fa-solid fa-box-archive"></i>
      </div>
      <h3 style="font-size: 18px; margin-bottom: 10px; color: var(--navy);">Kemasan Zipper Tebal</h3>
      <p style="color: var(--navy-light); font-size: 14px; line-height: 1.6;">
        Setiap produk dibungkus dengan standing pouch berkualitas tinggi yang dapat dibuka-tutup kembali agar kerenyahan tetap terjaga berbulan-bulan.
      </p>
    </div>

    <div class="card scroll-animate animate-fade-up" style="text-align: center; border-radius: var(--radius-md);">
      <div style="width: 60px; height: 60px; background: var(--light-blue); color: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 18px;">
        <i class="fa-solid fa-handshake-angle"></i>
      </div>
      <h3 style="font-size: 18px; margin-bottom: 10px; color: var(--navy);">Harga Bersahabat</h3>
      <p style="color: var(--navy-light); font-size: 14px; line-height: 1.6;">
        Menyajikan cita rasa kuliner bintang lima dengan harga terjangkau yang ramah di kantong pelajar, mahasiswa, dan keluarga.
      </p>
    </div>
  </div>

  <!-- Info Workshop Resmi -->
  <div class="card scroll-animate animate-fade-up" style="margin-top: 40px; padding: 25px 30px; background: var(--soft-gray); border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
    <div style="display: flex; align-items: center; gap: 16px;">
      <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--primary-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
        <i class="fa-solid fa-location-dot"></i>
      </div>
      <div>
        <h4 style="font-size: 16px; font-weight: 800; color: var(--navy); margin-bottom: 2px;">Workshop & Dapur Produksi Resmi</h4>
        <p style="margin: 0; font-size: 14px; color: var(--navy-light);">
          <strong>ZAFANA SNACK</strong> &bull; <?= STORE_ADDRESS ?> &bull; Telp/WA: <strong><?= STORE_PHONE ?></strong>
        </p>
      </div>
    </div>
    <a href="https://wa.me/<?= WA_STORE_NUMBER ?>?text=<?= urlencode('Halo Admin Zafana Snack, saya ingin tahu lebih banyak tentang produk snack.') ?>" target="_blank" class="btn btn-primary btn-sm">
      <i class="fa-brands fa-whatsapp"></i> Hubungi Kami
    </a>
  </div>

  <div class="scroll-animate animate-zoom-in" style="text-align: center; margin-top: 50px;">
    <a href="<?= BASE_URL ?>katalog.php" class="btn btn-primary btn-lg">
      <i class="fa-solid fa-cookie-bite"></i> Coba Camilan Kami Sekarang
    </a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
