<?php

namespace MiniTwitter\Middlewares;

use Exception;

class AuthMiddleware
{
    public function handle(): void
    {
        if(empty($_SESSION['user'])) {
            throw new Exception("Não autenticado", 401);
        }
    }
}