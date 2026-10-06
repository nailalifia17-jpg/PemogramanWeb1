# Checklist Audit Keamanan Jobsheet 11

| Area audit | Kondisi dan perlindungan |
| --- | --- |
| SQL injection | Query memakai PDO prepared statement; parameter input tidak digabungkan ke SQL. |
| XSS | Output HTML dari data, session, dan parameter URL memakai helper `e()` dengan `htmlspecialchars()`, `ENT_QUOTES`, dan UTF-8. |
| CSRF | Semua form POST mengirim token acak per sesi; endpoint memeriksanya dengan `hash_equals()` sebelum menjalankan perubahan data. |
| Validasi input | Server memvalidasi ID, rentang bulan/tahun, jumlah positif, role, status anggota, dan pilihan kategori/metode. |
| Session fixation | Login berhasil memanggil `session_regenerate_id(true)` sebelum menyimpan identitas pengguna ke session. |
| Urutan guard | Endpoint perubahan data memanggil autentikasi dan otorisasi sebelum memeriksa token CSRF. |

## Verifikasi manual

1. Kirim POST tanpa `_csrf_token` ke endpoint perubahan data saat sudah login. Respons harus `403` dan data tidak berubah.
2. Simpan teks `<script>alert(1)</script>` pada kolom keterangan, lalu buka daftar transaksi. Teks harus tampil sebagai teks dan tidak menjalankan script.
3. Kirim POST tanpa login ke endpoint yang dilindungi. Browser harus diarahkan ke halaman login sebelum pemeriksaan CSRF.
4. Coba username `' OR '1'='1` pada form login. Login harus gagal dengan pesan kredensial tidak cocok.

Form pencarian menggunakan GET karena hanya membaca data; operasi yang mengubah data tetap memakai POST dan token CSRF.