-- Migration to create weekly reports related tables.
-- Stores weekly reports and item-level outcomes.
-- 0009_create_weekly_reports_tables.sql
-- Weekly reports and report items.

SET search_path TO workflows;

CREATE TABLE IF NOT EXISTS weekly_reports (
    id             BIGSERIAL PRIMARY KEY,
    schedule_id    BIGINT NOT NULL REFERENCES weekly_schedules(id) ON UPDATE CASCADE ON DELETE CASCADE,
    user_id        BIGINT NOT NULL REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    overall_summary TEXT,
    challenges     TEXT,
    support_needed TEXT,
    submitted_at   TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_at     TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (schedule_id, user_id)
);

CREATE INDEX IF NOT EXISTS idx_weekly_reports_user_id ON weekly_reports (user_id);

CREATE TABLE IF NOT EXISTS weekly_report_items (
    id               BIGSERIAL PRIMARY KEY,
    report_id        BIGINT NOT NULL REFERENCES weekly_reports(id) ON UPDATE CASCADE ON DELETE CASCADE,
    schedule_item_id BIGINT REFERENCES weekly_schedule_items(id) ON UPDATE CASCADE ON DELETE SET NULL,
    status           VARCHAR(30) NOT NULL, -- completed, partially_completed, not_completed
    comment          TEXT,
    created_at       TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_weekly_report_items_report_id ON weekly_report_items (report_id);
