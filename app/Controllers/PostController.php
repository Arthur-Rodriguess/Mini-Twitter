<?php

namespace MiniTwitter\Controllers;

use Exception;
use MiniTwitter\Core\Response;
use MiniTwitter\Services\PostService;
use MiniTwitter\Traits\HandlesExceptions;

class PostController
{
    use HandlesExceptions;

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
        try {
            $posts = $this->postService->getTimeLine($this->currentUserId);
            Response::json($posts);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    // Busca um único post
    // GET: /posts/{id}
    public function show(int $id): void
    {
        try {
            $post = $this->postService->getOnePost($id, $this->currentUserId);
            Response::json($post);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    // Cria uma postagem
    // POST: /posts
    public function store(): void
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true) ?? [];
            $this->postService->createNewPost($data, $this->currentUserId);

            Response::json(["success" => "Postagem criada com sucesso"], 201);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    // Exclui uma postagem
    // DELETE: /posts/{id}
    public function destroy(int $id): void
    {
        try {
            $this->postService->deletePost($id, $this->currentUserId);
            Response::json(["success" => "Postagem deletada com sucesso"]);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    // Atualiza uma postagem
    // PATCH: /posts/{id}
    public function update(int $id): void
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true) ?? [];
            $this->postService->updatePost($id, $data, $this->currentUserId);

            Response::json(["success" => "Postagem atualizada com sucesso"]);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }
}