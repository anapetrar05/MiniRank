# MiniRank

A small full-stack keyword position tracker for a single configured website.
Tracks daily **simulated** search-engine positions per keyword — no external
search APIs are used.

## Features

- CRUD for tracked keywords (requires login)
- User accounts: register, log in, log out; passwords stored as bcrypt hashes;
  all mutating requests are CSRF-protected (forms + AJAX header)
- Demo account `demo@example.com` / `demo1234` is created by the seed script
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

1. **Seed the database** — creates `data/minirank.db`, applies the schema,
   inserts demo keywords with ~30 days of positions and creates the demo
   account `demo@example.com` / `demo1234`:

   ```
   php scripts/seed.php
   ```

2. **Start the app** (one command):

   ```
   php -S localhost:8000 -t public
   ```

3. Open http://localhost:8000 and log in (or register a new account).

Notes:

- Re-running `scripts/seed.php` is safe: it exits without touching data once
  the database already has keywords (the demo account is only created once).
- The SQLite database lives in `data/` and is gitignored — nothing sensitive is
  ever committed.

## Commands

| Command | Purpose |
|---|---|
| `php scripts/seed.php` | Create DB + schema + demo data (idempotent) |
| `php -S localhost:8000 -t public` | One-command start of the app |
| `php phpunit.phar` | Run the PHPUnit test suite |

## Testing

Core logic (seeding, trend calculation and auth) is covered by PHPUnit tests in
`tests/`. They run against an in-memory SQLite database, so they never touch
your `data/` file.

Get the runner (once) and run:

```
php -r "copy('https://phar.phpunit.de/phpunit.phar', 'phpunit.phar');"
php phpunit.phar
```

The `.phar` is gitignored; `phpunit.xml` and the `tests/` suite are committed.

## Project structure

```
public/            web root: index.php (list), keyword.php (detail), login/register/logout, api/refresh.php (AJAX), assets/
src/               app code (outside web root)
  bootstrap.php    loads all app classes (single include for entry points)
  config.php       configuration (SQLite path — no secrets)
  Database.php     PDO connection
  KeywordRepository.php   data access (parameterized SQL)
  UserRepository.php      user accounts data access
  Auth.php         session login/logout + requireLogin()
  Csrf.php         per-session CSRF token + verification
  RankingService.php      business logic (simulated refresh, 7-day trend)
  helpers.php      output escaping + view helpers
  views/           header, footer, list, detail, login, register
scripts/seed.php   seed command
migrations/schema.sql     table definitions (keywords, positions, users)
data/              SQLite database (gitignored)
```

## Security

- All SQL uses PDO prepared statements with bound parameters.
- All dynamic output is HTML-escaped (`htmlspecialchars` with `ENT_QUOTES`).
- Passwords are stored as bcrypt hashes (`password_hash` / `password_verify`).
- Every state-changing request (add/update/delete keyword, login, register,
  logout, AJAX refresh) requires a valid per-session CSRF token; AJAX sends it
  in the `X-CSRF-Token` header.
- No secrets: configuration holds only the database path.