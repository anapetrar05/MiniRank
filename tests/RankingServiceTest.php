<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class RankingServiceTest extends TestCase
{
    use CreatesInMemoryRepo;

    private RankingService $service;

    protected function setUp(): void
    {
        $this->service = new RankingService();
    }

    public function testLowerPositionIsImprovement(): void
    {
        $this->assertSame('improved', $this->service->trend(42, 48));
        $this->assertSame('improved', $this->service->trend(1, 100));
    }

    public function testHigherPositionIsDecline(): void
    {
        $this->assertSame('declined', $this->service->trend(55, 20));
        $this->assertSame('declined', $this->service->trend(100, 1));
    }

    public function testEqualPositionsAreStable(): void
    {
        $this->assertSame('stable', $this->service->trend(30, 30));
    }

    public function testMissingDataIsStable(): void
    {
        $this->assertSame('stable', $this->service->trend(null, 30));
        $this->assertSame('stable', $this->service->trend(30, null));
        $this->assertSame('stable', $this->service->trend(null, null));
    }

    public function testRefreshTodayReturnsEveryKeywordWithValidData(): void
    {
        $repo = $this->makeRepo(seeded: true);

        $updated = $this->service->refreshToday($repo);

        $this->assertCount(6, $updated);
        foreach ($updated as $row) {
            $this->assertArrayHasKey('id', $row);
            $this->assertArrayHasKey('keyword', $row);
            $this->assertArrayHasKey('position', $row);
            $this->assertArrayHasKey('trend', $row);

            $this->assertGreaterThanOrEqual(1, $row['position']);
            $this->assertLessThanOrEqual(100, $row['position']);
            $this->assertContains($row['trend'], ['improved', 'declined', 'stable']);
        }
    }

    public function testRefreshTodayPersistsTodayPosition(): void
    {
        $repo = $this->makeRepo(seeded: true);

        $updated = $this->service->refreshToday($repo);
        $today = date('Y-m-d');

        foreach ($updated as $row) {
            $this->assertSame(
                $row['position'],
                $repo->positionOn((int) $row['id'], $today),
                'refresh should persist today\'s position for ' . $row['keyword']
            );
        }
    }
}