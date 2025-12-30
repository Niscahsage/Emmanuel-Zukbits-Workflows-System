-- Migration to create notifications table.
-- Stores notifications targeted at specific users.
-- 0011_create_notifications_table.sql
-- In-system notifications.

SET search_path TO workflows;

CREATE TABLE IF NOT EXISTS notifications (
    id         BIGSERIAL PRIMARY KEY,
    user_id    BIGINT NOT NULL REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    type       VARCHAR(50) NOT NULL,  -- e.g. project_assigned, approval_requested
    message    TEXT NOT NULL,
    link       TEXT,                  -- URL/path to open when clicked
    is_read    BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_notifications_user_id ON notifications (user_id);
CREATE INDEX IF NOT EXISTS idx_notifications_is_read ON notifications (is_read);
