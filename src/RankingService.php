<?php

declare(strict_types=1);

/**
 * Business logic for ranking data.
 */
final class RankingService
{
    /**
     * Compare a current position against the position ~7 days earlier.
     * Position 1 is best, so a smaller number means an improvement.
     *
     * @return string one of "improved", "declined", "stable"
     */
    public function trend(?int $current, ?int $previous): string
    {
        if ($current === null || $previous === null || $current === $previous) {
            return 'stable';
        }

        return $current < $previous ? 'improved' : 'declined';
    }
}