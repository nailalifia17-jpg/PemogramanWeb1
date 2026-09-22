// ===== Menu hamburger (JS-driven, menggantikan checkbox hack) =====
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");
    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}

// ===== Konfirmasi hapus (front-end only, belum ke server) =====
function initHapusConfirm() {
    document.querySelectorAll(".btn-hapus").forEach(function (btn) {
        btn.addEventListener("click", function () {
            const row = btn.closest("tr");
            const cells = row ? row.querySelectorAll("td") : null;
            const label = cells && cells[1] ? cells[1].textContent.trim() : "data ini";
            const yakin = confirm("Yakin ingin menghapus \"" + label + "\"?");
            if (yakin && row) {
                row.remove();
            }
        });
    });
}

// ===== Filter/pencarian tabel real-time =====
function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(".table-responsive table");
    if (!input || !table) return;

    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr");
        rows.forEach(function (row) {
            const teks = row.textContent.toLowerCase();
            row.style.display = teks.includes(keyword) ? "" : "none";
        });
    });
}

// ===== Validasi form (client-side) =====
function tampilkanError(input, pesan) {
    hapusError(input);
    const span = document.createElement("span");
    span.className = "error";
    span.textContent = pesan;
    input.insertAdjacentElement("afterend", span);
}

function hapusError(input) {
    const next = input.nextElementSibling;
    if (next && next.classList.contains("error")) {
        next.remove();
    }
}

function initValidasiForm() {
    const form = document.getElementById("form-tambah");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;

        const tanggal = form.querySelector("[name='tanggal']");
        if (tanggal && tanggal.value.trim() === "") {
            tampilkanError(tanggal, "Tanggal wajib diisi.");
            valid = false;
        } else if (tanggal) {
            hapusError(tanggal);
        }

        const keterangan = form.querySelector("[name='keterangan']");
        if (keterangan && keterangan.value.trim() === "") {
            tampilkanError(keterangan, "Keterangan wajib diisi.");
            valid = false;
        } else if (keterangan) {
            hapusError(keterangan);
        }

        const jumlah = form.querySelector("[name='jumlah']");
        if (jumlah) {
            const nilai = parseInt(jumlah.value, 10);
            if (isNaN(nilai) || nilai < 0) {
                tampilkanError(jumlah, "Jumlah harus angka dan tidak boleh negatif.");
                valid = false;
            } else {
                hapusError(jumlah);
            }
        }

        const nama = form.querySelector("[name='nama']");
        if (nama && nama.value.trim() === "") {
            tampilkanError(nama, "Nama wajib diisi.");
            valid = false;
        } else if (nama) {
            hapusError(nama);
        }

        const noKk = form.querySelector("[name='no_kk']");
        if (noKk && noKk.value.trim() === "") {
            tampilkanError(noKk, "No. KK wajib diisi.");
            valid = false;
        } else if (noKk) {
            hapusError(noKk);
        }

        const alamat = form.querySelector("[name='alamat']");
        if (alamat && alamat.value.trim() === "") {
            tampilkanError(alamat, "Alamat wajib diisi.");
            valid = false;
        } else if (alamat) {
            hapusError(alamat);
        }

        if (!valid) {
            e.preventDefault();
        }
    });
}

// ===== Cetak/download laporan sebagai PDF (pakai fitur print browser) =====
function initCetakLaporan() {
    const btn = document.getElementById("btn-cetak");
    if (!btn) return;

    btn.addEventListener("click", function () {
        window.print();
    });
}

document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
    initCetakLaporan();
});