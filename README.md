
# Web Inventori dan Perhitungan EOQ

Ini adalah website sederhana untuk membantu praktikum APSI, terutama untuk mencatat barang dan menghitung kebutuhan pemesanan barang.

Website ini bisa menyimpan data barang, menghitung jumlah pemesanan yang ideal, melihat hasil perhitungan sebelumnya, dan membuat laporan.

## Website ini buat apa?

Bayangkan kita punya gudang yang menyimpan banyak barang. Kita perlu tahu kapan harus memesan barang dan berapa banyak yang sebaiknya dibeli supaya biaya tidak membengkak.

Nah, website ini membantu menghitungnya menggunakan metode EOQ (Economic Order Quantity).

## Apa saja yang ada di website?

- Dashboard: halaman utama yang menampilkan ringkasan data barang, jumlah analisis, biaya, dan grafik.
- Data Barang: tempat untuk menambahkan, mengubah, mencari, dan menonaktifkan data barang.
- Analisis EOQ: tempat memasukkan angka yang dibutuhkan untuk menghitung jumlah pemesanan ideal.
- Riwayat Analisis: tempat melihat hasil perhitungan yang sudah disimpan.
- Laporan: tempat melihat ringkasan hasil analisis dan mengunduhnya dalam bentuk CSV, yang bisa dibuka menggunakan Excel.
- Panduan EOQ: penjelasan rumus dan arti angka yang digunakan.
- Anggota Kelompok: halaman sederhana untuk catatan kelompok.

## Apa yang dibutuhkan?

Untuk menjalankan website ini di laptop, siapkan:

- XAMPP untuk menjalankan website dan database.
- PHP untuk menjalankan program website.
- MySQL untuk menyimpan data.
- Browser seperti Chrome untuk membuka website.

Tidak perlu menginstal semuanya satu per satu jika sudah menggunakan XAMPP dengan komponen yang dibutuhkan.

## Cara menjalankan website

Ikuti langkah berikut secara berurutan.

1. Pastikan XAMPP sudah terpasang di laptop.
2. Masukkan folder project ini ke dalam folder `htdocs` milik XAMPP.
3. Buka XAMPP, lalu nyalakan Apache dan MySQL.
4. Buka browser dan masuk ke `http://localhost/phpmyadmin`.
5. Pilih menu Import, lalu masukkan file `database/schema.sql` dari folder project ini.
6. Jalankan proses import dan tunggu sampai selesai.
7. Buka tab baru di browser dan kunjungi `http://localhost/Handover-asdos-apsi/`.

Kalau nama folder project berbeda, alamat website juga perlu disesuaikan.

## Database-nya sudah ada isinya?

Struktur database sudah disiapkan, tetapi datanya masih kosong.

Artinya, tabel untuk menyimpan kategori barang, data barang, dan hasil analisis sudah tersedia. Kamu tinggal mulai memasukkan data yang diperlukan.

Dashboard akan menampilkan angka nol atau keadaan kosong jika belum ada data. Itu normal, bukan error.

## Cara mulai menggunakan website

1. Buka menu Data Barang.
2. Tambahkan barang yang ingin dicatat.
3. Buka menu Analisis EOQ.
4. Pilih barang dan masukkan angka yang dibutuhkan.
5. Jalankan perhitungan dan simpan hasilnya.
6. Periksa hasilnya di menu Riwayat Analisis atau Laporan.

## EOQ itu apa?

EOQ adalah cara menghitung berapa banyak barang yang sebaiknya dipesan dalam satu kali pemesanan.

Perhitungannya menggunakan tiga informasi utama:

- D: jumlah kebutuhan barang dalam satu tahun.
- S: biaya yang dikeluarkan setiap kali memesan barang.
- H: biaya penyimpanan untuk satu unit barang selama satu tahun.

Website akan menggunakan angka tersebut untuk menghitung jumlah pemesanan ideal dan biaya terkait.

## Kalau website tidak bisa dibuka

Coba periksa beberapa hal ini:

- Apache dan MySQL sudah dinyalakan di XAMPP.
- Folder project berada di dalam `htdocs`.
- File database sudah diimpor melalui phpMyAdmin.
- Nama database dan pengaturan koneksi di `config/database.php` sudah sesuai.
- Alamat website yang dibuka sudah sesuai dengan nama folder project.

Kalau masih bermasalah, catat pesan error yang muncul supaya lebih mudah mencari penyebabnya.

## Catatan untuk penerus

Website ini dibuat supaya bisa dilanjutkan dan dikembangkan oleh orang berikutnya.

Sebelum mengubah program, sebaiknya pahami dulu fungsi setiap menu dan pastikan website masih berjalan setelah perubahan dilakukan.

Kalau ingin menambahkan fitur baru, usahakan perubahan tersebut tidak merusak fitur yang sudah ada.

Selamat melanjutkan project!
