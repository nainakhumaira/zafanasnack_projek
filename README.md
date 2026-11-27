# ZAFANA SNACK - Toko Online Makanan Ringan
> **Tagline:** *"Teman Ngemil, Teman Bahagia!"*

Website toko online modern, elegan, profesional, dan interaktif untuk usaha nyata **ZAFANA SNACK** sekaligus proyek tugas kuliah berbasis **PHP Native, MySQL, HTML5, CSS3, dan JavaScript (AJAX)**.

---

## 🎨 Identitas & Skema Desain
* **Biru Utama (`#2563EB`)**: Navbar, tombol aksi utama (CTA), ikon aktif, dan link.
* **Biru Muda (`#DBEAFE`)**: Badge kategori, progress bar bebas ongkir, dan hover menu.
* **Putih (`#FFFFFF`)**: Latar belakang utama body dan kartu produk.
* **Navy (`#1E293B`)**: Teks judul, nama produk, dan harga (tajam, kontras & elegan).
* **Abu-abu Lembut (`#F1F5F9`)**: Latar kartu fitur, tabel zebra, dan panel samping.
* **Tipografi**: `Poppins` (Headings & Brand) & `Inter` (Body Text) dari Google Fonts.

---

## 🌟 Fitur Baru & Unggulan Hasil Redesign Total

### 1. Header & Navbar Modern:
* **Tinggi Ringkas & Sticky**: Navbar tetap menempel (*sticky*) di bagian atas layar saat di-scroll dengan bayangan (*box-shadow*) halus.
* **Layout 3 Kolom Seimbang**: Logo ZAFANA SNACK di sebelah kiri, menu utama di tengah (Beranda, Produk, Tentang Kami, Kontak), dan aksi pengguna di sebelah kanan (keranjang belanja & profil).
* **Skema Putih & Biru**: Background putih bersih dengan aksen biru `#2563EB` dan teks navy `#1E293B`.
* **Top Bar Minimalis**: Informasi kontak WhatsApp dan jaminan 100% Halal dibuat lebih kecil dan rapi di atas navbar.

### 2. Slide-out Drawer Keranjang Belanja:
* **Tersembunyi secara Default**: Panel keranjang tidak lagi memakan ruang halaman secara permanen.
* **Animasi Muncul dari Sisi Kanan**: Drawer meluncur mulus saat ikon keranjang atau tombol *Tambah ke Keranjang* ditekan.
* **Backdrop & Multi-Cara Tutup**: Dilengkapi overlay transparan; dapat ditutup dengan tombol (X), klik di luar panel, atau menekan tombol `Escape`.
* **AJAX & Perhitungan Otomatis**: Update kuantitas (+/-) dan penghapusan item langsung menghitung subtotal dan total harga secara instan.
* **Ilustrasi State Kosong**: Menampilkan grafis keranjang kosong dan tombol ajakan jelajah katalog ketika keranjang belum terisi.

### 3. Dropdown Informasi Akun Ringkas:
* Menggantikan tampilan administrator/pengguna yang sebelumnya terlalu besar.
* Menampilkan dropdown elegan saat ikon profil ditekan, berisi:
  - *Profil Saya*
  - *Riwayat Pesanan*
  - *Dashboard Admin* (khusus akun administrator)
  - *Keluar*

### 4. Animasi Scroll (IntersectionObserver):
* Menggunakan JavaScript `IntersectionObserver` ringan (tanpa library berat eksternal).
* Mendukung animasi: **Fade In**, **Fade Up**, **Slide In Left**, **Slide In Right**, dan **Zoom In** berdurasi 500–800 ms.
* **Stagger Animation**: Kartu produk muncul bergantian secara bertahap saat digulir.
* **Aksesibilitas Penuh**: Mendukung media query `@media (prefers-reduced-motion: reduce)` bagi pengunjung yang sensitif terhadap pergerakan animasi.

### 5. Tampilan Kartu Produk Premium:
* **Foto Makanan Riil & Besar**: 8 varian camilan menggunakan foto berkualitas tinggi dengan `object-fit: cover`.
* **Efek Hover Halus**: Gambar sedikit membesar (`scale(1.08)`) dan kartu terangkat ke atas dengan bayangan lembut.
* **Label Diskon & Rating**: Persentase hemat harga coret dan rating bintang ditampilkan rapi.
* **Tombol Cepat Tambah ke Keranjang**: Terintegrasi langsung dengan Drawer Cart via AJAX.

