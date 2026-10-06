CREATE TABLE IF NOT EXISTS iuran (
    id BIGSERIAL PRIMARY KEY,
    anggota_id BIGINT NOT NULL REFERENCES anggota(id) ON DELETE RESTRICT,
    transaksi_id BIGINT NOT NULL UNIQUE REFERENCES transaksi(id) ON DELETE RESTRICT,
    bulan SMALLINT NOT NULL CHECK (bulan BETWEEN 1 AND 12),
    tahun SMALLINT NOT NULL CHECK (tahun BETWEEN 2000 AND 2100),
    tanggal_bayar DATE NOT NULL,
    jumlah INTEGER NOT NULL CHECK (jumlah > 0),
    metode VARCHAR(20) NOT NULL CHECK (metode IN ('tunai', 'transfer', 'qris')),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (anggota_id, bulan, tahun)
);

CREATE INDEX IF NOT EXISTS idx_iuran_periode ON iuran (tahun, bulan);
CREATE INDEX IF NOT EXISTS idx_iuran_anggota ON iuran (anggota_id);