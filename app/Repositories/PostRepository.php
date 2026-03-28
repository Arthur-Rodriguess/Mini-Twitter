<?php

namespace MiniTwitter\Repositories;

use DateTime;
use Exception;
use MiniTwitter\Config\Database;
use MiniTwitter\Models\Post;
use PDO;
use PDOException;

class PostRepository
{   
    private PDO $pdo;
    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    /**
     * Salva um post no banco de dados
     * 
     * Escolhe por meio da existência ou não de um id se o post é criado ou atualizado
     * 
     * @param Post
     * @return bool
     */
    public function save(Post $post): void
    {
        if($post->getId() !== null) {
            $this->edit($post);
            return;
        }

        $this->create($post);
    }

    public function find(int $id): Post
    {
        try {
            $sql = "SELECT p.*, u.username
                    FROM posts p
                    JOIN users u ON p.user_id = u.id
                    WHERE p.id = ?
                    ORDER BY p.created_at DESC";
            $statement = $this->pdo->prepare($sql);
            $statement->execute([$id]);

            $postArray = $statement->fetch();

            if(!$postArray) {
                throw new Exception("Postagem não encontrada", 404);
            }

            return $this->hydrateList($postArray);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Erro ao buscar postagem no banco de dados", 500);
        }
    }

    /**
     * Busca todos os posts do banco
     * 
     * @param void
     * @return Post[]
     */
    public function findAll(): array
    {
        $sql = "SELECT p.id as post_id, p.*, u.username
                FROM posts p
                JOIN users u ON p.user_id = u.id
                ORDER BY p.created_at DESC";
        
        $statement = $this->pdo->query($sql);
        $postsArray = $statement->fetchAll();

        return array_map(fn($postArray) => $this->hydrateList($postArray),
        $postsArray);
    }

    /**
     * Busca posts de um usuário específico no banco
     * 
     * @param int
     * @return Post[]
     */
    public function findAllByUserId(int $userId): array
    {
        $sql = "SELECT p.*, u.username
                FROM posts p
                JOIN users u ON p.user_id = u.id
                WHERE user_id = ?
                ORDER BY p.created_at DESC";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            $userId
        ]);
        $postsArray = $statement->fetchAll();
        
        return array_map(fn($postArray) => $this->hydrateList($postArray),
        $postsArray);
    }

    /**
     * Remove um post no banco
     * 
     * @param int
     * @return bool
     */
    public function exclude(int $id, int $userId): void
    {   
        try {
            $sql = "DELETE FROM posts WHERE id = ? AND user_id = ?";
            $statement = $this->pdo->prepare($sql);
            $statement->execute([$id, $userId]);

            if($statement->rowCount() === 0) {
                throw new Exception("Postagem não encontrada ou não pertence ao usuário", 404);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Erro ao excluir postagem no banco de dados", 500);
        }
    }

    /**
     * Transforma um array em um objeto Post
     * 
     * @param array
     * @return Post
     */
    private function hydrateList(array $postArray): Post
    {
        $post = new Post(
            userId: $postArray['user_id'],
            content: $postArray['content']
        );

        $post->username = $postArray['username'] ?? '';
        $post->setCreatedAt(new DateTime($postArray['created_at'])->format('d-m-Y H:i'));
        $post->setId($postArray['id']);

        return $post;
    }

    /**
     * Cria um post no banco
     * 
     * @param Post
     * @return bool
     */
    private function create(Post $post): void
    {
        try {
            $sql = "INSERT INTO posts (user_id, content) VALUES (?, ?)";
            $statement = $this->pdo->prepare($sql);

            $statement->execute([
                $post->userId,
                $post->content
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Erro ao criar postagem no banco de dados", 500);
        }
    }

    /**
     * Atualiza um post no banco
     * 
     * @param Post
     * @return bool
     */
    private function edit(Post $post): void
    {
        try {
            $sql = "UPDATE posts SET content = ? WHERE id = ? AND user_id = ?";
            $statement = $this->pdo->prepare($sql);

            $statement->execute([
                $post->content,
                $post->getId(),
                $post->userId
            ]);

            if($statement->rowCount() === 0) {
                throw new Exception("Nenhuma alteração foi feita ou post não foi encontrado", 404);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Erro ao atualizar a postagem", 500);
        }
    }
}