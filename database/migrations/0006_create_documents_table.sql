-- Migration to create documents table.
-- Stores documentation entries for projects.
-- 0006_create_documents_table.sql
-- Project documentation / knowledge entries.

SET search_path TO workflows;

CREATE TABLE IF NOT EXISTS documents (
    id          BIGSERIAL PRIMARY KEY,
    project_id  BIGINT REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE,
    title       VARCHAR(250) NOT NULL,
    type        VARCHAR(50),    -- architecture, integration, notes, etc.
    body        TEXT,
    file_path   TEXT,           -- if you later store files on disk/Object Storage
    tags        TEXT,           -- simple comma-separated tags
    created_by  BIGINT REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_documents_project_id ON documents (project_id);
CREATE INDEX IF NOT EXISTS idx_documents_type ON documents (type);
