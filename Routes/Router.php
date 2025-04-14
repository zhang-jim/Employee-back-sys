<?php
class Router
{
    private $routes = [
        'GET' => [],
        'POST' => [],
    ];
    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }
    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }
    public function dispatch($url, $method)
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (isset($this->routes[$method][$path])) {
            call_user_func($this->routes[$method][$path]);
        } else {
            http_response_code(404);
        }
    }
}
