DO $$
BEGIN
    IF EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = 'public' AND table_name = 'anggota' AND column_name = 'no_kk'
    ) AND NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = 'public' AND table_name = 'anggota' AND column_name = 'no_anggota'
    ) THEN
        ALTER TABLE anggota RENAME COLUMN no_kk TO no_anggota;
        UPDATE anggota
        SET no_anggota = 'RQ-' || LPAD(id::text, 6, '0');
    END IF;
END $$;

ALTER TABLE anggota ADD COLUMN IF NOT EXISTS no_anggota VARCHAR(32);
UPDATE anggota
SET no_anggota = 'RQ-' || LPAD(id::text, 6, '0')
WHERE no_anggota IS NULL OR no_anggota = '';
ALTER TABLE anggota ALTER COLUMN no_anggota SET NOT NULL;
CREATE UNIQUE INDEX IF NOT EXISTS idx_anggota_no_anggota ON anggota (no_anggota);

DO $$
DECLARE
    constraint_name TEXT;
BEGIN
    FOR constraint_name IN
        SELECT conname
        FROM pg_constraint
        WHERE conrelid = 'anggota'::regclass
          AND contype = 'c'
          AND pg_get_constraintdef(oid) ILIKE '%status%'
    LOOP
        EXECUTE format('ALTER TABLE anggota DROP CONSTRAINT %I', constraint_name);
    END LOOP;
END $$;

UPDATE anggota SET status = 'nonaktif' WHERE status = 'pindah';
ALTER TABLE anggota ALTER COLUMN status SET DEFAULT 'aktif';
ALTER TABLE anggota
    ADD CONSTRAINT anggota_status_keanggotaan_check
    CHECK (status IN ('aktif', 'nonaktif'));
