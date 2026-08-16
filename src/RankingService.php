<?php

declare(strict_types=1);

/**
 * Business logic for ranking data.
 */
final class RankingService
{
    /**
     * Mean of the given positions, or null when there are none.
     */
    public function average(array $positions): ?float
    {
        $count = count($positions);

        return $count === 0 ? null : array_sum($positions) / $count;
    }

    /**
     * 7-day trend from window averages.
     *
     * Recent = average of the last 7 days, previous = average of the 7 days
     * before that. Comparing windows instead of two single days smooths out
     * daily noise. Position 1 is best, so a smaller recent average is an
     * improvement. When either window has no data the trend is "stable".
     *
     * @return string one of "improved", "declined", "stable"
     */
    public function trend(?float $recentAvg, ?float $previousAvg): string
    {
        if ($recentAvg === null || $previousAvg === null) {
            return 'stable';
        }

        $delta = $recentAvg - $previousAvg;

        if (abs($delta) < 0.001) {
            return 'stable';
        }

        return $delta < 0 ? 'improved' : 'declined';
    }

    /**
     * Compute the two 7-day window averages for a keyword:
     *   recent  = days [today-6 .. today]
     *   previous = days [today-13 .. today-7]
     *
     * @return array{recent: ?float, previous: ?float}
     */
    public function windowAverages(KeywordRepository $repo, int $keywordId, string $today): array
    {
        $today = new DateTimeImmutable($today);

        $recent = $repo->positionsBetween(
            $keywordId,
            $today->modify('-6 days')->format('Y-m-d'),
            $today->format('Y-m-d')
        );

        $previous = $repo->positionsBetween(
            $keywordId,
            $today->modify('-13 days')->format('Y-m-d'),
            $today->modify('-7 days')->format('Y-m-d')
        );

        return [
            'recent' => $this->average($recent),
            'previous' => $this->average($previous),
        ];
    }

    /**
     * Simulate "today's" ranking refresh for every keyword:
     * writes a new position (1-100) for today and returns the updated rows
     * with their 7-day trend, ready for JSON output.
     */
    public function refreshToday(KeywordRepository $repo): array
    {
        $today = date('Y-m-d');
        $updated = [];

        foreach ($repo->all() as $kw) {
            $id = (int) $kw['id'];
            $position = mt_rand(1, 100);

            $repo->upsertPosition($id, $today, $position);

            $avg = $this->windowAverages($repo, $id, $today);
            $updated[] = [
                'id' => $id,
                'keyword' => $kw['keyword'],
                'position' => $position,
                'trend' => $this->trend($avg['recent'], $avg['previous']),
            ];
        }

        return $updated;
    }
}