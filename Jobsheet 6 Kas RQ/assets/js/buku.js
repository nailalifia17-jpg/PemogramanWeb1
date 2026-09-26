document.addEventListener("DOMContentLoaded", function () {
    muatDaftarBuku();
});

async function muatDaftarBuku() {
    const tbody = document.querySelector("tbody");
    if (!tbody) return;

    // 1. Tampilkan Loading Indicator
    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-4 loading-indicator">
                <div class="spinner-border spinner-border-sm text-custom me-2" role="status"></div>
                <span>Memuat data...</span>
            </td>
        </tr>
    `;

    try {
        // Simulasi delay sedikit (opsional, dapat dihapus jika tidak diperlukan)
        await new Promise(resolve => setTimeout(resolve, 600));

        // 2. Fetch Data JSON
        const response = await fetch("../data/buku.json");

        if (!response.ok) {
            throw new Error(`Gagal mengambil data (Status: ${response.status})`);
        }

        const dataBuku = await response.json();

        // 3. Render Data ke Tabel
        tbody.innerHTML = "";

        if (dataBuku.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-3 text-muted">Tidak ada data buku.</td>
                </tr>
            `;
            return;
        }

        dataBuku.forEach(function (buku, index) {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td class="text-center">${index + 1}</td>
                <td>${buku.judul}</td>
                <td>${buku.pengarang}</td>
                <td class="text-center">${buku.tahun}</td>
                <td class="text-center">${buku.stok}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-edit btn-sm rounded-pill text-dark fw-bold me-1">Edit</button>
                    <button type="button" class="btn btn-detail btn-sm rounded-pill text-white fw-bold me-1">Detail</button>
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
                <td colspan="6" class="text-center py-4 text-danger fw-semibold">
                    âš ï¸ Terjadi kesalahan saat memuat data buku: ${error.message}
                </td>
            </tr>
        `;
    }
}
