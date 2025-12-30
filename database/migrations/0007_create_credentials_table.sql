-- Migration to create credentials table.
-- Stores encrypted credentials and sensitive integration data.
-- 0007_create_credentials_table.sql
-- Encrypted credentials / integration keys.

SET search_path TO workflows;

CREATE TABLE IF NOT EXISTS credentials (
    id            BIGSERIAL PRIMARY KEY,
    project_id    BIGINT NOT NULL REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE,
    label         VARCHAR(200) NOT NULL,  -- e.g. "Production DB", "Stripe API Key"
    description   TEXT,
    encrypted_value TEXT NOT NULL,       -- ciphertext
    allowed_roles TEXT,                  -- optional: comma-separated allowed roles
    created_by    BIGINT REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    created_at    TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at    TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_credentials_project_id ON credentials (project_id);
CREATE INDEX IF NOT EXISTS idx_credentials_label ON credentials (label);
