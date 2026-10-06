# SIM Kas Yayasan RQ - Jobsheet 12

Folder ini meneruskan fondasi Jobsheet 11 untuk SIM Kas Yayasan Rumah Quran Mumtazah.

Jobsheet 12 mengadaptasi konsep integrasi transaksi menjadi modul pencatatan iuran anggota. Setiap pembayaran menyimpan satu baris iuran dan satu pemasukan kas secara atomik.

## Fitur Jobsheet 12

- Tabel `iuran` berelasi ke `anggota` dan `transaksi` dengan foreign key.
- Bendahara mencatat pembayaran maksimal satu kali per anggota untuk setiap bulan dan tahun.
- Transaction database memastikan catatan iuran dan pemasukan kas tersimpan atau dibatalkan bersama.
- Daftar iuran menampilkan anggota, periode, tanggal pembayaran, jumlah, dan metode pembayaran.
- Kategori `Iuran Anggota` ikut muncul pada daftar transaksi dan laporan.

## Fitur Jobsheet 10

- Registrasi akun dengan `password_hash()`.
- Login dengan `password_verify()`.
- Logout dengan penghancuran session.
- Navbar menampilkan nama dan peran pengurus saat login.
- Bendahara dapat menambah, mengedit, menghapus data, dan mengunduh laporan.
- Ketua RT hanya dapat melihat data dan mengunduh laporan.
- Semua halaman aplikasi membutuhkan login; halaman login/register tetap publik.
- Tombol Download Excel menghasilkan CSV yang bisa dibuka dengan Microsoft Excel.
- Tombol Download PDF membuka dialog cetak browser; pilih Save as PDF.

## Fitur Jobsheet 9

- Edit dan hapus data anggota.
- Edit dan hapus transaksi kas.
- Hapus hanya melalui form `POST` dengan konfirmasi JavaScript.
- Pencarian server-side menggunakan `ILIKE`.
- Pagination lima baris per halaman.
- Filter transaksi berdasarkan bulan dan tahun tetap tersedia.

## Persiapan PostgreSQL

1. Pastikan service PostgreSQL berjalan di Laragon/Windows.
2. Aktifkan ekstensi `pdo_pgsql` pada `php.ini` PHP yang digunakan Laragon, lalu restart server.
3. Buat database:

```bash
createdb -U postgres sim_kas_yayasan_rq
```

4. Jalankan skema dari folder proyek:

```bash
psql -U postgres -d sim_kas_yayasan_rq -f sql/01_schema.sql
```

5. Jalankan skema users:

```bash
psql -U postgres -d sim_kas_yayasan_rq -f sql/02_users.sql
```

Untuk database yang sudah lama, jalankan `sql/03_migrasi_anggota.sql` sebelum skema anggota. Setelah skema utama dan users siap, buat tabel iuran dengan:

```bash
psql -U postgres -d sim_kas_yayasan_rq -f sql/04_iuran.sql
```

Konfigurasi default aplikasi adalah host `127.0.0.1`, port `5432`, database `sim_kas_yayasan_rq`, user `postgres`, dan password `postgres`. Nilai ini dapat diganti melalui environment variable `SIMKAS_DB_HOST`, `SIMKAS_DB_PORT`, `SIMKAS_DB_NAME`, `SIMKAS_DB_USER`, dan `SIMKAS_DB_PASS`.

Jika password PostgreSQL kamu berbeda, jalankan aplikasi dengan password tersebut. Contoh PowerShell:

```powershell
$env:SIMKAS_DB_PASS = "password-postgresql-kamu"
php -S localhost:8087
```

Untuk membuat database memakai password yang sama:

```powershell
$env:PGPASSWORD = "password-postgresql-kamu"
createdb -U postgres sim_kas_yayasan_rq
psql -U postgres -d sim_kas_yayasan_rq -f sql/01_schema.sql
```

Jangan memasukkan password asli ke dalam file PHP atau Git.

## Menjalankan aplikasi

```bash
php -S localhost:8087
```

Buka `http://localhost:8087`.

Untuk membuat akun, buka menu `Login` lalu pilih `Daftar`. Setelah login, menu tambah/edit/hapus akan dapat digunakan.

Gunakan peran `Bendahara` untuk akun yang mengelola data. Gunakan peran `Ketua RT` untuk akun yang hanya membaca dan mengunduh laporan.

## Uji Modul Iuran

Login sebagai Bendahara, buka **Iuran Anggota**, catat pembayaran untuk anggota aktif, lalu pastikan pembayaran muncul pada daftar iuran dan transaksi pemasukan dengan kategori **Iuran Anggota**. Coba catat ulang anggota dan periode yang sama; permintaan kedua harus ditolak tanpa membuat pemasukan kas duplikat.

Transaksi kas dan iuran tersimpan dalam satu transaction; jalankan `sql/04_iuran.sql` sebelum membuka modul.

## Struktur CRUD

```text
anggota/list.php                         # Read, pencarian, pagination
anggota/edit.php                         # Form Update
transaksi/list.php                     # Read, filter, pencarian, pagination
transaksi/edit.php                     # Form Update
proses/proses_edit_anggota.php           # UPDATE anggota
proses/proses_edit_transaksi.php       # UPDATE transaksi
proses/hapus_anggota.php                 # DELETE anggota via POST
proses/hapus_transaksi.php             # DELETE transaksi via POST
```


