-- Migration to create project assignments table.
-- Links users to projects with specific roles or responsibilities.
-- 0004_create_project_assignments_table.sql
-- Links users to projects with a role type (developer, marketer, etc.).

SET search_path TO workflows;

CREATE TABLE IF NOT EXISTS project_assignments (
    id          BIGSERIAL PRIMARY KEY,
    project_id  BIGINT NOT NULL REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE,
    user_id     BIGINT NOT NULL REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    role_type   VARCHAR(50) NOT NULL, -- developer, marketer, etc.
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE UNIQUE INDEX IF NOT EXISTS uq_project_assignments_project_user
    ON project_assignments (project_id, user_id);

CREATE INDEX IF NOT EXISTS idx_project_assignments_user_id ON project_assignments (user_id);
CREATE INDEX IF NOT EXISTS idx_project_assignments_role_type ON project_assignments (role_type);
