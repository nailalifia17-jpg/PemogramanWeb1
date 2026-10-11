# Wireframe — SIM Kas Yayasan RQ

Jobsheet 6 meneruskan tampilan dan alur SIM Kas RQ dari jobsheet sebelumnya. Perubahan utama pada tahap ini adalah data transaksi dan anggota diambil dari berkas JSON dan ditampilkan secara dinamis.

## Beranda

```text
+--------------------------------------------------------------+
| Yayasan Rumah Quran | Beranda | Daftar Transaksi | Laporan | Iuran Anggota |
+--------------------------------------------------------------+
| Selamat Datang di Sistem Informasi Kas Yayasan Rumah Quran   |
| Pencatatan pemasukan dan pengeluaran yayasan                 |
|                                                              |
| Ringkasan Keuangan                                           |
| Total Pemasukan | Total Pengeluaran | Saldo Kas               |
|                                                              |
| Aktivitas Terbaru                                            |
| Jenis | Keterangan dan jumlah | Tanggal                      |
+--------------------------------------------------------------+
```

## Daftar Transaksi

```text
+--------------------------------------------------------------+
| Daftar Transaksi Kas                    [Cari transaksi...]  |
|                                                              |
| Tanggal | Keterangan | Kategori | Jenis | Jumlah | Metode | Aksi |
|         | Data diisi dinamis dari data/transaksi.json         |
+--------------------------------------------------------------+
```

## Daftar Anggota

```text
+--------------------------------------------------------------+
| Daftar Anggota                           [Cari anggota...]   |
|                                                              |
| No. KK | Nama | Alamat | No. HP | Status | Aksi               |
|        | Data diisi dinamis dari data/anggota.json            |
+--------------------------------------------------------------+
```

Navigasi utama Jobsheet 6 menampilkan Beranda, Daftar Transaksi, dan Anggota. Tombol Tambah Transaksi berada di halaman daftar transaksi. Halaman daftar menampilkan indikator loading saat JSON diambil dan pesan error jika permintaan gagal. Tombol Edit membuka form modal; perubahannya disimpan di `localStorage` browser, bukan ke berkas JSON. Tombol Hapus bekerja pada baris yang dibuat dinamis melalui event delegation. Form tambah transaksi dan anggota hanya latihan validasi.
