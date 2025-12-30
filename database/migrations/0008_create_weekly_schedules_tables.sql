-- Migration to create weekly schedules related tables.
-- Stores weekly schedules and their items.
-- 0008_create_weekly_schedules_tables.sql
-- Weekly schedules and schedule items.

SET search_path TO workflows;

CREATE TABLE IF NOT EXISTS weekly_schedules (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT NOT NULL REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    week_start_date DATE NOT NULL,          -- Monday (or chosen) of the week
    summary_plan    TEXT,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (user_id, week_start_date)
);

CREATE INDEX IF NOT EXISTS idx_weekly_schedules_user_id ON weekly_schedules (user_id);

CREATE TABLE IF NOT EXISTS weekly_schedule_items (
    id            BIGSERIAL PRIMARY KEY,
    schedule_id   BIGINT NOT NULL REFERENCES weekly_schedules(id) ON UPDATE CASCADE ON DELETE CASCADE,
    description   TEXT NOT NULL,
    estimated_hours NUMERIC(5,2),
    project_id    BIGINT REFERENCES projects(id) ON UPDATE CASCADE ON DELETE SET NULL,
    created_at    TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_schedule_items_schedule_id ON weekly_schedule_items (schedule_id);
CREATE INDEX IF NOT EXISTS idx_schedule_items_project_id ON weekly_schedule_items (project_id);
