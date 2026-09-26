# SIM Kas Yayasan RQ - Jobsheet 9

Versi ini melanjutkan Jobsheet 8 tanpa mengubah alur dan tampilan SIM Kas Yayasan RQ. Penyimpanan data menggunakan PostgreSQL dan sekarang sudah mendukung CRUD penuh: tambah, tampil, edit, dan hapus.

## Fitur Jobsheet 9

- Edit dan hapus data warga.
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

## Struktur CRUD

```text
warga/list.php                         # Read, pencarian, pagination
warga/edit.php                         # Form Update
transaksi/list.php                     # Read, filter, pencarian, pagination
transaksi/edit.php                     # Form Update
proses/proses_edit_warga.php           # UPDATE warga
proses/proses_edit_transaksi.php       # UPDATE transaksi
proses/hapus_warga.php                 # DELETE warga via POST
proses/hapus_transaksi.php             # DELETE transaksi via POST
```


