-- Migration to create approvals related tables.
-- Stores approval requests and decision history.
-- 0010_create_approvals_tables.sql
-- Approval requests and decisions.

SET search_path TO workflows;

CREATE TABLE IF NOT EXISTS approvals (
    id             BIGSERIAL PRIMARY KEY,
    approval_type  VARCHAR(50) NOT NULL, -- e.g. project_completion
    target_id      BIGINT NOT NULL,      -- e.g. projects.id
    requested_by   BIGINT REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    status         VARCHAR(30) NOT NULL DEFAULT 'pending', -- pending, approved, rejected
    created_at     TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at     TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_approvals_type_target ON approvals (approval_type, target_id);
CREATE INDEX IF NOT EXISTS idx_approvals_status ON approvals (status);

CREATE TABLE IF NOT EXISTS approval_decisions (
    id            BIGSERIAL PRIMARY KEY,
    approval_id   BIGINT NOT NULL REFERENCES approvals(id) ON UPDATE CASCADE ON DELETE CASCADE,
    approver_id   BIGINT NOT NULL REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    decision      VARCHAR(30) NOT NULL, -- approved, rejected
    comment       TEXT,
    decided_at    TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_approval_decisions_approval_id ON approval_decisions (approval_id);
CREATE INDEX IF NOT EXISTS idx_approval_decisions_approver_id ON approval_decisions (approver_id);
