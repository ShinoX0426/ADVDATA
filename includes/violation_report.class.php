<?php
class ViolationReport
{
    private $conn;
    private $table = 'rule_violation_reports';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getRecentViolations($limit = 5)
    {
        $query = "SELECT rv.*, 
                         CONCAT(s.first_name, ' ', s.last_name) as student_name
                  FROM " . $this->table . " rv
                  JOIN students s ON rv.student_id = s.id
                  ORDER BY rv.date_reported DESC
                  LIMIT :limit";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting recent violations: " . $e->getMessage());
            return [];
        }
    }
}