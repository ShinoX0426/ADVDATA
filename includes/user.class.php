<?php
require_once 'database.php';

class User
{
    private $conn;
    private $table = 'users';

    public $id;
    public $username;
    public $email;
    public $password;
    public $role;
    public $full_name;

    public function __construct($db)
    {
        $this->conn = $db;
    }


    public function create()
    {
        $query = "INSERT INTO " . $this->table . " SET username=:username, email=:email, password=:password, role=:role, full_name=:full_name";
        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':full_name', $this->full_name);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . "
              SET username = :username, 
                  email = :email, 
                  role = :role,
                  full_name = :full_name
              WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    public function delete()
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get total number of users
    public function getTotalUsers()
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Get total number of users by role
    public function getTotalUsersByRole($role)
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE role = :role";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Get users by role
    public function getUsersByRole($role)
    {
        $query = "SELECT id, username, email, full_name FROM {$this->table} WHERE role = :role ORDER BY full_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add class
    public function addClass($class_data)
    {
        $query = "INSERT INTO classes (name, grade_level, section, teacher_id) 
              VALUES (:name, :grade_level, :section, :teacher_id)";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $name = htmlspecialchars(strip_tags($class_data['name']));
        $grade_level = htmlspecialchars(strip_tags($class_data['grade_level']));
        $section = htmlspecialchars(strip_tags($class_data['section']));
        $teacher_id = htmlspecialchars(strip_tags($class_data['teacher_id']));

        // Bind parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':grade_level', $grade_level);
        $stmt->bindParam(':section', $section);
        $stmt->bindParam(':teacher_id', $teacher_id);

        try {
            // First, ensure the classes table has the correct structure
            $alterTable = "ALTER TABLE classes 
                      ADD COLUMN IF NOT EXISTS grade_level INT,
                      ADD COLUMN IF NOT EXISTS section VARCHAR(255)";
            $this->conn->exec($alterTable);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error adding class: " . $e->getMessage());
            return false;
        }
    }
    // Update class
    public function updateClass($class_data)
    {
        $query = "UPDATE classes 
                 SET name = :name, 
                     grade_level = :grade_level, 
                     section = :section, 
                     teacher_id = :teacher_id 
                 WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $class_data['id']);
        $stmt->bindParam(':name', $class_data['name']);
        $stmt->bindParam(':grade_level', $class_data['grade_level']);
        $stmt->bindParam(':section', $class_data['section']);
        $stmt->bindParam(':teacher_id', $class_data['teacher_id']);

        return $stmt->execute();
    }

    // Delete class
    public function deleteClass($class_id)
    {
        $query = "DELETE FROM classes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $class_id);
        return $stmt->execute();
    }

    // Get all classes
    public function getAllClasses()
    {
        // First, let's create the classes table if it doesn't exist
        $createTable = "CREATE TABLE IF NOT EXISTS classes (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        teacher_id INT,
        FOREIGN KEY (teacher_id) REFERENCES users(id)
    )";

        $this->conn->exec($createTable);

