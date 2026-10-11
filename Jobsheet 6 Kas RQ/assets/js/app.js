document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
});

/**
 * 1. Menu Hamburger Toggle
 */
function initNavToggle() {
    const toggler = document.querySelector(".navbar-toggler");
    const navbarCollapse = document.querySelector("#navbarNav");

    if (!toggler || !navbarCollapse) return;

    toggler.addEventListener("click", function () {
        const isExpanded = navbarCollapse.classList.toggle("show");
        toggler.setAttribute("aria-expanded", String(isExpanded));
    });
}

/**
 * 2. Konfirmasi & Hapus Baris Tabel (Menggunakan Event Delegation)
 */
function initHapusConfirm() {
    document.addEventListener("click", function (event) {
        const target = event.target;
        if (!(target instanceof Element)) return;

        const btnHapus = target.closest(".btn-hapus");
        if (btnHapus) {
            const row = btnHapus.closest("tr");
            const konfirmasi = confirm("Apakah Anda yakin ingin menghapus data ini?");

            if (konfirmasi && row) {
                row.remove();
            }
        }
    });
}

/**
 * 3. Filter / Pencarian Tabel Real-Time
 */
function initTableFilter() {
    const searchInput = document.querySelector(".search-box");

    if (!searchInput) return;

    searchInput.addEventListener("input", function () {
        const keyword = searchInput.value.toLowerCase().trim();
        const tableRows = document.querySelectorAll("tbody tr");

        tableRows.forEach(function (row) {
            // Biarkan baris loading atau error tetap tampil jika ada
            if (row.querySelector(".loading-indicator") || row.querySelector(".text-danger")) {
                return;
            }

            const text = row.textContent.toLowerCase();
            if (text.includes(keyword)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
}

/**
 * 4. Validasi Form Sisi Klien
 */
function initValidasiForm() {
    const form = document.querySelector("#form-tambah");

    if (!form) return;

    form.addEventListener("submit", function (event) {
        let isValid = true;
        const requiredInputs = form.querySelectorAll("input[required], select[required], textarea[required]");

        requiredInputs.forEach(function (input) {
            hapusError(input);

            if (input.value.trim() === "") {
                isValid = false;
                const fieldName = input.previousElementSibling ? input.previousElementSibling.textContent : "Field ini";
                tampilkanError(input, `${fieldName} wajib diisi!`);
            }
        });

        if (!isValid) {
            event.preventDefault();
            return;
        }

        event.preventDefault();
        const feedback = form.parentElement.querySelector(".form-feedback");
        if (feedback) {
            feedback.hidden = false;
            feedback.textContent = "Form valid. Pada Jobsheet 6, data belum disimpan.";
        }
    });
}

function tampilkanError(input, pesan) {
    input.classList.add("is-invalid");

    const errorDiv = document.createElement("div");
    errorDiv.className = "error-message text-danger small mt-1";
    errorDiv.textContent = pesan;

    input.insertAdjacentElement("afterend", errorDiv);
}

function hapusError(input) {
    input.classList.remove("is-invalid");
    const existingError = input.parentElement.querySelector(".error-message");
    if (existingError) {
        existingError.remove();
    }
}
