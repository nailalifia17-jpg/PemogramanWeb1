Latihan Reflektif - Jobsheet 1

1. Kenapa field "Alamat" dan "No. HP" tidak diberi required, sedangkan "Nama" dan "No. Anggota" diberi?
Jawab : Atribut yang wajib diisi harus diberikan pada field yang menjadi identitas utama dari data tersebut."Nama" dan "Nomor" adalah contoh dari field tersebut. "Anggota harus diisi karena berfungsi untuk membedakan satu anggota dengan anggota lainnya di dalam sistem." Sedangkan, "Alamat" dan "No. "HP hanya merupakan data pendukung yang tidak memengaruhi identifikasi anggota, sehingga sistem tetap berjalan dengan normal meskipun keduanya belum diisi."

2. Apa yang akan terjadi (di browser) kalau kamu klik tombol "Simpan" tanpa mengisi field "Nama"? Coba buka filenya di browser dan praktikkan.
Jawab : Saat diuji coba di halaman anggota/tambah.html, form tidak bisa dikirim jika field "Nama" kosong. Browser menampilkan pesan peringatan bawaan seperti "Silakan isi kolom ini" dan secara otomatis mengarahkan kursor ke kolom tersebut. Ini adalah validasi HTML5 native berdasarkan atribut required, yang berjalan sepenuhnya di sisi klien tanpa melibatkan server.

3. Form ini juga belum punya action pada tag <form>-nya — apa dampaknya saat tombol "Simpan" ditekan?
Jawab : Jika semua kolom wajib sudah diisi, form tetap bisa dikirim meski tidak ada tindakan tambahan — secara default, data akan dikirim ke alamat URL halaman tersebut sendiri. Akibatnya, halaman harus direload dan data muncul di address bar dalam bentuk query string (misalnya ?nama=Budi&no_anggota=A003), karena metode default dari form adalah GET. Namun karena belum ada berkas pemroses (PHP), data tidak disimpan secara permanen. Fitur aksi menuju berkas pemroses baru akan berlaku pada jobsheet berikutnya yang membahas pengolahan data di sisi server.