### 6. Desain 100% Responsif:
* **Laptop / Desktop**: Navbar horizontal, grid produk multi-kolom yang luas.
* **Tablet**: Grid katalog dan formulir otomatis menyesuaikan 2-3 kolom.
* **Smartphone (HP)**: Menu navigasi hamburger yang fleksibel, drawer keranjang responsif layar penuh, tanpa *horizontal scrolling*.

### 7. Foto Produk Makanan Sungguhan (High Resolution):
Seluruh 8 camilan dilengkapi foto makanan riil berkualitas tinggi dalam folder `assets/images/products/`:
1. `keripik_pisang.jpg` — Keripik Pisang Cokelat Lumer
2. `keripik_singkong.jpg` — Keripik Singkong Renyah Original
3. `basreng_pedas.jpg` — Basreng Pedas Daun Jeruk
4. `makaroni_pedas.jpg` — Makaroni Spiral Super Pedas
5. `keripik_kaca.jpg` — Keripik Kaca Pedas Beling
6. `kacang_bawang.jpg` — Kacang Bawang Renyah Gurih
7. `cookies.jpg` — Choco Melt Butter Cookies
8. `kerupuk_seblak.jpg` — Kerupuk Seblak Pedas Bantet

### 3. Halaman Pelanggan Lengkap:
* **Beranda (`index.php`)**: Hero banner besar, USP ticker, kategori camilan, section **Produk Terlaris**, section **Flash Promo Diskon** dengan hitung mundur waktu, **Produk Terbaru**, dan **Testimoni Pelanggan Terverifikasi**.
* **Katalog (`katalog.php`)**: Filter kategori interaktif, live search, dan sorting dinamis.
* **Detail Produk (`detail_produk.php`)**: Foto cover, badge diskon %, rating, sisa stok, kuantitas order, tombol WhatsApp, dan rekomendasi produk terkait.
* **Keranjang Belanja (`keranjang.php`)**: Review pesanan tradisional lengkap.
* **Checkout (`checkout.php`)**: Formulir data penerima, alamat tujuan, metode pembayaran, dan pembuatan kode pesanan otomatis.
* **Sukses Pesanan (`order_sukses.php`)**: Bukti order + tombol otomatis kirim pesan WhatsApp ke nomor penjual.
* **Riwayat Pesanan (`riwayat_pesanan.php`)**: Pantau status pesanan (*Menunggu Bayar, Diproses, Dikirim, Selesai*).
* **Profil Pelanggan (`profil.php`)**: Kelola identitas pembeli, alamat pengiriman default, riwayat belanja, dan ganti kata sandi.
* **Tentang Kami (`tentang.php`)**: Cerita pendirian toko, komitmen higienis & halal, serta filosofi "Teman Ngemil, Teman Bahagia!".
* **Kontak (`kontak.php`)**: Formulir kirim pesan online (tersimpan di database `kontak_pesan`), kontak WhatsApp, jam operasional, dan lokasi workshop.
* **Autentikasi (`login.php` & `register.php`)**: Pendaftaran dan login akun dengan enkripsi `password_hash()`.

### 4. Panel Dashboard Administrator Modern (`/admin`):
* **Dashboard Metrik (`admin/index.php`)**: Ringkasan omset penjualan valid, total pesanan, varian snack aktif, dan jumlah member pelanggan.
* **Grafik Penjualan Interaktif**: Ditenagai oleh **Chart.js** yang memetakan tren pendapatan harian selama 7 hari terakhir secara dinamis dari database.
* **Peringatan Stok Kritis**: Notifikasi cepat untuk camilan yang memiliki stok di bawah 10 bungkus.
* **Kelola Produk (`admin/produk.php`, `produk_tambah.php`, `produk_edit.php`)**: Tambah varian snack, upload foto produk dengan live preview, tentukan harga coret diskon, badge Best Seller, dan status aktif/nonaktif.
* **Kelola Kategori (`admin/kategori.php`)**: Pengelompokan jenis snack beserta ikon visualnya.
* **Kelola Stok Cepat (`admin/stok.php`)**: Update kuantitas stok camilan dalam hitungan detik.
* **Kelola Pesanan (`admin/pesanan.php` & `pesanan_detail.php`)**: Pemrosesan pesanan, ubah status pengiriman, cetak nota transaksi, dan tombol langsung hubungi pembeli via WhatsApp.
* **Data Pelanggan (`admin/pelanggan.php`)**: Rekap member terdaftar beserta total frekuensi order dan total belanja mereka.
* **Laporan Penjualan (`admin/laporan.php`)**: Rekap omset berdasarkan rentang tanggal kalender, rata-rata nilai transaksi, dan tombol cetak/PDF resmi untuk laporan tugas kuliah.