        // Now get all classes with teacher names
        $query = "SELECT c.*, u.full_name as teacher_name 
             FROM classes c 
             LEFT JOIN users u ON c.teacher_id = u.id 
             ORDER BY c.name";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Get class by ID
    public function getClassById($class_id)
    {
        $query = "SELECT * FROM classes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $class_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addSubject($subject_data)
    {
        try {
            // Validate input
            if (empty($subject_data['name']) || !isset($subject_data['grade_level'])) {
                return false;
            }

            $query = "INSERT INTO subjects (name, description, grade_level) 
                 VALUES (:name, :description, :grade_level)";
            $stmt = $this->conn->prepare($query);

            // Sanitize and bind parameters
            $stmt->bindValue(':name', htmlspecialchars(strip_tags($subject_data['name'])));
            $stmt->bindValue(':description', htmlspecialchars(strip_tags($subject_data['description'])));
            $stmt->bindValue(':grade_level', (int) $subject_data['grade_level'], PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error adding subject: " . $e->getMessage());
            return false;
        }
    }

    public function deleteSubject($subject_id)
    {
        try {
            $query = "DELETE FROM subjects WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', (int) $subject_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting subject: " . $e->getMessage());
            return false;
        }
    }

    public function getAllSubjects()
    {
        try {
            $query = "SELECT * FROM subjects ORDER BY grade_level, name";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching subjects: " . $e->getMessage());
            return [];
        }
    }

    public function getUserById($id)
    {
        $query = "SELECT id, username, email, role, full_name FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind the ID parameter
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }



    public function deleteUser($user_id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind the ID
        $user_id = htmlspecialchars(strip_tags($user_id));
        $stmt->bindParam(':id', $user_id);

        return $stmt->execute();
    }

    public function getAllUsers()
    {
        $query = "SELECT id, username, email, role, full_name FROM " . $this->table . " ORDER BY username";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sanitize the output
        foreach ($results as &$user) {
            foreach ($user as $key => $value) {
                // Skip password field if it exists
                if ($key !== 'password') {
                    $user[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
            }
        }

        return $results;
    }

    // Add this method to validate user data before saving
    private function validateUserData($data)
    {
        $required_fields = ['username', 'email', 'role', 'full_name'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Validate role
        $valid_roles = ['Admin', 'Teacher', 'Student', 'Counselor'];
        if (!in_array($data['role'], $valid_roles)) {
            return false;
        }

        return true;
    }

    // Update the addUser method to use validation
    public function addUser($user_data)
    {
        if (!$this->validateUserData($user_data)) {
            return false;
        }

        $query = "INSERT INTO " . $this->table . " 
              (username, password, email, role, full_name) 
              VALUES (:username, :password, :email, :role, :full_name)";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        foreach ($user_data as $key => $value) {
            if ($key !== 'password') {
                $user_data[$key] = htmlspecialchars(strip_tags($value));
            }
        }

        // Bind parameters
        $stmt->bindParam(':username', $user_data['username']);
        $stmt->bindParam(':password', $user_data['password']);
        $stmt->bindParam(':email', $user_data['email']);
        $stmt->bindParam(':role', $user_data['role']);
        $stmt->bindParam(':full_name', $user_data['full_name']);

        return $stmt->execute();
    }

    public function login($username, $password)
    {
        // Use prepared statement to prevent SQL injection
        $query = "SELECT id, username, email, password, role, full_name FROM " . $this->table . " 
              WHERE username = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password using password_verify
            if (password_verify($password, $row['password'])) {
                // Return user data for session
                return [
                    'success' => true,
                    'user_id' => $row['id'],
                    'username' => $row['username'],
                    'email' => $row['email'],
                    'role' => $row['role'],
                    'full_name' => $row['full_name']
                ];
            }
        }

        return ['success' => false];
    }

    public function updateUser($user_data)
    {
        if (!$this->validateUserData($user_data)) {
            return false;
        }

        $query = "UPDATE " . $this->table . " SET 
              username = :username, 
              email = :email, 
              role = :role, 
              full_name = :full_name";

        // Only update password if it's provided
        if (!empty($user_data['password'])) {
            $query .= ", password = :password";
        }

        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        foreach ($user_data as $key => $value) {
            if ($key !== 'password' && $key !== 'id') {
                $user_data[$key] = htmlspecialchars(strip_tags($value));
            }
        }

        // Bind parameters
        $stmt->bindParam(':username', $user_data['username']);
        $stmt->bindParam(':email', $user_data['email']);
        $stmt->bindParam(':role', $user_data['role']);
        $stmt->bindParam(':full_name', $user_data['full_name']);
        $stmt->bindParam(':id', $user_data['id']);

        // Only bind password if it's being updated
        if (!empty($user_data['password'])) {
            $hashed_password = password_hash($user_data['password'], PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashed_password);
        }

        try {
            $result = $stmt->execute();

            // If this is the currently logged-in user, update their session
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_data['id']) {
                $_SESSION['username'] = $user_data['username'];
                $_SESSION['email'] = $user_data['email'];
                $_SESSION['role'] = $user_data['role'];
                $_SESSION['full_name'] = $user_data['full_name'];
            }

            return $result;
        } catch (PDOException $e) {
            // Log error and return false
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }

}