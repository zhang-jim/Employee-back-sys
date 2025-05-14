<?php
namespace Routes;
class Router
{
    private $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];
    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }
    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }
    public function put($path, $callback)
    {
        $this->routes['PUT'][$path] = $callback;
    }
    public function delete($path, $callback)
    {
        $this->routes['DELETE'][$path] = $callback;
    }
    public function dispatch($url, $method)
    {
        $path = parse_url($url, PHP_URL_PATH);
        foreach ($this->routes[$method] as $route => $callback) {
            $pattern = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([a-zA-Z0-9_]+)', $route);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); 
                return call_user_func_array($callback, $matches);
            }
        }
        http_response_code(404);
    }
}
