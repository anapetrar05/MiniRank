-- MiniRank schema
-- Executed by scripts/seed.php (idempotent: safe to run repeatedly).

CREATE TABLE IF NOT EXISTS keywords (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    keyword    TEXT    NOT NULL UNIQUE,
    created_at TEXT    NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS positions (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    keyword_id INTEGER NOT NULL REFERENCES keywords(id) ON DELETE CASCADE,
    position   INTEGER NOT NULL CHECK (position BETWEEN 1 AND 100),
    date       TEXT    NOT NULL,
    UNIQUE (keyword_id, date)
);

CREATE INDEX IF NOT EXISTS idx_positions_keyword_date
    ON positions (keyword_id, date);