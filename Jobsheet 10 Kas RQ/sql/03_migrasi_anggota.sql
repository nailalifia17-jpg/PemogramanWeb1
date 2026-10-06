DO $$
BEGIN
    IF to_regclass('public.anggota') IS NULL AND to_regclass('public.warga') IS NOT NULL THEN
        ALTER TABLE warga RENAME TO anggota;
    END IF;
END $$;