<?php $pageTitle = $keyword['keyword']; ?>

<section class="card">
    <h2><?= e($keyword['keyword']) ?></h2>

    <?php
    $days = count($history);
    $latest = $days > 0 ? (int) $history[$days - 1]['position'] : null;
    $best = $days > 0 ? (int) min(array_column($history, 'position')) : null;
    ?>
    <p class="meta">
        Tracked since <?= e($keyword['created_at']) ?> &middot;
        <?= e((string) $days) ?> days &middot;
        latest position <strong><?= $latest === null ? '&mdash;' : e((string) $latest) ?></strong> &middot;
        best <?= $best === null ? '&mdash;' : e((string) $best) ?>
    </p>
</section>

<section class="card">
    <h2>Position history</h2>

    <?php if (!$history): ?>
        <p class="empty">No position data yet.</p>
    <?php else: ?>
        <div class="chart" aria-hidden="true">
            <?php foreach ($history as $row): ?>
                <?php $height = 101 - (int) $row['position']; ?>
                <div class="chart-bar"
                     style="height: <?= e((string) $height) ?>%"
                     title="<?= e($row['date']) ?>: <?= e((string) $row['position']) ?>"></div>
            <?php endforeach; ?>
        </div>

        <div class="table-wrap">
            <table class="keyword-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Position</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_reverse($history) as $row): ?>
                        <tr>
                            <td data-label="Date"><?= e($row['date']) ?></td>
                            <td data-label="Position"><?= e((string) $row['position']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>