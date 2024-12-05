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
        try {
            // Begin transaction
            $this->conn->beginTransaction();

            // First update the users table
            $user_query = "UPDATE users 
                      SET email = :email
                      WHERE id = (SELECT user_id FROM students WHERE id = :student_id)";

            $user_stmt = $this->conn->prepare($user_query);
            $user_stmt->bindParam(':email', $data['email']);
            $user_stmt->bindParam(':student_id', $data['id']);
            $user_stmt->execute();

            // Then update the students table
            $student_query = "UPDATE " . $this->table . "
                         SET first_name = :first_name, 
                             last_name = :last_name, 
                             date_of_birth = :date_of_birth, 
                             grade_level = :grade_level
                         WHERE id = :id";

            $student_stmt = $this->conn->prepare($student_query);

            // Sanitize inputs
            $first_name = htmlspecialchars(strip_tags($data['first_name']));
            $last_name = htmlspecialchars(strip_tags($data['last_name']));
            $date_of_birth = htmlspecialchars(strip_tags($data['date_of_birth']));
            $grade_level = intval($data['grade_level']);

            // Bind parameters
            $student_stmt->bindParam(':id', $data['id']);
            $student_stmt->bindParam(':first_name', $first_name);
            $student_stmt->bindParam(':last_name', $last_name);
            $student_stmt->bindParam(':date_of_birth', $date_of_birth);
            $student_stmt->bindParam(':grade_level', $grade_level);

            $student_stmt->execute();

            // Commit transaction
            $this->conn->commit();
            return true;

        } catch (PDOException $e) {
            // Rollback transaction on error
            $this->conn->rollBack();
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

    public function getTotalStudents()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Error getting total students: " . $e->getMessage());
            return 0;
        }
    }
}