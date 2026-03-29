<?php

namespace MiniTwitter\Controllers;

use MiniTwitter\Core\Response;
use MiniTwitter\Services\PostService;

class PostController
{
    private PostService $postService;
    private ?int $currentUserId;

    public function __construct()
    {
        $this->postService = new PostService();
        $this->currentUserId = $_SESSION['user']->id ?? null;
    }

    // Mostra todas as postagens atuais
    // GET: /posts
    public function index(): void
    {
        $posts = $this->postService->getTimeLine($this->currentUserId);
        Response::json($posts);
    }

    // Busca um único post
    // GET: /posts/{id}
    public function show(int $id): void
    {
        $post = $this->postService->getOnePost($id, $this->currentUserId);
        Response::json($post);
    }

    // Cria uma postagem
    // POST: /posts
    public function store(): void
    {
        $data = json_decode(file_get_contents("php://input"), true) ?? [];
        $this->postService->createNewPost($data, $this->currentUserId);

        Response::json(["success" => "Postagem criada com sucesso"], 201);
    }

    // Exclui uma postagem
    // DELETE: /posts/{id}
    public function destroy(int $id): void
    {
        $this->postService->deletePost($id, $this->currentUserId);
        Response::json(["success" => "Postagem deletada com sucesso"]);
    }

    // Atualiza uma postagem
    // PATCH: /posts/{id}
    public function update(int $id): void
    {
        $data = json_decode(file_get_contents("php://input"), true) ?? [];
        $this->postService->updatePost($id, $data, $this->currentUserId);

        Response::json(["success" => "Postagem atualizada com sucesso"]);
    }
}