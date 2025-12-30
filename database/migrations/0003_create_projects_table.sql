-- Migration to create projects table.
-- Stores project metadata such as name, status, priority, and dates.
-- 0003_create_projects_table.sql
-- Creates projects table.

SET search_path TO workflows;

CREATE TABLE IF NOT EXISTS projects (
    id               BIGSERIAL PRIMARY KEY,
    name             VARCHAR(200) NOT NULL,
    code             VARCHAR(50) UNIQUE,
    client_name      VARCHAR(200),
    description      TEXT,
    objectives       TEXT,
    category         VARCHAR(50),   -- internal, client, feature, campaign etc.
    priority         VARCHAR(20),   -- high, medium, low
    status           VARCHAR(50) NOT NULL DEFAULT 'draft', -- draft, ongoing, pending_approval, approved, archived
    start_date       DATE,
    target_end_date  DATE,
    created_by       BIGINT REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    created_at       TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at       TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_projects_status ON projects (status);
CREATE INDEX IF NOT EXISTS idx_projects_priority ON projects (priority);
CREATE INDEX IF NOT EXISTS idx_projects_category ON projects (category);
CREATE INDEX IF NOT EXISTS idx_projects_created_by ON projects (created_by);
