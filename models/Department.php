<?php
class Department {
    private $pdo;

    public function __construct() {
        $this->pdo = DB::getInstance()->getConnection();
    }

    public function create($name) {
        $query = $this->pdo->prepare("INSERT INTO departments (name) VALUES (?)");
        $query->execute([$name]);
    }

    public function update($id, $name) {
        $query = $this->pdo->prepare("UPDATE departments SET name = ? WHERE id = ?");
        $query->execute([$name, $id]);
    }

    public function delete($id) {
        $query = $this->pdo->prepare("DELETE FROM departments WHERE id = ?");
        $query->execute([$id]);
    }

    public function findById($id) {
        $query = $this->pdo->prepare("SELECT * FROM departments WHERE id = ?");
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }
}