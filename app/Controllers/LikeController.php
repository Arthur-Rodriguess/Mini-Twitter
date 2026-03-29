<?php

namespace MiniTwitter\Controllers;

use MiniTwitter\Core\Response;
use MiniTwitter\Services\LikeService;

class LikeController
{
    private LikeService $likeService;
    private ?int $currentUserId;

    public function __construct()
    {
        $this->likeService = new LikeService();
        $this->currentUserId = $_SESSION['user']->id ?? null;
    }

    public function like(int $id): void
    {
        $this->likeService->likePost($id, $this->currentUserId);
        Response::json(["success" => "Postagem curtida com sucesso"]);
    }
}