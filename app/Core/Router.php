<?php

namespace MiniTwitter\Core;

class Router
{
    private $routes = [];

    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    public function put($uri, $action)
    {
        $this->addRoute('PUT', $uri, $action);
    }

    public function delete($uri, $action)
    {
        $this->addRoute('DELETE', $uri, $action);
    }

    private function addRoute(string $method, string $uri, string $action): void
    {
        $this->routes[$method][$uri] = $action;
    }

    public function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') ?: '/';

        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $routeUri => $action) {
            // Transforma {id} em regex
            $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $routeUri);
            
            $pattern = "#^" . rtrim($pattern, '/') . "$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
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