<?php

class Router {
    private $routes = [];

    public function add($method, $uri, $handler) {
        $this->routes[strtoupper($method)][$uri] = $handler;
    }

    public function get($uri, $handler) {
        $this->add('GET', $uri, $handler);
    }

    public function post($uri, $handler) {
        $this->add('POST', $uri, $handler);
    }

    public function put($uri, $handler) {
        $this->add('PUT', $uri, $handler);
    }

    public function delete($uri, $handler) {
        $this->add('DELETE', $uri, $handler);
    }

    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        if (isset($this->routes[$method][$uri])) {
            [$class, $func] = $this->routes[$method][$uri];
            $controller = new $class();
            $controller->$func($input);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Route not found"]);
        }
    }
}