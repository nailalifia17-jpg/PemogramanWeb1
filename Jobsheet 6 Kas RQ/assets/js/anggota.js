let dataAnggotaSaatIni = [];

document.addEventListener("DOMContentLoaded", function () {
    muatDaftarAnggota();
    initEditAnggota();
});

async function muatDaftarAnggota() {
    const tbody = document.querySelector("#daftar-anggota");
    if (!tbody) return;

    tampilkanPesanTabel(tbody, 6, "Memuat data anggota...", "loading-indicator");

    try {
        await new Promise(resolve => setTimeout(resolve, 600));

        const response = await fetch("../data/anggota.json");
        if (!response.ok) {
            throw new Error(`Gagal mengambil data (Status: ${response.status})`);
        }

        const dataAnggota = await response.json();
        if (!Array.isArray(dataAnggota)) {
            throw new Error("Format data anggota tidak valid.");
        }

        dataAnggotaSaatIni = gabungkanEditAnggota(dataAnggota);
        renderDaftarAnggota();
    } catch (error) {
        console.error("Gagal memuat data anggota:", error);
        tampilkanPesanTabel(tbody, 6, `Terjadi kesalahan saat memuat data anggota: ${error.message}`, "text-danger");
    }
}

function renderDaftarAnggota() {
    const tbody = document.querySelector("#daftar-anggota");
    if (!tbody) return;

    const dataAnggota = dataAnggotaSaatIni;
    tbody.replaceChildren();
    if (dataAnggota.length === 0) {
        tampilkanPesanTabel(tbody, 6, "Belum ada data anggota.", "text-muted");
        return;
    }

    dataAnggota.forEach(function (anggota) {
        const row = document.createElement("tr");
        tambahSel(row, anggota.no_kk);
        tambahSel(row, anggota.nama);
        tambahSel(row, anggota.alamat);
        tambahSel(row, anggota.no_hp);

        const statusCell = tambahSel(row, "");
        const statusBadge = document.createElement("span");
        statusBadge.className = anggota.status === "aktif"
            ? "badge text-bg-success"
            : "badge text-bg-secondary";
        statusBadge.textContent = anggota.status === "aktif" ? "Aktif" : "Pindah";
        statusCell.classList.add("text-center");
        statusCell.appendChild(statusBadge);

        const actionCell = tambahSel(row, "");
        actionCell.classList.add("text-center");
        const editButton = document.createElement("button");
        editButton.type = "button";
        editButton.className = "btn btn-submit btn-sm rounded-pill text-white fw-bold me-1";
        editButton.dataset.aksiEditAnggota = anggota._id;
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

function gabungkanEditAnggota(data) {
    const tersimpan = localStorage.getItem("jobsheet6-edit-anggota");
    if (!tersimpan) return data.map(item => ({ ...item, _id: item.no_kk }));
    const edit = JSON.parse(tersimpan);
    if (!edit || Array.isArray(edit) || typeof edit !== "object") {
        throw new Error("Data edit anggota di browser tidak valid.");
    }
    return data.map(item => ({ ...item, ...(edit[item.no_kk] ?? {}), _id: item.no_kk }));
}

function initEditAnggota() {
    const tbody = document.querySelector("#daftar-anggota");
    const form = document.querySelector("#form-edit-anggota");
    const modalElement = document.querySelector("#modal-edit-anggota");
    if (!tbody || !form || !modalElement) return;

    const modal = new bootstrap.Modal(modalElement);
    tbody.addEventListener("click", event => {
        const target = event.target;
        if (!(target instanceof Element)) return;
        const button = target.closest("[data-aksi-edit-anggota]");
        if (!button) return;
        const anggota = dataAnggotaSaatIni.find(item => item._id === button.dataset.aksiEditAnggota);
        if (!anggota) return;
        const feedback = form.querySelector(".form-feedback");
        feedback.hidden = true;
        feedback.textContent = "";
        feedback.classList.remove("text-danger");
        form.elements.no_kk.value = anggota.no_kk;
        form.elements.nama.value = anggota.nama;
        form.elements.alamat.value = anggota.alamat;
        form.elements.no_hp.value = anggota.no_hp;
        form.elements.status.value = anggota.status;
        form.dataset.noKk = anggota._id;
        modal.show();
    });

    form.addEventListener("submit", event => {
        event.preventDefault();
        if (!form.reportValidity()) return;

        const noKkLama = form.dataset.noKk;
        const edited = {
            no_kk: form.elements.no_kk.value.trim(),
            nama: form.elements.nama.value.trim(),
            alamat: form.elements.alamat.value.trim(),
            no_hp: form.elements.no_hp.value.trim(),
            status: form.elements.status.value
        };
        try {
            const stored = localStorage.getItem("jobsheet6-edit-anggota");
            const edits = stored ? JSON.parse(stored) : {};
            if (!edits || Array.isArray(edits) || typeof edits !== "object") {
                throw new Error("Data edit anggota di browser tidak valid.");
            }
            edits[noKkLama] = edited;
            localStorage.setItem("jobsheet6-edit-anggota", JSON.stringify(edits));
            dataAnggotaSaatIni = dataAnggotaSaatIni.map(item =>
                item._id === noKkLama ? { ...item, ...edited } : item
            );
            renderDaftarAnggota();
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
