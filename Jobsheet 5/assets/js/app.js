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
        navbarCollapse.classList.toggle("show");
    });
}

/**
 * 2. Konfirmasi & Hapus Baris Tabel
 */
function initHapusConfirm() {
    const btnHapusList = document.querySelectorAll(".btn-hapus");

    if (btnHapusList.length === 0) return;

    btnHapusList.forEach(function (btn) {
        btn.addEventListener("click", function (event) {
            const row = event.target.closest("tr");
            const konfirmasi = confirm("Apakah Anda yakin ingin menghapus data ini?");

            if (konfirmasi && row) {
                row.remove();
            }
        });
    });
}

/**
 * 3. Filter / Pencarian Tabel Real-Time
 */
function initTableFilter() {
    const searchInput = document.querySelector(".search-box");
    const tableRows = document.querySelectorAll("tbody tr");

    if (!searchInput || tableRows.length === 0) return;

    searchInput.addEventListener("keyup", function () {
        const keyword = searchInput.value.toLowerCase().trim();

        tableRows.forEach(function (row) {
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
        const requiredInputs = form.querySelectorAll("input[required]");

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