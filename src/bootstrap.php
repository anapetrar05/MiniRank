<?php

declare(strict_types=1);

// Central place to load the application classes. Every public entry point
// (controllers and scripts) starts with a single require of this file.

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/KeywordRepository.php';
require_once __DIR__ . '/UserRepository.php';
require_once __DIR__ . '/RankingService.php';
require_once __DIR__ . '/Seeder.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/Csrf.php';
require_once __DIR__ . '/helpers.php';