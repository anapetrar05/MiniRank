<?php

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

/**
 * All data access for keywords and their daily positions.
 * Every query is a prepared (parameterized) statement.
 */
final class KeywordRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** All keywords, alphabetically. */
    public function all(): array
    {
        $stmt = $this->db->query('SELECT id, keyword, created_at FROM keywords ORDER BY keyword ASC');
        return $stmt->fetchAll();
    }

    /** Keyword by id, or null when not found. */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, keyword, created_at FROM keywords WHERE id = :id');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /** Keyword by exact text, or null when not found. */
    public function findByKeyword(string $keyword): ?array
    {
        $stmt = $this->db->prepare('SELECT id, keyword, created_at FROM keywords WHERE keyword = :keyword');
        $stmt->execute(['keyword' => $keyword]);

        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /** Keywords matching a search term (case-insensitive substring). */
    public function search(string $term): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, keyword, created_at FROM keywords WHERE keyword LIKE :term ORDER BY keyword ASC'
        );
        $stmt->execute(['term' => '%' . $term . '%']);

        return $stmt->fetchAll();
    }

    /** Insert a keyword, returns its id. */
    public function create(string $keyword): int
    {
        $stmt = $this->db->prepare('INSERT INTO keywords (keyword) VALUES (:keyword)');
        $stmt->execute(['keyword' => $keyword]);

        return (int) $this->db->lastInsertId();
    }

    /** Rename a keyword. */
    public function update(int $id, string $keyword): void
    {
        $stmt = $this->db->prepare('UPDATE keywords SET keyword = :keyword WHERE id = :id');
        $stmt->execute(['id' => $id, 'keyword' => $keyword]);
    }

    /** Delete a keyword (positions cascade). */
    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM keywords WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /** Insert or overwrite one keyword/day position. */
    public function upsertPosition(int $keywordId, string $date, int $position): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO positions (keyword_id, date, position)
             VALUES (:keyword_id, :date, :position)
             ON CONFLICT (keyword_id, date) DO UPDATE SET position = :position'
        );
        $stmt->execute([
            'keyword_id' => $keywordId,
            'date' => $date,
            'position' => $position,
        ]);
    }

    /** Position on a given date, or null when there is no data. */
    public function positionOn(int $keywordId, string $date): ?int
    {
        $stmt = $this->db->prepare(
            'SELECT position FROM positions WHERE keyword_id = :keyword_id AND date = :date'
        );
        $stmt->execute(['keyword_id' => $keywordId, 'date' => $date]);

        $row = $stmt->fetch();

        return $row === false ? null : (int) $row['position'];
    }

    /** Full position history for a keyword, oldest first. */
    public function history(int $keywordId): array
    {
        $stmt = $this->db->prepare(
            'SELECT date, position FROM positions WHERE keyword_id = :keyword_id ORDER BY date ASC'
        );
        $stmt->execute(['keyword_id' => $keywordId]);

        return $stmt->fetchAll();
    }
}