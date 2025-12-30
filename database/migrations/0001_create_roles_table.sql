-- Migration to create roles table.
-- Define core roles such as Super Admin, Director, System Admin, Developer, and Marketer.
-- 0001_create_roles_table.sql
-- Creates schema and core roles table.

CREATE SCHEMA IF NOT EXISTS workflows;

SET search_path TO workflows;

CREATE TABLE IF NOT EXISTS roles (
    id          BIGSERIAL PRIMARY KEY,
    key         VARCHAR(50) NOT NULL UNIQUE,
    name        VARCHAR(100) NOT NULL,
    description TEXT,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Index for quick lookup by key
CREATE INDEX IF NOT EXISTS idx_roles_key ON roles (key);
