<?php

use MiniTwitter\Core\Router;

$router = new Router();

// Rotas de autenticação
$router->post('/login', 'AuthController@login');
$router->post('/register', 'AuthController@register');
$router->delete('/logout', 'AuthController@logout');

// Rotas estáticas
$router->get('/posts', 'PostController@index');
$router->post('/posts', 'PostController@store');

// Rotas Dinâmicas
$router->get('/posts/{id}', 'PostController@show');
$router->put('/posts/{id}', 'PostController@update');
$router->delete('/posts/{id}', 'PostController@destroy');
$router->post('/posts/{id}/like', 'LikeController@like');

return $router;