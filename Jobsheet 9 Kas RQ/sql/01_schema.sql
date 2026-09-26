CREATE TABLE IF NOT EXISTS warga (
    id BIGSERIAL PRIMARY KEY,
    no_kk VARCHAR(32) NOT NULL UNIQUE,
    nama VARCHAR(120) NOT NULL,
    alamat TEXT NOT NULL,
    no_hp VARCHAR(30) NOT NULL DEFAULT '',
    status VARCHAR(20) NOT NULL DEFAULT 'aktif' CHECK (status IN ('aktif', 'pindah')),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS transaksi (
    id BIGSERIAL PRIMARY KEY,
    tanggal DATE NOT NULL,
    bulan SMALLINT NOT NULL CHECK (bulan BETWEEN 1 AND 12),
    tahun SMALLINT NOT NULL CHECK (tahun BETWEEN 2000 AND 2100),
    keterangan VARCHAR(255) NOT NULL,
    jenis VARCHAR(10) NOT NULL CHECK (jenis IN ('masuk', 'keluar')),
    jumlah INTEGER NOT NULL CHECK (jumlah > 0),
    metode VARCHAR(20) NOT NULL DEFAULT 'tunai' CHECK (metode IN ('tunai', 'transfer')),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_transaksi_periode ON transaksi (tahun, bulan);
CREATE INDEX IF NOT EXISTS idx_transaksi_jenis ON transaksi (jenis);
