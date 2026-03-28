<?php

namespace MiniTwitter\Services;

use MiniTwitter\Models\Like;
use MiniTwitter\Repositories\LikeRepository;

class LikeService
{
    private LikeRepository $likeRepository;

    public function __construct()
    {
        $this->likeRepository = new LikeRepository();
    }

    public function likePost(int $postId, ?int $currentUserId): void
    {
        $like = new Like($currentUserId, $postId);

        if($this->likeRepository->exists($currentUserId, $postId)) {
            $this->likeRepository->remove($currentUserId, $postId);
        }

        $this->likeRepository->add($like);
    }
}