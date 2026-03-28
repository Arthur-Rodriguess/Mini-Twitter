<?php

namespace MiniTwitter\Models;

use JsonSerializable;

class Post implements JsonSerializable
{
    private ?int $id = null;
    public readonly string $createdAt;
    public int $likesCount = 0;
    public bool $isLiked = false;
    public string $username = '';

    public function __construct(
        public readonly int $userId,
        public readonly string $content,
    )
    {
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'userId' => $this->userId,
            'username' => $this->username,
            'content' => $this->content,
            'createdAt' => $this->createdAt,
            'likesCount' => $this->likesCount,
            'isLiked' => $this->isLiked
        ];
    }
}