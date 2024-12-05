<?php
class CounselingSession
{
    private $conn;
    private $table = 'counseling_sessions';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getUpcomingSessions($counselor_id, $limit = 5)
    {
        $query = "SELECT cs.*, 
                         CONCAT(s.first_name, ' ', s.last_name) as student_name
                  FROM " . $this->table . " cs
                  JOIN students s ON cs.student_id = s.id
                  WHERE cs.counselor_id = :counselor_id 
                  AND cs.date_time >= NOW()
                  AND cs.status = 'Scheduled'
                  ORDER BY cs.date_time ASC
                  LIMIT :limit";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':counselor_id', $counselor_id, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting upcoming sessions: " . $e->getMessage());
            return [];
        }
    }
}