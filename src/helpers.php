<?php

declare(strict_types=1);

/**
 * Presentation helpers.
 */

/** HTML-escape a value before output (XSS protection). */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Build a filter-chip URL. Keeps any existing query params (text search,
 * position min/max) and sets or removes the trend filter.
 * Pass null for the trend to omit it (i.e. the "All" chip).
 */
function chip_url(array $query, ?string $trend): string
{
    if ($trend === null) {
        unset($query['trend']);
    } else {
        $query['trend'] = $trend;
    }

    return '/?' . http_build_query($query);
}

/** Renders the badge markup for a 7-day trend. */
function trend_badge(string $trend): string
{
    $labels = [
        'improved' => 'Improved &#8593;',
        'declined' => 'Declined &#8595;',
        'stable' => 'Stable',
    ];
    $classes = [
        'improved' => 'badge badge-good',
        'declined' => 'badge badge-bad',
        'stable' => 'badge badge-neutral',
    ];

    $label = $labels[$trend] ?? $labels['stable'];
    $class = $classes[$trend] ?? $classes['stable'];

    return '<span class="' . $class . '">' . $label . '</span>';
}

/**
 * Hand-rolled SVG line chart of a keyword's position history.
 * Position 1 (best) is at the top. Native <title> elements give
 * hover tooltips without any JavaScript.
 *
 * @param array $history rows from KeywordRepository::history(): [date, position]
 */
function position_line_chart(array $history): string
{
    $count = count($history);
    if ($count === 0) {
        return '';
    }

    $width = 640;
    $height = 200;
    $padL = 44;   // room for y-axis labels
    $padR = 12;
    $padT = 12;
    $padB = 26;   // room for x-axis date labels

    $plotW = $width - $padL - $padR;
    $plotH = $height - $padT - $padB;

    // Map each day to an (x, y) point. Y uses the full 1..100 range.
    $points = [];
    foreach ($history as $i => $row) {
        $position = (int) $row['position'];
        $x = $count === 1
            ? $padL + $plotW / 2
            : $padL + ($i / ($count - 1)) * $plotW;
        $y = $padT + (($position - 1) / 99) * $plotH;

        $points[] = [
            'x' => round($x, 1),
            'y' => round($y, 1),
            'date' => $row['date'],
            'position' => $position,
        ];
    }

    $baseline = $padT + $plotH;

    $svg = '<svg class="line-chart" viewBox="0 0 ' . $width . ' ' . $height
        . '" role="img" aria-label="Position history line chart" preserveAspectRatio="xMidYMid meet">';

    // Horizontal grid lines with position labels.
    foreach ([1, 25, 50, 75, 100] as $gridPos) {
        $gridY = $padT + (($gridPos - 1) / 99) * $plotH;
        $svg .= '<line class="grid-line" x1="' . $padL . '" y1="' . $gridY
            . '" x2="' . ($width - $padR) . '" y2="' . $gridY . '"/>';
        $svg .= '<text class="axis-label" x="' . ($padL - 6) . '" y="' . ($gridY + 4)
            . '" text-anchor="end">' . $gridPos . '</text>';
    }

    // A few date labels on the x axis (first, middle, last).
    $labelIndexes = array_unique([0, (int) floor(($count - 1) / 2), $count - 1]);
    foreach ($labelIndexes as $i) {
        $svg .= '<text class="axis-label" x="' . $points[$i]['x'] . '" y="' . ($height - 8)
            . '" text-anchor="middle">' . e($points[$i]['date']) . '</text>';
    }

    // Area under the line, then the line itself.
    $linePts = [];
    foreach ($points as $pt) {
        $linePts[] = $pt['x'] . ',' . $pt['y'];
    }
    $polyline = implode(' ', $linePts);

    $area = $points[0]['x'] . ',' . $baseline . ' ' . $polyline . ' '
        . $points[$count - 1]['x'] . ',' . $baseline;

    $svg .= '<polygon class="chart-area" points="' . $area . '"/>';
    $svg .= '<polyline class="chart-line" points="' . $polyline . '"/>';

    // Data points; the last one (today) is highlighted.
    foreach ($points as $i => $pt) {
        $class = $i === $count - 1 ? 'chart-point chart-point-today' : 'chart-point';
        $radius = $i === $count - 1 ? 4.5 : 3;
        $svg .= '<circle class="' . $class . '" cx="' . $pt['x'] . '" cy="' . $pt['y']
            . '" r="' . $radius . '">'
            . '<title>' . e($pt['date']) . ': ' . $pt['position'] . '</title>'
            . '</circle>';
    }

    return $svg . '</svg>';
}