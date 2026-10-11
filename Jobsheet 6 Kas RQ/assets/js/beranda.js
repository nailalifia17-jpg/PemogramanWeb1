document.addEventListener("DOMContentLoaded", function () {
    muatRingkasanBeranda();
});

async function muatRingkasanBeranda() {
    const aktivitas = document.querySelector("#aktivitas-terbaru");
    if (!aktivitas) return;

    try {
        const response = await fetch("data/transaksi.json");
        if (!response.ok) {
            throw new Error(`Gagal mengambil data (Status: ${response.status})`);
        }

        const transaksiDariJson = await response.json();
        if (!Array.isArray(transaksiDariJson)) {
            throw new Error("Format data transaksi tidak valid.");
        }
        const transaksi = gabungkanEditTransaksiBeranda(transaksiDariJson);

        const totalMasuk = transaksi
            .filter(item => item.jenis === "masuk")
            .reduce((total, item) => total + Number(item.jumlah), 0);
        const totalKeluar = transaksi
            .filter(item => item.jenis === "keluar")
            .reduce((total, item) => total + Number(item.jumlah), 0);

        document.querySelector("#total-pemasukan").textContent = formatRupiah(totalMasuk);
        document.querySelector("#total-pengeluaran").textContent = formatRupiah(totalKeluar);
        document.querySelector("#saldo-kas").textContent = formatRupiah(totalMasuk - totalKeluar);

        aktivitas.replaceChildren();
        const transaksiTerbaru = [...transaksi]
            .sort((a, b) => b.tanggal.localeCompare(a.tanggal))
            .slice(0, 3);

        if (transaksiTerbaru.length === 0) {
            tampilkanPesanAktivitas(aktivitas, "Belum ada transaksi.");
            return;
        }

        transaksiTerbaru.forEach(function (item) {
            const row = document.createElement("li");
            const badge = document.createElement("span");
            badge.className = `badge badge-${item.jenis}`;
            badge.textContent = item.jenis === "masuk" ? "Masuk" : "Keluar";

            const description = document.createElement("span");
            description.className = "activity-text";
            description.append(document.createTextNode(`${item.keterangan} — `));
            const amount = document.createElement("strong");
            amount.textContent = formatRupiah(item.jumlah);
            description.appendChild(amount);

            const date = document.createElement("time");
            date.dateTime = item.tanggal;
            date.textContent = item.tanggal;

            row.append(badge, description, date);
            aktivitas.appendChild(row);
        });
    } catch (error) {
        console.error("Gagal memuat ringkasan beranda:", error);
        document.querySelector("#total-pemasukan").textContent = "Tidak tersedia";
        document.querySelector("#total-pengeluaran").textContent = "Tidak tersedia";
        document.querySelector("#saldo-kas").textContent = "Tidak tersedia";
        tampilkanPesanAktivitas(aktivitas, `Gagal memuat transaksi terbaru: ${error.message}`);
    }
}

function gabungkanEditTransaksiBeranda(transaksi) {
    const tersimpan = localStorage.getItem("jobsheet6-edit-transaksi");
    if (!tersimpan) return transaksi;
    const edit = JSON.parse(tersimpan);
    if (!edit || Array.isArray(edit) || typeof edit !== "object") {
        throw new Error("Data edit transaksi di browser tidak valid.");
    }
    return transaksi.map(item => ({ ...item, ...(edit[String(item.id)] ?? {}) }));
}

function formatRupiah(jumlah) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0
    }).format(jumlah);
}

function tampilkanPesanAktivitas(aktivitas, pesan) {
    const row = document.createElement("li");
    row.className = "activity-empty";
    row.textContent = pesan;
    aktivitas.replaceChildren(row);
}
