document.addEventListener("DOMContentLoaded", () => {
    // 1. Toggle Navigasi Responsive
    const navToggleBtn = document.getElementById("nav-toggle-btn");
    const navMenu = document.querySelector("header nav");

    if (navToggleBtn && navMenu) {
        navToggleBtn.addEventListener("click", () => {
            navMenu.classList.toggle("active");
        });
    }

    // 2. Load Data Warga Dinamis (Jobsheet 6 - Fetch API & JSON)
    const tbodyWarga = document.querySelector("table tbody");
    
    if (tbodyWarga && window.location.pathname.includes("warga/list.html")) {
        loadWargaData(tbodyWarga);
    }

    // 3. Form Tambah Warga Handler
    const formTambah = document.getElementById("form-tambah");
    if (formTambah) {
        formTambah.addEventListener("submit", (e) => {
            e.preventDefault();
            alert("Data warga berhasil disimpan (Simulasi)!");
            window.location.href = "list.html";
        });
    }
});

// Fungsi Asinkron untuk Mengambil Data Warga dari JSON
async function loadWargaData(tbodyElement) {
    // Tampilkan indikator loading sementara
    tbodyElement.innerHTML = `<tr><td colspan="5" style="text-align: center;">Memuat data warga...</td></tr>`;

    try {
        const response = await fetch("../assets/data/warga.json");
        
        if (!response.ok) {
            throw new Error(`Gagal memuat data: ${response.statusText}`);
        }

        const wargaList = await response.json();
        renderWargaTable(wargaList, tbodyElement);

    } catch (error) {
        console.error("Error fetching data:", error);
        tbodyElement.innerHTML = `<tr><td colspan="5" style="text-align: center; color: red;">Gagal memuat data warga. Pastikan berjalan di local server (Live Server).</td></tr>`;
    }
}

// Fungsi Merender Data ke dalam Tabel HTML menggunakan Event Delegation
function renderWargaTable(data, tbodyElement) {
    tbodyElement.innerHTML = ""; // Bersihkan kontainer/loading

    if (data.length === 0) {
        tbodyElement.innerHTML = `<tr><td colspan="5" style="text-align: center;">Tidak ada data warga.</td></tr>`;
        return;
    }

    data.forEach((warga) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${warga.no_kk}</td>
            <td>${warga.nama}</td>
            <td>${warga.alamat}</td>
            <td>${warga.no_hp}</td>
            <td>
                <button type="button" class="btn-edit" data-kk="${warga.no_kk}">Edit</button>
                <button type="button" class="btn-hapus" data-kk="${warga.no_kk}">Hapus</button>
            </td>
        `;
        tbodyElement.appendChild(tr);
    });

    // Implementasi Event Delegation untuk tombol aksi dalam tabel dinamis
    tbodyElement.addEventListener("click", (e) => {
        if (e.target.classList.contains("btn-hapus")) {
            const kk = e.target.getAttribute("data-kk");
            if (confirm(`Apakah Anda yakin ingin menghapus data dengan No. KK: ${kk}?`)) {
                e.target.closest("tr").remove();
                alert("Data berhasil dihapus.");
            }
        } else if (e.target.classList.contains("btn-edit")) {
            const kk = e.target.getAttribute("data-kk");
            alert(`Fitur edit untuk No. KK ${kk} akan segera dibuka.`);
        }
    });
}