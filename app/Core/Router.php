<?php

namespace MiniTwitter\Core;

use MiniTwitter\Middlewares\AuthMiddleware;

class Router
{
    private $routes = [];

    public function get($uri, $action, $middlewares = [])
    {
        $this->addRoute('GET', $uri, $action, $middlewares);
    }

    public function post($uri, $action, $middlewares = [])
    {
        $this->addRoute('POST', $uri, $action, $middlewares);
    }

    public function put($uri, $action, $middlewares = [])
    {
        $this->addRoute('PUT', $uri, $action, $middlewares);
    }

    public function delete($uri, $action, $middlewares = [])
    {
        $this->addRoute('DELETE', $uri, $action, $middlewares);
    }

    private function addRoute(string $method, string $uri, string $action, array $middlewares = []): void
    {
        $this->routes[$method][$uri] = [
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    public function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') ?: '/';

        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $routeUri => $route) {
            // Transforma {id} em regex
            $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $routeUri);
            
            $pattern = "#^" . rtrim($pattern, '/') . "$#";

            if (preg_match($pattern, $uri, $matches)) {
                foreach ($route['middlewares'] as $middleware) {
                    $instance = new $middleware();
                    $instance->handle();
                }
                array_shift($matches);
                $action = $route['action'];
                [$controller, $methodAction] = explode('@', $action);

                $controllerClass = "MiniTwitter\\Controllers\\$controller";

                if (!class_exists($controllerClass)) {
                    Response::json(["error" => "Controller não encontrado"], 500);
                    return;
                }

                $controllerInstance = new $controllerClass();

                if (!method_exists($controllerInstance, $methodAction)) {
                    Response::json(["error" => "Método não encontrado"], 500);
                    return;
                }

                $controllerInstance->$methodAction(...$matches);
            }
        }
    }
}