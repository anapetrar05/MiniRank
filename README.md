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
- Docker setup: `docker compose up --build -d` runs app + SQLite database in
  one container (see "Docker" below)

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

## Docker

The project ships a `Dockerfile` + `docker-compose.yml` that run the whole app
(Apache + PHP 8.2 + SQLite) in a single container. SQLite is embedded, so the
"database" is the SQLite file kept on a persistent Docker volume
(`minirank-data`) — no separate DB container is needed.

Start it (first build may take a couple of minutes):

```
docker compose up --build -d
```

Open http://localhost:8080 and log in with `demo@example.com` / `demo1234`.

On first start the container seeds the database automatically (demo keywords +
demo account); later starts are no-ops. The volume keeps your data across
restarts. Port 8080 is used so it does not clash with the local
`php -S localhost:8000` dev server.

## Commands

| Command | Purpose |
|---|---|
| `php scripts/seed.php` | Create DB + schema + demo data (idempotent) |
| `php -S localhost:8000 -t public` | One-command start of the app (no Docker) |
| `php phpunit.phar` | Run the PHPUnit test suite |
| `docker compose up --build -d` | Build + start the app in Docker |
| `docker compose ps` | Show container status |
| `docker compose logs -f app` | Follow the app logs |
| `docker compose exec app php phpunit.phar` | Run the test suite inside the container |
| `docker compose down` | Stop the container (database volume is kept) |
| `docker compose down -v` | Stop and delete the volume — full reset, DB is re-seeded next start |

## Testing

Core logic (seeding, trend calculation and auth) is covered by PHPUnit tests in
`tests/`. They run against an in-memory SQLite database, so they never touch
your `data/` file.

Get the runner (once) and run:

```
php -r "copy('https://phar.phpunit.de/phpunit.phar', 'phpunit.phar');"
php phpunit.phar
```

With Docker the runner is already inside the image:

```
docker compose exec app php phpunit.phar
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
Dockerfile         container build (Apache + PHP + SQLite)
docker-compose.yml        one-command container start (port 8080)
docker/apache.conf        Apache vhost pointing at public/
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