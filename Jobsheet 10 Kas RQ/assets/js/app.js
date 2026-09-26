// ========================================
// HAMBURGER MENU
// ========================================

function initNavToggle() {

    const toggleBtn =
        document.getElementById("nav-toggle-btn");

    const nav =
        document.querySelector("#navbarNav");

    if (!toggleBtn || !nav) {
        return;
    }

    toggleBtn.addEventListener("click", function () {

        const isOpen = nav.classList.toggle("show");
        toggleBtn.setAttribute("aria-expanded", String(isOpen));
        toggleBtn.setAttribute("aria-label", isOpen ? "Tutup menu" : "Buka menu");

    });
}


// ========================================
// KONFIRMASI HAPUS
// ========================================

function initHapusConfirm() {
    document.addEventListener("submit", function (event) {
        const form = event.target.closest(".form-hapus");
        if (!form) return;

        if (!window.confirm("Yakin ingin menghapus data ini?")) {
            event.preventDefault();
        }
    });
}


// ========================================
// PENCARIAN TABEL
// ========================================

function initTableFilter() {

    const input =
        document.getElementById("search-input");

    const table =
        document.querySelector(
            ".table-responsive table"
        );

    if (!input || !table) {
        return;
    }

    input.addEventListener("keyup", function () {

        const keyword =
            input.value.toLowerCase();

        const rows =
            table.querySelectorAll(
                "tbody tr"
            );

        rows.forEach(function (row) {

            const teks =
                row.textContent.toLowerCase();

            row.style.display =
                teks.includes(keyword)
                    ? ""
                    : "none";

        });

    });

}


// ========================================
// VALIDASI FORM
// ========================================

function tampilkanError(input, pesan) {

    hapusError(input);

    const span =
        document.createElement("span");

    span.className = "error";

    span.textContent = pesan;

    input.insertAdjacentElement(
        "afterend",
        span
    );
}


function hapusError(input) {

    const next =
        input.nextElementSibling;

    if (
        next &&
        next.classList.contains("error")
    ) {

        next.remove();

    }
}


function initValidasiForm() {

    const form =
        document.getElementById("form-tambah");

    if (!form) {
        return;
    }


    form.addEventListener(
        "submit",
        function (e) {

            let valid = true;


            // ==============================
            // FORM WARGA
            // ==============================

            const nama =
                form.querySelector(
                    "[name='nama']"
                );

            if (
                nama &&
                nama.value.trim() === ""
            ) {

                tampilkanError(
                    nama,
                    "Nama wajib diisi."
                );

                valid = false;

            }
            else if (nama) {

                hapusError(nama);

            }


            const noKk =
                form.querySelector(
                    "[name='no_kk']"
                );

            if (
                noKk &&
                noKk.value.trim() === ""
            ) {

                tampilkanError(
                    noKk,
                    "No. KK wajib diisi."
                );

                valid = false;

            }
            else if (noKk) {

                hapusError(noKk);

            }


            const alamat =
                form.querySelector(
                    "[name='alamat']"
                );

            if (
                alamat &&
                alamat.value.trim() === ""
            ) {

                tampilkanError(
                    alamat,
                    "Alamat wajib diisi."
                );

                valid = false;

            }
            else if (alamat) {

                hapusError(alamat);

            }


            // ==============================
            // FORM TRANSAKSI
            // ==============================

            const keterangan =
                form.querySelector(
                    "[name='keterangan']"
                );

            if (
                keterangan &&
                keterangan.value.trim() === ""
            ) {

                tampilkanError(
                    keterangan,
                    "Keterangan wajib diisi."
                );

                valid = false;

            }
            else if (keterangan) {

                hapusError(keterangan);

            }


            const jumlah =
                form.querySelector(
                    "[name='jumlah']"
                );

            if (jumlah) {

                const nilai =
                    parseInt(
                        jumlah.value,
                        10
                    );

                if (
                    isNaN(nilai) ||
                    nilai <= 0 || !Number.isInteger(nilai)
                ) {

                    tampilkanError(
                        jumlah,
                        "Jumlah harus berupa bilangan bulat lebih dari 0."
                    );

                    valid = false;

                }
                else {

                    hapusError(jumlah);

                }

            }


            if (!valid) {

                e.preventDefault();

            }

        }
    );

}

function initPeriodeTransaksi() {
    const tanggal = document.querySelector("#tanggal");
    const bulan = document.querySelector("#bulan");
    const tahun = document.querySelector("#tahun");

    if (!tanggal || !bulan || !tahun) return;

    tanggal.addEventListener("change", function () {
        const date = new Date(`${tanggal.value}T00:00:00`);
        if (Number.isNaN(date.getTime())) return;
        bulan.value = String(date.getMonth() + 1);
        tahun.value = String(date.getFullYear());
    });
}

function initKategoriTransaksi() {
    const jenis = document.querySelector("#jenis");
    const kategori = document.querySelector("#kategori");
    if (!jenis || !kategori) return;
    const options = {
        masuk: ["Sedekah Subuh", "Infaq", "Zakat Maal", "Wakaf Pembebasan Tanah", "Infaq TPQ", "Lain-lain"],
        keluar: ["Biaya Operasional", "Bisyaroh Pengajar", "Biaya Sewa Ruko", "Lain-lain"]
    };
    const render = function () {
        const selected = kategori.dataset.selected || "";
        kategori.innerHTML = options[jenis.value].map(function (item) {
            return `<option value="${item}"${item === selected ? " selected" : ""}>${item}</option>`;
        }).join("");
        kategori.dataset.selected = "";
    };
    jenis.addEventListener("change", render);
    render();
}


// ========================================
// JALANKAN SAAT HALAMAN SELESAI DIMUAT
// ========================================

document.addEventListener(
    "DOMContentLoaded",
    function () {

        initNavToggle();

        initHapusConfirm();

        initTableFilter();

        initValidasiForm();
        initPeriodeTransaksi();
        initKategoriTransaksi();

    }
);
