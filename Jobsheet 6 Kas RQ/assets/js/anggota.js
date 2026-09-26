document.addEventListener("DOMContentLoaded", function () {
    muatDaftarAnggota();
});

async function muatDaftarAnggota() {
    const tbody = document.querySelector("tbody");
    if (!tbody) return;

    // 1. Tampilkan Loading Indicator
    tbody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-4 loading-indicator">
                <div class="spinner-border spinner-border-sm text-custom me-2" role="status"></div>
                <span>Memuat data...</span>
            </td>
        </tr>
    `;

    try {
        // Simulasi delay sedikit
        await new Promise(resolve => setTimeout(resolve, 600));

        // 2. Fetch Data JSON
        const response = await fetch("../data/anggota.json");

        if (!response.ok) {
            throw new Error(`Gagal mengambil data (Status: ${response.status})`);
        }

        const dataAnggota = await response.json();

        // 3. Render Data ke Tabel
        tbody.innerHTML = "";

        if (dataAnggota.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-3 text-muted">Tidak ada data anggota.</td>
                </tr>
            `;
            return;
        }

        dataAnggota.forEach(function (anggota) {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td class="text-center">${anggota.no_anggota}</td>
                <td>${anggota.nama}</td>
                <td>${anggota.alamat}</td>
                <td class="text-center">${anggota.no_hp}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-edit btn-sm rounded-pill text-dark fw-bold me-1">Edit</button>
                    <button type="button" class="btn btn-hapus btn-sm rounded-pill text-white fw-bold">Hapus</button>
                </td>
            `;
            tbody.appendChild(tr);
        });

    } catch (error) {
        console.error("Error:", error);
        // 4. Tampilkan Pesan Error di Tabel
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-4 text-danger fw-semibold">
                    âš ï¸ Terjadi kesalahan saat memuat data anggota: ${error.message}
                </td>
            </tr>
        `;
    }
}
