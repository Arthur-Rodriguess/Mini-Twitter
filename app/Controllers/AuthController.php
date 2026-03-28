<?php

namespace MiniTwitter\Controllers;

use Exception;
use MiniTwitter\Core\Response;
use MiniTwitter\Services\AuthService;
use MiniTwitter\Traits\HandlesExceptions;

class AuthController
{
    use HandlesExceptions;

    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService(); 
    }

    public function register()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true) ?? [];
            $this->authService->register($data);

            Response::json(["success" => "Usuário registrado com sucesso"], 201);
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

    public function login()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true) ?? [];
            $user = $this->authService->login($data);

            $_SESSION['user'] = (object) [
                "id" => $user->getId(),
                "username" => $user->username,
                "bio" => $user->bio
            ];
            
            Response::json(["success" => "Usuário logado com sucesso"]);            
        } catch(Exception $e) {
            $this->handleException($e);
        }
    }

    public function logout()
    {
        if (!session_destroy()) {
            Response::json(["error" => "Erro ao realizar logout"], 500);
            return;
        }

        Response::json(["success" => "Usuário deslogado com sucesso"]);
    }
}