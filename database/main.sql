-- schema
CREATE SCHEMA IF NOT EXISTS workflows;

SET search_path TO workflows;

-- roles
CREATE TABLE IF NOT EXISTS roles (
    id          BIGSERIAL PRIMARY KEY,
    key         VARCHAR(50) NOT NULL UNIQUE,
    name        VARCHAR(100) NOT NULL,
    description TEXT,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_roles_key ON roles (key);

-- users
CREATE TABLE IF NOT EXISTS users (
    id             BIGSERIAL PRIMARY KEY,
    name           VARCHAR(150) NOT NULL,
    email          VARCHAR(150) NOT NULL UNIQUE,
    password_hash  TEXT NOT NULL,
    role_id        BIGINT NOT NULL REFERENCES roles(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    is_active      BOOLEAN NOT NULL DEFAULT TRUE,
    created_at     TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at     TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_users_role_id ON users (role_id);
CREATE INDEX IF NOT EXISTS idx_users_is_active ON users (is_active);

-- projects
CREATE TABLE IF NOT EXISTS projects (
    id               BIGSERIAL PRIMARY KEY,
    name             VARCHAR(200) NOT NULL,
    code             VARCHAR(50) UNIQUE,
    client_name      VARCHAR(200),
    description      TEXT,
    objectives       TEXT,
    category         VARCHAR(50),
    priority         VARCHAR(20),
    status           VARCHAR(50) NOT NULL DEFAULT 'draft',
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

-- project_assignments
CREATE TABLE IF NOT EXISTS project_assignments (
    id          BIGSERIAL PRIMARY KEY,
    project_id  BIGINT NOT NULL REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE,
    user_id     BIGINT NOT NULL REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    role_type   VARCHAR(50) NOT NULL,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE UNIQUE INDEX IF NOT EXISTS uq_project_assignments_project_user
    ON project_assignments (project_id, user_id);

CREATE INDEX IF NOT EXISTS idx_project_assignments_user_id ON project_assignments (user_id);
CREATE INDEX IF NOT EXISTS idx_project_assignments_role_type ON project_assignments (role_type);

-- project_progress_logs
CREATE TABLE IF NOT EXISTS project_progress_logs (
    id              BIGSERIAL PRIMARY KEY,
    project_id      BIGINT NOT NULL REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE,
    user_id         BIGINT REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    comment         TEXT NOT NULL,
    status_snapshot VARCHAR(50),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_progress_logs_project_id ON project_progress_logs (project_id);
CREATE INDEX IF NOT EXISTS idx_progress_logs_user_id ON project_progress_logs (user_id);

-- documents
CREATE TABLE IF NOT EXISTS documents (
    id          BIGSERIAL PRIMARY KEY,
    project_id  BIGINT REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE,
    title       VARCHAR(250) NOT NULL,
    type        VARCHAR(50),
    body        TEXT,
    file_path   TEXT,
    tags        TEXT,
    created_by  BIGINT REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_documents_project_id ON documents (project_id);
CREATE INDEX IF NOT EXISTS idx_documents_type ON documents (type);

-- credentials
CREATE TABLE IF NOT EXISTS credentials (
    id              BIGSERIAL PRIMARY KEY,
    project_id      BIGINT NOT NULL REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE,
    label           VARCHAR(200) NOT NULL,
    description     TEXT,
    encrypted_value TEXT NOT NULL,
    allowed_roles   TEXT,
    created_by      BIGINT REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_credentials_project_id ON credentials (project_id);
CREATE INDEX IF NOT EXISTS idx_credentials_label ON credentials (label);

-- weekly_schedules
CREATE TABLE IF NOT EXISTS weekly_schedules (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT NOT NULL REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    week_start_date DATE NOT NULL,
    summary_plan    TEXT,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (user_id, week_start_date)
);

CREATE INDEX IF NOT EXISTS idx_weekly_schedules_user_id ON weekly_schedules (user_id);

-- weekly_schedule_items
CREATE TABLE IF NOT EXISTS weekly_schedule_items (
    id              BIGSERIAL PRIMARY KEY,
    schedule_id     BIGINT NOT NULL REFERENCES weekly_schedules(id) ON UPDATE CASCADE ON DELETE CASCADE,
    description     TEXT NOT NULL,
    estimated_hours NUMERIC(5,2),
    project_id      BIGINT REFERENCES projects(id) ON UPDATE CASCADE ON DELETE SET NULL,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_schedule_items_schedule_id ON weekly_schedule_items (schedule_id);
CREATE INDEX IF NOT EXISTS idx_schedule_items_project_id ON weekly_schedule_items (project_id);

-- weekly_reports
CREATE TABLE IF NOT EXISTS weekly_reports (
    id              BIGSERIAL PRIMARY KEY,
    schedule_id     BIGINT NOT NULL REFERENCES weekly_schedules(id) ON UPDATE CASCADE ON DELETE CASCADE,
    user_id         BIGINT NOT NULL REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    overall_summary TEXT,
    challenges      TEXT,
    support_needed  TEXT,
    submitted_at    TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (schedule_id, user_id)
);

CREATE INDEX IF NOT EXISTS idx_weekly_reports_user_id ON weekly_reports (user_id);

-- weekly_report_items
CREATE TABLE IF NOT EXISTS weekly_report_items (
    id               BIGSERIAL PRIMARY KEY,
    report_id        BIGINT NOT NULL REFERENCES weekly_reports(id) ON UPDATE CASCADE ON DELETE CASCADE,
    schedule_item_id BIGINT REFERENCES weekly_schedule_items(id) ON UPDATE CASCADE ON DELETE SET NULL,
    status           VARCHAR(30) NOT NULL,
    comment          TEXT,
    created_at       TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_weekly_report_items_report_id ON weekly_report_items (report_id);

-- approvals
CREATE TABLE IF NOT EXISTS approvals (
    id            BIGSERIAL PRIMARY KEY,
    approval_type VARCHAR(50) NOT NULL,
    target_id     BIGINT NOT NULL,
    requested_by  BIGINT REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    status        VARCHAR(30) NOT NULL DEFAULT 'pending',
    created_at    TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at    TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_approvals_type_target ON approvals (approval_type, target_id);
CREATE INDEX IF NOT EXISTS idx_approvals_status ON approvals (status);

-- approval_decisions
CREATE TABLE IF NOT EXISTS approval_decisions (
    id          BIGSERIAL PRIMARY KEY,
    approval_id BIGINT NOT NULL REFERENCES approvals(id) ON UPDATE CASCADE ON DELETE CASCADE,
    approver_id BIGINT NOT NULL REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    decision    VARCHAR(30) NOT NULL,
    comment     TEXT,
    decided_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_approval_decisions_approval_id ON approval_decisions (approval_id);
CREATE INDEX IF NOT EXISTS idx_approval_decisions_approver_id ON approval_decisions (approver_id);

-- notifications
CREATE TABLE IF NOT EXISTS notifications (
    id         BIGSERIAL PRIMARY KEY,
    user_id    BIGINT NOT NULL REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    type       VARCHAR(50) NOT NULL,
    message    TEXT NOT NULL,
    link       TEXT,
    is_read    BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_notifications_user_id ON notifications (user_id);
CREATE INDEX IF NOT EXISTS idx_notifications_is_read ON notifications (is_read);

-- seed_roles
INSERT INTO roles (key, name, description)
VALUES
    ('super_admin',  'Super Admin',  'Full system access and user management.'),
    ('director',     'Director',     'Executive oversight and approvals.'),
    ('system_admin', 'System Admin', 'System configuration and operational management.'),
    ('developer',    'Developer',    'Technical project implementation and documentation.'),
    ('marketer',     'Marketer',     'Marketing projects and campaigns.')
ON CONFLICT (key) DO UPDATE
SET
    name        = EXCLUDED.name,
    description = EXCLUDED.description,
    updated_at  = NOW();

-- seed_superadmin_user
-- initial password = hunter2 (from known bcrypt example)
-- you can change it later via the app
INSERT INTO users (name, email, password_hash, role_id, is_active)
VALUES (
    'ZukBits Super Admin',
    'emunyao2@gmail.com',
    '$2y$10$GUYJOA4aWc6HegA2x.85PeZ9yjP2HCKFjb44C3vz1jlpUNb4AgYz2',
    (SELECT id FROM roles WHERE key = 'super_admin' LIMIT 1),
    TRUE
)
ON CONFLICT (email) DO UPDATE
SET
    name          = EXCLUDED.name,
    password_hash = EXCLUDED.password_hash,
    role_id       = EXCLUDED.role_id,
    is_active     = TRUE,
    updated_at    = NOW();

-- seed_demo_projects
WITH creator AS (
    SELECT id AS user_id
    FROM users
    WHERE email = 'emunyao2@gmail.com'
    LIMIT 1
)
INSERT INTO projects (
    name, code, client_name, description, objectives,
    category, priority, status, start_date, target_end_date, created_by
)
SELECT
    'Internal ZukBits Workflows Platform',
    'ZWF-001',
    'Zukbits Online',
    'Core internal system for managing projects, documentation, credentials and performance.',
    'Centralize operations and performance tracking.',
    'internal',
    'high',
    'ongoing',
    CURRENT_DATE,
    CURRENT_DATE + INTERVAL '30 days',
    creator.user_id
FROM creator
ON CONFLICT (code) DO UPDATE
SET
    name         = EXCLUDED.name,
    client_name  = EXCLUDED.client_name,
    description  = EXCLUDED.description,
    objectives   = EXCLUDED.objectives,
    category     = EXCLUDED.category,
    priority     = EXCLUDED.priority,
    status       = EXCLUDED.status,
    updated_at   = NOW();

WITH creator AS (
    SELECT id AS user_id
    FROM users
    WHERE email = 'emunyao2@gmail.com'
    LIMIT 1
)
INSERT INTO projects (
    name, code, client_name, description, objectives,
    category, priority, status, start_date, target_end_date, created_by
)
SELECT
    'Client Website Revamp',
    'CWR-002',
    'Sample Client Ltd.',
    'Full redesign and backend upgrade of client website.',
    'Improve performance and UX for client platform.',
    'client',
    'medium',
    'draft',
    CURRENT_DATE,
    CURRENT_DATE + INTERVAL '45 days',
    creator.user_id
FROM creator
ON CONFLICT (code) DO UPDATE
SET
    name         = EXCLUDED.name,
    client_name  = EXCLUDED.client_name,
    description  = EXCLUDED.description,
    objectives   = EXCLUDED.objectives,
    category     = EXCLUDED.category,
    priority     = EXCLUDED.priority,
    status       = EXCLUDED.status,
    updated_at   = NOW();

-- seed_demo_project_assignment (superadmin as developer on ZWF-001)
INSERT INTO project_assignments (project_id, user_id, role_type)
SELECT
    p.id,
    u.id,
    'developer'
FROM projects p
JOIN users u ON u.email = 'emunyao2@gmail.com'
WHERE p.code = 'ZWF-001'
ON CONFLICT (project_id, user_id) DO NOTHING;

-- seed_demo_weekly_schedule + item
WITH creator AS (
    SELECT id AS user_id
    FROM users
    WHERE email = 'emunyao2@gmail.com'
    LIMIT 1
),
week_data AS (
    SELECT
        user_id,
        date_trunc('week', now())::date AS week_start
    FROM creator
),
new_schedule AS (
    INSERT INTO weekly_schedules (user_id, week_start_date, summary_plan)
    SELECT
        user_id,
        week_start,
        'Initial demo week – focus on workflows system core modules.'
    FROM week_data
    ON CONFLICT (user_id, week_start_date) DO NOTHING
    RETURNING id, user_id
)
INSERT INTO weekly_schedule_items (schedule_id, description, estimated_hours, project_id)
SELECT
    COALESCE(ns.id,
        (SELECT id FROM weekly_schedules WHERE user_id = wd.user_id AND week_start_date = wd.week_start LIMIT 1)
    ) AS schedule_id,
    'Design database schema and core project module.',
    8,
    (SELECT id FROM projects WHERE code = 'ZWF-001' LIMIT 1) AS project_id
FROM week_data wd
LEFT JOIN new_schedule ns ON ns.user_id = wd.user_id
WHERE COALESCE(ns.id,
        (SELECT id FROM weekly_schedules WHERE user_id = wd.user_id AND week_start_date = wd.week_start LIMIT 1)
    ) IS NOT NULL;