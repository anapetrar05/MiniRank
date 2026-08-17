<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class SeederTest extends TestCase
{
    use CreatesInMemoryRepo;

    public function testSeedCreatesSixDemoKeywords(): void
    {
        $pdo = $this->makePdo();
        $repo = new KeywordRepository($pdo);
        $seeder = new Seeder($pdo, $repo);

        $result = $seeder->seed();

        $this->assertSame(['created' => 6, 'positions' => 180], $result);
        $this->assertCount(6, $repo->all());
    }

    public function testSeedCreatesThirtyDaysPerKeywordEndingToday(): void
    {
        $pdo = $this->makePdo();
        $repo = new KeywordRepository($pdo);
        $seeder = new Seeder($pdo, $repo);
        $seeder->seed();

        $today = (new DateTimeImmutable())->format('Y-m-d');
        $firstDay = (new DateTimeImmutable('-29 days'))->format('Y-m-d');

        foreach ($repo->all() as $keyword) {
            $history = $repo->history((int) $keyword['id']);
            $dates = array_column($history, 'date');

            $this->assertCount(30, $history, 'expected 30 days for ' . $keyword['keyword']);
            $this->assertSame($firstDay, $dates[0]);
            $this->assertSame($today, $dates[29]);
        }
    }

    public function testAllSeededPositionsAreWithinOneToHundred(): void
    {
        $pdo = $this->makePdo();
        $repo = new KeywordRepository($pdo);
        $seeder = new Seeder($pdo, $repo);
        $seeder->seed();

        foreach ($repo->all() as $keyword) {
            foreach ($repo->history((int) $keyword['id']) as $row) {
                $this->assertGreaterThanOrEqual(1, (int) $row['position']);
                $this->assertLessThanOrEqual(100, (int) $row['position']);
            }
        }
    }

    public function testSeedIsIdempotent(): void
    {
        $pdo = $this->makePdo();
        $repo = new KeywordRepository($pdo);
        $seeder = new Seeder($pdo, $repo);

        $first = $seeder->seed();
        $second = $seeder->seed();

        $this->assertSame(['created' => 6, 'positions' => 180], $first);
        $this->assertSame(['created' => 0, 'positions' => 0], $second);
        $this->assertCount(6, $repo->all());
        $this->assertSame(
            180,
            (int) $pdo->query('SELECT COUNT(*) FROM positions')->fetchColumn()
        );
    }
}