---

## 📂 Struktur Berkas Proyek
```text
zafanasnack/
├── api/
│   └── cart.php              # Endpoint REST/AJAX Keranjang Belanja
├── assets/
│   ├── css/
│   │   ├── style.css         # Styling modern Biru & Putih (Poppins + Inter)
│   │   └── admin.css         # Styling panel admin
│   ├── js/
│   │   └── main.js           # AJAX Drawer Cart, Live Search, Sort, Toast
│   └── images/
│       ├── banners/          # Banner promosi
│       ├── logo/             # Logo SVG Zafana Snack
│       └── products/         # 8 Foto makanan riil beresolusi tinggi (JPG)
├── config/
│   ├── koneksi.php           # Koneksi MySQLi
│   └── helper.php            # Format rupiah, session login, nomor WA
├── includes/
│   ├── header.php            # Header HTML & font
│   ├── navbar.php            # Navbar biru dengan menu & cart badge
│   ├── cart_drawer.php       # Slide-out drawer keranjang belanja
│   ├── footer.php            # Footer lengkap & trust badges
│   └── wa_float.php          # Tombol melayang WhatsApp
├── admin/
│   ├── includes/             # Header (Chart.js), Sidebar, Footer admin
│   ├── index.php             # Dashboard admin + Grafik Penjualan Chart.js
│   ├── produk.php            # Kelola produk snack
│   ├── produk_tambah.php     # Tambah snack baru + upload foto
│   ├── produk_edit.php       # Edit snack + harga diskon
│   ├── kategori.php          # Kelola kategori camilan
│   ├── stok.php              # Pantau & update cepat stok
│   ├── pesanan.php           # Kelola pesanan masuk
│   ├── pesanan_detail.php    # Nota & rincian pesanan
│   ├── pelanggan.php         # Data pelanggan & chat WA
│   ├── laporan.php           # Laporan penjualan & cetak PDF
│   ├── login.php             # Login admin
│   └── logout.php            # Logout admin
├── index.php                 # Halaman Beranda utama
├── katalog.php               # Halaman Katalog Snack (Live Search & Sort)
├── detail_produk.php         # Halaman Rincian Snack & Produk Terkait
├── keranjang.php             # Halaman Keranjang Belanja
├── checkout.php              # Halaman Checkout Pembayaran
├── order_sukses.php          # Halaman Pesanan Berhasil + Konfirmasi WA
├── riwayat_pesanan.php       # Halaman Riwayat Pesanan Pelanggan
├── profil.php                # Halaman Profil Pelanggan
├── tentang.php               # Halaman Tentang Kami
├── kontak.php                # Halaman Kontak & Formulir Pesan
├── login.php                 # Halaman Masuk Pelanggan
├── register.php              # Halaman Daftar Pelanggan
├── logout.php                # Halaman Logout Pelanggan
└── db_zafanasnack.sql        # Database MySQL siap import
```

---

## 🚀 Panduan Menjalankan di XAMPP

1. **Jalankan Apache & MySQL**:
   * Buka **XAMPP Control Panel**.
   * Klik tombol **Start** pada modul **Apache** dan **MySQL**.

2. **Import Database**:
   * Buka browser dan buka: `http://localhost/phpmyadmin`
   * Buat database baru bernama: `db_zafanasnack`
   * Klik tab **Import**, pilih file: `c:\xampp\htdocs\zafanasnack\db_zafanasnack.sql`, lalu klik **Import / Go**.

3. **Buka Website di Browser**:
   * **Toko Online Pelanggan**: `http://localhost/zafanasnack/`
   * **Panel Administrator**: `http://localhost/zafanasnack/admin/`

---

## 🔑 Akun Demo Default
* **Akun Admin**:
  * Email: `admin@zafanasnack.com`
  * Password: `admin123`
* **Akun Pelanggan (Tester)**:
  * Email: `sinta@gmail.com`
  * Password: `pelanggan123`
