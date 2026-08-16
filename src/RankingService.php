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

    /**
     * Simulate "today's" ranking refresh for every keyword:
     * writes a new position (1-100) for today and returns the updated rows
     * with their 7-day trend, ready for JSON output.
     */
    public function refreshToday(KeywordRepository $repo): array
    {
        $today = date('Y-m-d');
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
        $updated = [];

        foreach ($repo->all() as $kw) {
            $id = (int) $kw['id'];
            $position = mt_rand(1, 100);

            $repo->upsertPosition($id, $today, $position);

            $updated[] = [
                'id' => $id,
                'keyword' => $kw['keyword'],
                'position' => $position,
                'trend' => $this->trend($position, $repo->positionOn($id, $sevenDaysAgo)),
            ];
        }

        return $updated;
    }
}