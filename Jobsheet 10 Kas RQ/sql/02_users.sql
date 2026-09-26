CREATE TABLE IF NOT EXISTS users (
    id BIGSERIAL PRIMARY KEY,
    nama VARCHAR(120) NOT NULL,
    username VARCHAR(60) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'bendahara' CHECK (role IN ('bendahara', 'ketua')),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DO $$
BEGIN
    IF EXISTS (SELECT 1 FROM pg_constraint WHERE conname = 'users_role_check') THEN
        ALTER TABLE users DROP CONSTRAINT users_role_check;
    END IF;
    UPDATE users SET role = 'bendahara' WHERE role IN ('admin', 'petugas');
    ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('bendahara', 'ketua'));
EXCEPTION WHEN duplicate_object THEN
    NULL;
END $$;
