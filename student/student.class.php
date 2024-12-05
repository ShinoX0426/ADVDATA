<?php
class Student
{
    private $conn;
    private $table = 'students';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllStudents()
    {
        $query = "SELECT s.*, u.email, u.username 
                  FROM " . $this->table . " s
                  JOIN users u ON s.user_id = u.id
                  ORDER BY s.last_name, s.first_name";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching students: " . $e->getMessage());
            return false;
        }
    }

    public function getStudentById($id)
    {
        $query = "SELECT s.*, u.email, u.username 
                  FROM " . $this->table . " s
                  JOIN users u ON s.user_id = u.id
                  WHERE s.id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching student: " . $e->getMessage());
            return false;
        }
    }

    public function createStudent($data)
    {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, first_name, last_name, date_of_birth, grade_level) 
                  VALUES (:user_id, :first_name, :last_name, :date_of_birth, :grade_level)";

        try {
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':date_of_birth', $data['date_of_birth']);
            $stmt->bindParam(':grade_level', $data['grade_level']);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error creating student: " . $e->getMessage());
            return false;
        }
    }

    public function updateStudent($data)
    {
        $query = "UPDATE " . $this->table . "
                  SET first_name = :first_name, 
                      last_name = :last_name, 
                      date_of_birth = :date_of_birth, 
                      grade_level = :grade_level
                  WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':id', $data['id']);
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':date_of_birth', $data['date_of_birth']);
            $stmt->bindParam(':grade_level', $data['grade_level']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating student: " . $e->getMessage());
            return false;
        }
    }

    public function deleteStudent($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting student: " . $e->getMessage());
            return false;
        }
    }
}