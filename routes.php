<?php

use MiniTwitter\Core\Router;
use MiniTwitter\Middlewares\AuthMiddleware;

$router = new Router();

// Rotas de autenticação
$router->post('/login', 'AuthController@login');
$router->post('/register', 'AuthController@register');
$router->delete('/logout', 'AuthController@logout', [AuthMiddleware::class]);

// Rotas estáticas
$router->get('/posts', 'PostController@index', [AuthMiddleware::class]);
$router->post('/posts', 'PostController@store', [AuthMiddleware::class]);

// Rotas Dinâmicas
$router->get('/posts/{id}', 'PostController@show', [AuthMiddleware::class]);
$router->put('/posts/{id}', 'PostController@update', [AuthMiddleware::class]);
$router->delete('/posts/{id}', 'PostController@destroy', [AuthMiddleware::class]);
$router->post('/posts/{id}/like', 'LikeController@like', [AuthMiddleware::class]);

return $router;