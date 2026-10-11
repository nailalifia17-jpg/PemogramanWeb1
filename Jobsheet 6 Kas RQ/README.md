# Jobsheet 6 Kas RQ — Fetch API & JSON

Versi sederhana Jobsheet 6 dari aplikasi Yayasan Rumah Quran. Materi Fetch API dan JSON diterapkan pada daftar transaksi dan anggota:

- `data/transaksi.json` dan `data/anggota.json` menjadi sumber data contoh.
- Halaman daftar transaksi dan anggota mengambil data secara asinkron dengan `fetch()` dan `async/await`.
- Isi tabel dibuat dinamis oleh JavaScript. Tabel menampilkan indikator loading, pesan jika data kosong, dan pesan error jika permintaan gagal.
- Tombol hapus pada baris dinamis ditangani menggunakan event delegation. Penghapusan hanya menghilangkan baris dari tampilan saat ini; JSON tidak diubah.
- Tombol edit pada daftar transaksi dan anggota membuka form edit. Perubahannya disimpan di `localStorage` browser dan tetap tampil setelah halaman dimuat ulang; berkas JSON awal tidak diubah.
- Beranda menghitung ringkasan pemasukan, pengeluaran, saldo, dan aktivitas terbaru dari data transaksi, termasuk perubahan transaksi yang tersimpan di browser.
- Navigasi utama menyediakan Beranda, Daftar Transaksi, dan Anggota. Tombol Tambah Transaksi berada di dalam halaman daftar transaksi agar tidak menjadi menu ganda.

## Menjalankan

`fetch()` tidak dapat membaca file JSON dari URL `file://`. Jalankan server lokal dari folder `Jobsheet 6 Kas RQ`, misalnya:

```powershell
php -S localhost:8000
```

Buka `http://localhost:8000`. Halaman yang tersedia: `/transaksi/list.html` dan `/anggota/list.html`.

## Batasan Jobsheet

Form tambah transaksi dan anggota hanya menyediakan validasi di sisi browser. Penambahan ke JSON/server/database belum menjadi bagian Jobsheet 6. Penghapusan tabel hanya menghilangkan baris dari tampilan saat ini. Gunakan proyek ini melalui server lokal agar permintaan `fetch()` ke JSON berhasil.
