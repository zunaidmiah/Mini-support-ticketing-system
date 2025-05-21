<?php
require 'core/Router.php';
require 'core/DB.php';
require 'core/Auth.php';

spl_autoload_register(function ($class) {
    foreach (["controllers", "models", "core"] as $folder) {
        $path = "$folder/$class.php";
        if (file_exists($path)) require_once $path;
    }
});

$router = new Router();

$router->get('/', [UserController::class, 'index']);

$router->get('/users', [UserController::class, 'getAll']);
$router->post('/register', [UserController::class, 'register']);
$router->post('/login', [UserController::class, 'login']);
$router->post('/logout', [UserController::class, 'logout']);

$router->get('/departments', [DepartmentController::class, 'getAll']);
$router->post('/departments', [DepartmentController::class, 'create']);
$router->put('/departments', [DepartmentController::class, 'update']);
$router->delete('/departments', [DepartmentController::class, 'delete']);

$router->get('/tickets', [TicketController::class, 'getAll']);
$router->post('/tickets', [TicketController::class, 'submit']);
$router->post('/tickets/assign', [TicketController::class, 'assign']);
$router->post('/tickets/status', [TicketController::class, 'changeStatus']);

$router->get('/tickets/notes', [TicketController::class, 'getNotes']);
$router->post('/tickets/notes', [TicketController::class, 'addNote']);

$router->run();