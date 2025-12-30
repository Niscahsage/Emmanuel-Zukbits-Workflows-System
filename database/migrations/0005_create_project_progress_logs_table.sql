-- Migration to create project progress logs table.
-- Stores time-stamped updates and comments on project progress.
-- 0005_create_project_progress_logs_table.sql
-- Stores time-stamped updates for project progress.

SET search_path TO workflows;

CREATE TABLE IF NOT EXISTS project_progress_logs (
    id              BIGSERIAL PRIMARY KEY,
    project_id      BIGINT NOT NULL REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE,
    user_id         BIGINT NOT NULL REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    comment         TEXT NOT NULL,
    status_snapshot VARCHAR(50), -- optional: snapshot of status at log time
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_progress_logs_project_id ON project_progress_logs (project_id);
CREATE INDEX IF NOT EXISTS idx_progress_logs_user_id ON project_progress_logs (user_id);
