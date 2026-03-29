<?php

namespace MiniTwitter\Services;

use Exception;
use MiniTwitter\Models\Post;
use MiniTwitter\Repositories\LikeRepository;
use MiniTwitter\Repositories\PostRepository;

class PostService
{
    private LikeRepository $likeRepository;
    private PostRepository $postRepository;

    public function __construct()
    {
        $this->likeRepository = new LikeRepository();
        $this->postRepository = new PostRepository();
    }

    /**
     * @param int|null
     * @return Post[]
     */
    public function getTimeLine(?int $currentUserId): array
    {
        if (!$currentUserId) {
            throw new Exception("Sem permissão", 403);
        }

        $posts = $this->postRepository->findAll();

        foreach($posts as $post) {
            $post->likesCount = $this->likeRepository->countByPost($post->getId());

            $post->isLiked = false;

            if ($currentUserId) {
                $post->isLiked = $this->likeRepository->exists($currentUserId, $post->getId());
            }
        }
        return $posts;
    }

    public function getOnePost(int $postId, ?int $currentUserId): Post
    {
        if(!$currentUserId) {
            throw new Exception("Sem permissão", 403);
        }

        if(!$postId) {
            throw new Exception("Id é obrigatório", 400);
        }

        return $this->postRepository->find($postId, $currentUserId);
    }

    public function createNewPost(array $data, ?int $currentUserId): void
    {
        $content = trim($data['content']);
        
        if(!$currentUserId) {
            throw new Exception("Sem permissão", 403);
        }

        if(!$content) {
            throw new Exception("Conteúdo é obrigatório", 400);
        }

        $post = new Post($currentUserId, $content);
        
        $this->postRepository->save($post);
    }

    public function deletePost(int $postId, ?int $currentUserId): void
    {
        if(!$currentUserId) {
            throw new Exception("Sem permissão", 403);
        }

        if(!$postId) {
            throw new Exception("Id é obrigatório", 400);
        }

        $this->postRepository->exclude($postId, $currentUserId);
    }

    public function updatePost(int $postId, array $data, ?int $currentUserId): void
    {
        $content = $data['content'];

        if(!$currentUserId) {
            throw new Exception("Sem permissão", 403);
        }

        if(!$postId) {
            throw new Exception("Id é obrigatório", 400);
        }

        if(!$content) {
            throw new Exception("Conteúdo é obrigatório", 400);
        }

        $post = new Post($currentUserId, $content);
        $post->setId($postId);

        $this->postRepository->save($post);
    }
}