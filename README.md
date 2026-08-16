# MiniRank

A small full-stack keyword position tracker for a single configured website.
Tracks daily **simulated** search-engine positions per keyword — no external
search APIs are used.

## Features

- CRUD for tracked keywords (no authentication)
- Seed script with 6 demo keywords and ~30 days of daily positions (1–100)
- One-click **Refresh positions**: today's ranks are generated server-side and
  the list updates via AJAX, no page reload
- Keyword list: current position, 7-day trend (Improved / Declined / Stable)
  and text search
- Detail page per keyword: full position history table + chart
- Responsive UI (table collapses to cards on small screens)

## Stack

- PHP 8+ (PDO + SQLite)
- HTML / CSS / vanilla JS
- No frameworks, no runtime dependencies

## Requirements

- PHP 8+ with the `pdo_sqlite` extension enabled.
  XAMPP ships this ready to use — e.g. `C:\xampp\php\php.exe`.

Verify PHP and the extension:

```
php -v
php -m | grep pdo_sqlite        # Windows PowerShell: php -m | Select-String pdo_sqlite
```

If `php` is not on your PATH, use the full path, e.g.
`C:\xampp\php\php.exe -v`.

## Setup

From the project root:

1. **Seed the database** — creates `data/minirank.db`, applies the schema and
   inserts demo keywords with ~30 days of positions:

   ```
   php scripts/seed.php
   ```

2. **Start the app** (one command):

   ```
   php -S localhost:8000 -t public
   ```

3. Open http://localhost:8000

Notes:

- Re-running `scripts/seed.php` is safe: it exits without touching data once
  the database already has keywords.
- The SQLite database lives in `data/` and is gitignored — nothing sensitive is
  ever committed.

## Commands

| Command | Purpose |
|---|---|
| `php scripts/seed.php` | Create DB + schema + demo data (idempotent) |
| `php -S localhost:8000 -t public` | One-command start of the app |

## Project structure

```
public/            web root: index.php (list), keyword.php (detail), api/refresh.php (AJAX), assets/
src/               app code (outside web root)
  config.php       configuration (SQLite path — no secrets)
  Database.php     PDO connection
  KeywordRepository.php   data access (parameterized SQL)
  RankingService.php      business logic (simulated refresh, 7-day trend)
  helpers.php      output escaping + view helpers
  views/           header, footer, list, detail
scripts/seed.php   seed command
migrations/schema.sql     table definitions
data/              SQLite database (gitignored)
```

## Security

- All SQL uses PDO prepared statements with bound parameters.
- All dynamic output is HTML-escaped (`htmlspecialchars` with `ENT_QUOTES`).
- No secrets: configuration holds only the database path.