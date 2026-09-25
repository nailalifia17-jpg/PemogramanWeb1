# SIM Kas RT 6 - Jobsheet 8

Versi ini mempertahankan alur dan tampilan SIM Kas RT 6, tetapi penyimpanan data sudah dipindahkan dari session PHP ke PostgreSQL menggunakan PDO prepared statement.

## Persiapan PostgreSQL

1. Pastikan service PostgreSQL berjalan di Laragon/Windows.
2. Aktifkan ekstensi `pdo_pgsql` pada `php.ini` PHP yang digunakan Laragon, lalu restart server.
3. Buat database:

```bash
createdb -U postgres sim_kas_rt6
```

4. Jalankan skema dari folder proyek:

```bash
psql -U postgres -d sim_kas_rt6 -f sql/01_schema.sql
```

Konfigurasi default aplikasi adalah host `127.0.0.1`, port `5432`, database `sim_kas_rt6`, user `postgres`, dan password `postgres`. Nilai ini dapat diganti melalui environment variable `SIMKAS_DB_HOST`, `SIMKAS_DB_PORT`, `SIMKAS_DB_NAME`, `SIMKAS_DB_USER`, dan `SIMKAS_DB_PASS`.

Jika password PostgreSQL kamu berbeda, jalankan aplikasi dengan password tersebut. Contoh PowerShell:

```powershell
$env:SIMKAS_DB_PASS = "password-postgresql-kamu"
php -S localhost:8087
```

Untuk membuat database memakai password yang sama:

```powershell
$env:PGPASSWORD = "password-postgresql-kamu"
createdb -U postgres sim_kas_rt6
psql -U postgres -d sim_kas_rt6 -f sql/01_schema.sql
```

Jangan memasukkan password asli ke dalam file PHP atau Git.

## Menjalankan aplikasi

```bash
php -S localhost:8087
```

Buka `http://localhost:8087`.
