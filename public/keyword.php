<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

Auth::requireLogin();

$repo = new KeywordRepository(Database::connection());

$id = (int) ($_GET['id'] ?? 0);
$keyword = $repo->find($id);

if ($keyword === null) {
    http_response_code(404);
    $pageTitle = 'Keyword not found';

    require __DIR__ . '/../src/views/header.php';
    echo '<section class="card"><p class="empty">Keyword not found.</p></section>';
    require __DIR__ . '/../src/views/footer.php';
    exit;
}

$history = $repo->history($id);
$breadcrumb = $keyword['keyword'];

require __DIR__ . '/../src/views/header.php';
require __DIR__ . '/../src/views/detail.php';
require __DIR__ . '/../src/views/footer.php';