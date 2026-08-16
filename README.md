# MiniRank

A small full-stack keyword position tracker. Tracks daily search-engine positions
for keywords of a single configured website. Positions are **simulated** locally —
no external search APIs are used.

## Stack

- PHP 8+ (PDO + SQLite)
- HTML / CSS / vanilla JS
- No frameworks, no runtime dependencies

## Requirements

- PHP 8+ with the `pdo_sqlite` extension enabled

## Setup

1. Install the demo database (creates schema and seeds ~30 days of positions):

   ```
   php scripts/seed.php
   ```

2. Start the built-in dev server (one command):

   ```
   php -S localhost:8000 -t public
   ```

3. Open http://localhost:8000

> The full setup/one-command instructions will be finalized as development completes.