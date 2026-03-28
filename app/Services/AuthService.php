<?php

namespace MiniTwitter\Services;

use Exception;
use MiniTwitter\Models\User;
use MiniTwitter\Repositories\UserRepository;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function register(array $data): void
    {
        if(!$data['username'] || !$data['password'] || !$data['email']) {
            throw new Exception("Nome de usuário, email e senha são obrigatórios", 400);
        }

        if($this->userRepository->emailExists($data['email'])) {
            throw new Exception("Email já existente", 409);
        }

        $hash = password_hash($data['password'], PASSWORD_ARGON2ID);
    
        $user = new User(
            $data['username'],
            $data['bio'],
            $data['email'],
            $hash
        );

        // Essa chamada aqui pode gerar uma Exception viu, tratada lá no controller
        $this->userRepository->save($user);
    }

    public function login(array $data): User
    {
        // Verificação dos campos enviados
        if(empty($data['email']) || empty($data['password'])) {
            throw new Exception("Email e senha obrigatórios", 400);
        }

        $user = $this->userRepository->findByEmail($data['email']);
    
        // Se um usuário não existir ou a senha não coincidir
        if(!$user || !password_verify(trim($data['password']), $user->hash ?? '')) {
            throw new Exception("Email ou senha inválidos", 400);
        }
        
        return $user;
    }
}