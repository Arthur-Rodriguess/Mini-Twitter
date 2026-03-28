<?php

namespace MiniTwitter\Repositories;

use Exception;
use MiniTwitter\Config\Database;
use MiniTwitter\Models\User;
use PDO;
use PDOException;

class UserRepository
{
    private PDO $pdo;
    
    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    /**
     * Salva usuário no banco de dados
     * 
     * Escolhe por meio da existência ou não de um id se o usuário é criado ou atualizado
     * 
     * @param User
     * @return bool
     */
    public function save(User $user): void
    {
        if($user->getId() !== null) {
            $this->edit($user);
            return;
        }
        
        $this->create($user);
    }

    /**
     * Cria um usuário no banco
     * 
     * @param User
     * @return bool
     */
    private function create(User $user): void
    {
        try {
            $sql = "INSERT INTO users (username, email, bio, password) VALUES (?, ?, ?, ?)";
            $statement = $this->pdo->prepare($sql);
            $statement->execute([
                $user->username,
                $user->email,
                $user->bio ?? '',
                $user->hash
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Erro ao criar usuário no banco de dados", 500);
        }
    }

    /**
     * Atualiza um usuário no banco
     * 
     * @param User
     * @return bool
     */
    private function edit(User $user): void
    {
        try {
            $sql = "UPDATE users SET username = ?, email =?, bio = ? WHERE id = ?";
            $statement = $this->pdo->prepare($sql);
            $statement->execute([
                $user->username,
                $user->email,
                $user->bio ?? '',
                $user->getId()
            ]);

            if($statement->rowCount() === 0) {
                throw new Exception("Nenhuma alteração foi feita ou usuário não foi encontrado", 404);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Erro ao atualizar usuário", 500);
        }
    }

    /**
     * Remove um usuário no banco
     * 
     * @param int
     * @return bool
     */
    public function exclude(int $userId): bool
    {
        $sql = "DELETE FROM users WHERE id = ?";
        $statement = $this->pdo->prepare($sql);
        return $statement->execute([$userId]);
    }

    /**
     * Verifica se um email existe no banco
     * 
     * @param string
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([$email]);
        return (bool) $statement->fetchColumn();
    }

    /**
     * Busca um usuário através de um email
     * 
     * @param string
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([$email]);

        $userArray = $statement->fetch();

        if(!$userArray) {
            return null;
        }

        return $this->hydrateList($userArray);
    }

    /**
     * Transforma um array em um objeto User
     * 
     * @param array
     * @return User
     */
    private function hydrateList(array $userArray): User
    {
        $user = new User(
            $userArray['username'],
            $userArray['bio'] ?? '',
            $userArray['email'],
            $userArray['password']
        );

        $user->setId($userArray['id']);

        return $user;
    }
}