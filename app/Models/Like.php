<?php

namespace MiniTwitter\Models;

class Like
{
    private ?int $id = null;

    public function __construct(
        public readonly int $userId,
        public readonly int $postId
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}