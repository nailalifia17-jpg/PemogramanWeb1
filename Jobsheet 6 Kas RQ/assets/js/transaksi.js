let dataTransaksiSaatIni = [];

document.addEventListener("DOMContentLoaded", function () {
    muatDaftarTransaksi();
    initEditTransaksi();
});

async function muatDaftarTransaksi() {
    const tbody = document.querySelector("#daftar-transaksi");
    if (!tbody) return;

    tampilkanPesanTabel(tbody, 7, "Memuat data transaksi...", "loading-indicator");

    try {
        await new Promise(resolve => setTimeout(resolve, 600));

        const response = await fetch("../data/transaksi.json");
        if (!response.ok) {
            throw new Error(`Gagal mengambil data (Status: ${response.status})`);
        }

        const dataTransaksi = await response.json();
        if (!Array.isArray(dataTransaksi)) {
            throw new Error("Format data transaksi tidak valid.");
        }

        dataTransaksiSaatIni = gabungkanEditTransaksi(dataTransaksi);
        renderDaftarTransaksi();
    } catch (error) {
        console.error("Gagal memuat data transaksi:", error);
        tampilkanPesanTabel(tbody, 7, `Terjadi kesalahan saat memuat data transaksi: ${error.message}`, "text-danger");
    }
}

function renderDaftarTransaksi() {
    const tbody = document.querySelector("#daftar-transaksi");
    if (!tbody) return;

    const dataTransaksi = dataTransaksiSaatIni;
    tbody.replaceChildren();
    if (dataTransaksi.length === 0) {
        tampilkanPesanTabel(tbody, 7, "Belum ada data transaksi.", "text-muted");
        return;
    }

    dataTransaksi.forEach(function (transaksi) {
        const row = document.createElement("tr");
        tambahSel(row, transaksi.tanggal);
        tambahSel(row, transaksi.keterangan);
        tambahSel(row, transaksi.kategori);

        const jenisCell = tambahSel(row, "");
        jenisCell.classList.add("text-center");
        const jenisBadge = document.createElement("span");
        jenisBadge.className = `badge badge-${transaksi.jenis}`;
        jenisBadge.textContent = transaksi.jenis === "masuk" ? "Pemasukan" : "Pengeluaran";
        jenisCell.appendChild(jenisBadge);

        const jumlahCell = tambahSel(row, formatRupiah(transaksi.jumlah));
        jumlahCell.classList.add("text-end");
        tambahSel(row, transaksi.metode === "transfer" ? "Transfer" : "Tunai").classList.add("text-center");

        const actionCell = tambahSel(row, "");
        actionCell.classList.add("text-center");
        const editButton = document.createElement("button");
        editButton.type = "button";
        editButton.className = "btn btn-submit btn-sm rounded-pill text-white fw-bold me-1";
        editButton.dataset.aksiEditTransaksi = String(transaksi.id);
        editButton.textContent = "Edit";
        actionCell.appendChild(editButton);

        const deleteButton = document.createElement("button");
        deleteButton.type = "button";
        deleteButton.className = "btn btn-hapus btn-sm rounded-pill text-white fw-bold";
        deleteButton.textContent = "Hapus";
        actionCell.appendChild(deleteButton);

        tbody.appendChild(row);
    });
}

function gabungkanEditTransaksi(data) {
    const tersimpan = localStorage.getItem("jobsheet6-edit-transaksi");
    if (!tersimpan) return data;
    const edit = JSON.parse(tersimpan);
    if (!edit || Array.isArray(edit) || typeof edit !== "object") {
        throw new Error("Data edit transaksi di browser tidak valid.");
    }
    return data.map(item => ({ ...item, ...(edit[String(item.id)] ?? {}) }));
}

function initEditTransaksi() {
    const tbody = document.querySelector("#daftar-transaksi");
    const form = document.querySelector("#form-edit-transaksi");
    const modalElement = document.querySelector("#modal-edit-transaksi");
    if (!tbody || !form || !modalElement) return;

    const modal = new bootstrap.Modal(modalElement);
    tbody.addEventListener("click", event => {
        const target = event.target;
        if (!(target instanceof Element)) return;
        const button = target.closest("[data-aksi-edit-transaksi]");
        if (!button) return;
        const transaksi = dataTransaksiSaatIni.find(item => String(item.id) === button.dataset.aksiEditTransaksi);
        if (!transaksi) return;
        const feedback = form.querySelector(".form-feedback");
        feedback.hidden = true;
        feedback.textContent = "";
        feedback.classList.remove("text-danger");
        form.elements.tanggal.value = transaksi.tanggal;
        form.elements.keterangan.value = transaksi.keterangan;
        form.elements.kategori.value = transaksi.kategori;
        form.elements.jenis.value = transaksi.jenis;
        form.elements.jumlah.value = transaksi.jumlah;
        form.elements.metode.value = transaksi.metode;
        form.dataset.transaksiId = String(transaksi.id);
        modal.show();
    });

    form.addEventListener("submit", event => {
        event.preventDefault();
        if (!form.reportValidity()) return;

        const id = form.dataset.transaksiId;
        const edited = {
            tanggal: form.elements.tanggal.value,
            keterangan: form.elements.keterangan.value.trim(),
            kategori: form.elements.kategori.value.trim(),
            jenis: form.elements.jenis.value,
            jumlah: Number(form.elements.jumlah.value),
            metode: form.elements.metode.value
        };
        try {
            const stored = localStorage.getItem("jobsheet6-edit-transaksi");
            const edits = stored ? JSON.parse(stored) : {};
            if (!edits || Array.isArray(edits) || typeof edits !== "object") {
                throw new Error("Data edit transaksi di browser tidak valid.");
            }
            edits[id] = edited;
            localStorage.setItem("jobsheet6-edit-transaksi", JSON.stringify(edits));
            dataTransaksiSaatIni = dataTransaksiSaatIni.map(item =>
                String(item.id) === id ? { ...item, ...edited } : item
            );
            renderDaftarTransaksi();
            modal.hide();
        } catch (error) {
            const feedback = form.querySelector(".form-feedback");
            feedback.textContent = `Perubahan tidak dapat disimpan di browser: ${error.message}`;
            feedback.classList.add("text-danger");
            feedback.hidden = false;
        }
    });
}

function tambahSel(row, text) {
    const cell = document.createElement("td");
    cell.textContent = text ?? "";
    row.appendChild(cell);
    return cell;
}

function tampilkanPesanTabel(tbody, jumlahKolom, pesan, className) {
    const row = document.createElement("tr");
    const cell = document.createElement("td");
    cell.colSpan = jumlahKolom;
    cell.className = `text-center py-4 ${className}`;
    cell.textContent = pesan;
    row.appendChild(cell);
    tbody.replaceChildren(row);
}

function formatRupiah(jumlah) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0
    }).format(jumlah);
}
