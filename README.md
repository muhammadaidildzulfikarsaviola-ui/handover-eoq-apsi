# Handover EOQ APSI

Web sederhana untuk pencatatan barang dan analisis Economic Order Quantity (EOQ), dibuat dengan PHP native dan MySQL.

## Stack
- PHP 8.1+
- MySQL / MariaDB
- HTML, CSS, JavaScript
- Chart.js untuk grafik dashboard

## Menjalankan secara lokal
1. Clone repo ke folder project.
2. Pastikan Apache dan MySQL aktif di XAMPP.
3. Import `database/schema.sql` melalui phpMyAdmin. Database dan tabel dibuat tanpa data contoh.
4. Pastikan pengaturan koneksi di `config/database.php` cocok dengan MySQL lokal.
5. Buka melalui Apache, misalnya `http://localhost/Handover-asdos-apsi/`. Jika folder berada di luar `htdocs`, gunakan konfigurasi VirtualHost Apache.

## Menu yang tersedia
- Dashboard: ringkasan barang, analisis, total biaya, dan grafik.
- Data Barang: tambah, edit, cari, aktifkan, dan nonaktifkan barang.
- Analisis EOQ: input D, S, H; hitung EOQ; simpan hasil ke database.
- Riwayat Analisis: cari analisis dan buka detail perhitungan.
- Laporan: rekap analisis dan ekspor CSV.
- Panduan EOQ: rumus, definisi variabel, dan interpretasi hasil.
- Anggota Kelompok: halaman catatan sederhana untuk handover.

## Rumus
- EOQ = sqrt((2 * D * S) / H)
- Frekuensi order = D / EOQ
- Interval order = 365 / frekuensi order
- Biaya relevan = biaya pemesanan + biaya penyimpanan

D adalah kebutuhan tahunan, S adalah biaya setiap pemesanan, dan H adalah biaya penyimpanan per unit per tahun.

## Catatan
- Dashboard mulai dari nol ketika database belum berisi data.
- Tidak ada seed data atau statistik palsu.
- Kode barang harus unik.
- Barang yang sudah dipakai di analisis dinonaktifkan, bukan dihapus, agar riwayat lama tetap valid.
- Jangan commit kredensial database produksi.
