<?php
class DepartmentController {
    private $departmentModel;

    public function __construct() {
        $this->departmentModel = new Department();
    }

    private function isAdmin() {
        $headers = getallheaders();
        $token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');
        $userId = Auth::validateToken($token);
        if (!$userId) return false;

        $user = (new User())->findById($userId);
        return $user && $user['role'] === 'admin';
    }

    public function create($data) {
        if (!$this->isAdmin()) {
            http_response_code(403);
            echo json_encode(["error" => "Only admin can create departments"]);
            return;
        }
        $this->departmentModel->create($data['name']);
        echo json_encode(["message" => "Department created successfully"]);
    }

    public function update($data) {
        if (!$this->isAdmin()) {
            http_response_code(403);
            echo json_encode(["error" => "Only admin can update departments"]);
            return;
        }
        $this->departmentModel->update($data['id'], $data['name']);
        echo json_encode(["message" => "Department updated successfully"]);
    }

    public function delete($data) {
        if (!$this->isAdmin()) {
            http_response_code(403);
            echo json_encode(["error" => "Only admin can delete departments"]);
            return;
        }
        $this->departmentModel->delete($data['id']);
        echo json_encode(["message" => "Department deleted successfully"]);
    }

    public function getAll() {
        $query = DB::getInstance()->getConnection()->query("SELECT * FROM departments");
        echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
    }

}