<?php

namespace MiniTwitter\Repositories;

use MiniTwitter\Config\Database;
use MiniTwitter\Models\Like;
use PDO;

class LikeRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function add(Like $like): bool
    {
        $sql = "INSERT IGNORE INTO likes (user_id, post_id) VALUES (?, ?)";
        $statement = $this->pdo->prepare($sql);
        return $statement->execute([
            $like->userId,
            $like->postId
        ]);
    }

    public function remove(int $userId, int $postId): bool
    {
        $sql = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
        $statement = $this->pdo->prepare($sql);
        return $statement->execute([
            $userId,
            $postId
        ]);
    }

    public function countByPost(int $postId): int
    {
        $sql = "SELECT COUNT(*) FROM likes WHERE post_id = ?";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([$postId]);
        return (int) $statement->fetchColumn();
    }

    public function exists(int $userId, int $postId): bool
    {
        $sql = "SELECT 1 FROM likes WHERE user_id = ? AND post_id = ? LIMIT 1";

        $statement = $this->pdo->prepare($sql);
        $statement->execute([$userId, $postId]);

        return (bool) $statement->fetchColumn();
    }
}