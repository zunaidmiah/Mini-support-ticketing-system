<?php

class TicketController {
    private $pdo;

    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

    private function getUserIdFromToken() {
        $headers = getallheaders();
        $token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');
        return Auth::validateToken($token);
    }

    public function submit($data) {
        $userId = $this->getUserIdFromToken();
        if (!$userId) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized Request"]);
            return;
        }
        $createdAt = date('Y-m-d H:i:s');
        $query = $this->pdo->prepare("INSERT INTO tickets (title, description, status, user_id, department_id, created_at) VALUES (?, ?, 'open', ?, ?, ?)");
        $query->execute([$data['title'], $data['description'], $userId, $data['department_id'], $createdAt]);
        echo json_encode(["message" => "Ticket submitted successfully"]);
    }

    public function assign($data) {
        $userId = $this->getUserIdFromToken();
        if (!$userId) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized Request"]);
            return;
        }

        $query = $this->pdo->prepare("UPDATE tickets SET assigned_agent_id = ? WHERE id = ?");
        $query->execute([$userId, $data['ticket_id']]);
        echo json_encode(["message" => "Ticket assigned to you successfully"]);
    }

    public function changeStatus($data) {
        $userId = $this->getUserIdFromToken();
        if (!$userId) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized Request"]);
            return;
        }

        $query = $this->pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
        $query->execute([$data['status'], $data['ticket_id']]);
        echo json_encode(["message" => "Ticket status updated successfully"]);
    }

    public function getAll() {
        $query = DB::getInstance()->getConnection()->query("
            SELECT t.*, u.name AS user_name, d.name AS department_name
            FROM tickets t
            JOIN users u ON t.user_id = u.id
            JOIN departments d ON t.department_id = d.id
        ");
        echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
    }


    public function addNote($data) {
        $userId = $this->getUserIdFromToken();
        if (!$userId) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized Request"]);
            return;
        }
        $createdAt = date('Y-m-d H:i:s');
        $query = $this->pdo->prepare("INSERT INTO ticket_notes (ticket_id, user_id, note, created_at) VALUES (?, ?, ?, ?)");
        $query->execute([$data['ticket_id'], $userId, $data['note'], $createdAt]);
        echo json_encode(["message" => "Note added to ticket successfully"]);
    }

    public function getNotes() {
        $query = DB::getInstance()->getConnection()->query("
            SELECT tn.*, u.name AS user_name
            FROM ticket_notes tn
            JOIN users u ON tn.user_id = u.id
        ");
        echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));
    }

}
