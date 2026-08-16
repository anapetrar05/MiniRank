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