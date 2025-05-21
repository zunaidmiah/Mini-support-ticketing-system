<?php
class UserController {
    private $pdo;

    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

    public function index(){
        echo "Our Mini support ticketing backend system is running now!";
    }

    public function register($data) {
        $query = $this->pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $query->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['role'] ?? 'agent'
        ]);
        echo json_encode(["message" => "User registeration successfully"]);
    }

    public function login($data) {
        $query = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $query->execute([$data['email']]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($data['password'], $user['password_hash'])) {
            $token = Auth::generateToken($user['id']);
            echo json_encode(["token" => $token, "user" => ["id" => $user['id'], "name" => $user['name'], "role" => $user['role']]]);
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Credentials didn't matched!"]);
        }
    }

    public function logout($data) {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        $token = str_replace('Bearer ', '', $authHeader);
        Auth::deleteToken($token);
        echo json_encode(["message" => "Logged out successfully!"]);
    }

    public function getAll() {
        $user = new User();
        $users = $user->findAll();
        echo json_encode($users);
    }
}
