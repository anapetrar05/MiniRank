<?php $pageTitle = 'Keywords'; ?>

<?php if ($editKeyword): ?>
<section class="card">
    <h2>Edit keyword</h2>
    <form method="post" class="inline-form">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= e($editKeyword['id']) ?>">
        <input type="text" name="keyword" value="<?= e($editKeyword['keyword']) ?>"
               required maxlength="100" placeholder="e.g. seo guide">
        <button type="submit">Save</button>
        <a href="/" class="btn-link">Cancel</a>
    </form>
</section>
<?php else: ?>
<section class="card">
    <h2>Add keyword</h2>
    <form method="post" class="inline-form">
        <input type="hidden" name="action" value="add">
        <input type="text" name="keyword" required maxlength="100"
               placeholder="e.g. seo guide">
        <button type="submit">Add</button>
    </form>
</section>
<?php endif; ?>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $error): ?>
            <p><?= e($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($flash): ?>
    <div class="alert alert-success"><p><?= e($flash) ?></p></div>
<?php endif; ?>

<section class="card">
    <div class="list-toolbar">
        <form method="get" class="search-form">
            <input type="search" name="q" value="<?= e($search) ?>"
                   placeholder="Search keywords…">
            <?php if ($trend !== null): ?><input type="hidden" name="trend" value="<?= e($trend) ?>"><?php endif; ?>
            <?php if ($posMin !== null): ?><input type="hidden" name="pos_min" value="<?= e((string) $posMin) ?>"><?php endif; ?>
            <?php if ($posMax !== null): ?><input type="hidden" name="pos_max" value="<?= e((string) $posMax) ?>"><?php endif; ?>
            <button type="submit">Search</button>
        </form>
        <button type="button" id="refresh-btn">Refresh positions</button>
    </div>

    <?php
    $query = [];
    if ($search !== '') {
        $query['q'] = $search;
    }
    if ($trend !== null) {
        $query['trend'] = $trend;
    }
    if ($posMin !== null) {
        $query['pos_min'] = $posMin;
    }
    if ($posMax !== null) {
        $query['pos_max'] = $posMax;
    }
    ?>
    <div class="filters">
        <div class="filter-group">
            <span class="filter-label">Trend:</span>
            <a class="chip<?= $trend === null ? ' chip-active' : '' ?>"
               href="<?= e(chip_url($query, null)) ?>">All</a>
            <?php foreach (['improved' => 'Improved', 'declined' => 'Declined', 'stable' => 'Stable'] as $key => $label): ?>
                <a class="chip<?= $trend === $key ? ' chip-active' : '' ?>"
                   href="<?= e(chip_url($query, $key)) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="filter-group">
            <span class="filter-label">Position:</span>
            <form method="get" class="pos-filter">
                <input type="hidden" name="q" value="<?= e($search) ?>">
                <?php if ($trend !== null): ?><input type="hidden" name="trend" value="<?= e($trend) ?>"><?php endif; ?>
                <input type="number" name="pos_min" min="1" max="100" placeholder="min"
                       value="<?= $posMin === null ? '' : e((string) $posMin) ?>">
                <span class="pos-sep">&ndash;</span>
                <input type="number" name="pos_max" min="1" max="100" placeholder="max"
                       value="<?= $posMax === null ? '' : e((string) $posMax) ?>">
                <button type="submit">Filter</button>
                <?php if ($posMin !== null || $posMax !== null): ?>
                    <?php $clear = $query; unset($clear['pos_min'], $clear['pos_max']); ?>
                    <a class="chip" href="<?= e('/?' . http_build_query($clear)) ?>">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if (!$rows): ?>
        <p class="empty">No keywords found.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="keyword-table">
                <thead>
                    <tr>
                        <th>Keyword</th>
                        <th>Position</th>
                        <th>7-day trend</th>
                        <th class="actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr data-id="<?= e($row['id']) ?>">
                            <td data-label="Keyword">
                                <a href="/keyword.php?id=<?= e($row['id']) ?>"><?= e($row['keyword']) ?></a>
                            </td>
                            <td data-label="Position" class="td-position">
                                <?= $row['current'] === null ? '&mdash;' : e((string) $row['current']) ?>
                            </td>
                            <td data-label="7-day trend" class="td-trend"><?= trend_badge($row['trend']) ?></td>
                            <td data-label="Actions" class="actions-col">
                                <a href="/?edit=<?= e($row['id']) ?>" class="btn-link">Edit</a>
                                <form method="post" class="inline-form"
                                      onsubmit="return confirm('Delete this keyword?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                    <button type="submit" class="btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>