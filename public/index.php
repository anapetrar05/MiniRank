<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/KeywordRepository.php';
require_once __DIR__ . '/../src/RankingService.php';

$repo = new KeywordRepository(Database::connection());
$service = new RankingService();

$errors = [];
$flash = '';
$editId = (int) ($_GET['edit'] ?? 0);
$editKeyword = $editId > 0 ? $repo->find($editId) : null;

// --- Mutations (add / update / delete) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add';

    if ($action === 'add' || $action === 'update') {
        $keyword = trim((string) ($_POST['keyword'] ?? ''));
        $updateId = (int) ($_POST['id'] ?? 0);

        if ($keyword === '') {
            $errors[] = 'Keyword cannot be empty.';
        } elseif (mb_strlen($keyword) > 100) {
            $errors[] = 'Keyword must be 100 characters or fewer.';
        } else {
            $duplicate = $repo->findByKeyword($keyword);
            $isDuplicate = $duplicate !== null
                && ($action === 'add' || (int) $duplicate['id'] !== $updateId);

            if ($isDuplicate) {
                $errors[] = 'That keyword already exists.';
            }
        }

        if ($errors === []) {
            if ($action === 'add') {
                $repo->create($keyword);
                $flash = 'Keyword added.';
            } else {
                $repo->update($updateId, $keyword);
                $flash = 'Keyword updated.';
            }
            $editKeyword = null;
        } elseif ($action === 'update') {
            // Re-show the edit form so the user can fix the input.
            $editKeyword = $repo->find($updateId);
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $repo->delete($id);
            $flash = 'Keyword deleted.';
        }
    }
}

// --- Read (list, with optional text search) ---
$search = trim((string) ($_GET['q'] ?? ''));
$keywords = $search === '' ? $repo->all() : $repo->search($search);

$today = date('Y-m-d');
$sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));

$rows = [];
foreach ($keywords as $kw) {
    $id = (int) $kw['id'];
    $current = $repo->positionOn($id, $today);
    $previous = $repo->positionOn($id, $sevenDaysAgo);

    $rows[] = [
        'id' => $id,
        'keyword' => $kw['keyword'],
        'current' => $current,
        'trend' => $service->trend($current, $previous),
    ];
}

require __DIR__ . '/../src/views/header.php';
require __DIR__ . '/../src/views/list.php';
require __DIR__ . '/../src/views/footer.php';