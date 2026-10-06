# SIM Kas Yayasan RQ - Jobsheet 11

Versi ini melanjutkan Jobsheet 9 tanpa mengubah alur dan tampilan utama SIM Kas Yayasan RQ. Penyimpanan data menggunakan PostgreSQL, CRUD tetap tersedia, dan sekarang ditambahkan autentikasi serta manajemen sesi.

Jobsheet 11 menambahkan helper `e()` untuk escaping output HTML dan token CSRF pada seluruh form POST. Session ID diregenerasi setelah login; query tetap menggunakan prepared statement.

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

Untuk mempertahankan data dari database lama, jalankan `sql/03_migrasi_anggota.sql` sebelum `sql/01_schema.sql`. Database baru tidak memerlukan migrasi tersebut.

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

## Verifikasi Keamanan Jobsheet 11

- POST tanpa `_csrf_token` harus ditolak dengan status HTTP 403.
- Teks `<script>alert(1)</script>` yang disimpan sebagai keterangan transaksi harus tampil sebagai teks, bukan dijalankan.
- Endpoint POST tanpa login harus mengarahkan pengguna ke halaman login sebelum memeriksa token.
- Login dengan username `' OR '1'='1` harus gagal.

Rincian audit tersedia di `docs/security-checklist.md`.

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


