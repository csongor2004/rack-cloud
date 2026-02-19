<?php
namespace App\Core;

class Router
{
    private $routes = [];

    public function add($route, $callback)
    {
        $this->routes[$route] = $callback;
    }

    public function dispatch($url)
    {
        // Alapértelmezett útvonal, ha üres
        $url = $url ?: 'home';

        if (isset($this->routes[$url])) {
            call_user_func($this->routes[$url]);
        } else {
            http_response_code(404);
            echo "404 - Az oldal nem található!";
        }
    }
}