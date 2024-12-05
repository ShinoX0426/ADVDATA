<?php
class Report
{
    // Private database connection
    private $conn;
    private $table = 'rule_violation_reports';

    // Constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create report
    public function createReport($data)
    {
        $query = "INSERT INTO " . $this->table . " 
                  (student_id, reported_by, violation_type, description, date_of_incident, status) 
                  VALUES (:student_id, :reported_by, :violation_type, :description, :date_of_incident, :status)";

        try {
            $stmt = $this->conn->prepare($query);

            // Sanitize inputs
            $data['student_id'] = htmlspecialchars(strip_tags($data['student_id']));
            $data['reported_by'] = htmlspecialchars(strip_tags($data['reported_by']));
            $data['violation_type'] = htmlspecialchars(strip_tags($data['violation_type']));
            $data['description'] = htmlspecialchars(strip_tags($data['description']));
            $data['date_of_incident'] = htmlspecialchars(strip_tags($data['date_of_incident']));
            $data['status'] = htmlspecialchars(strip_tags($data['status']));

            // Bind parameters
            $stmt->bindParam(':student_id', $data['student_id']);
            $stmt->bindParam(':reported_by', $data['reported_by']);
            $stmt->bindParam(':violation_type', $data['violation_type']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':date_of_incident', $data['date_of_incident']);
            $stmt->bindParam(':status', $data['status']);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error creating report: " . $e->getMessage());
            return false;
        }
    }

    // Get all reports with optional filters
    public function getAllReports($filters = [])
    {
        try {
            $query = "SELECT r.*, 
                             s.first_name, s.last_name, 
                             u.username as reported_by_name
                      FROM " . $this->table . " r
                      JOIN students s ON r.student_id = s.id
                      JOIN users u ON r.reported_by = u.id
                      WHERE 1=1";

            $params = [];

            if (!empty($filters['violation_type'])) {
                $query .= " AND r.violation_type = :violation_type";
                $params[':violation_type'] = $filters['violation_type'];
            }

            if (!empty($filters['status'])) {
                $query .= " AND r.status = :status";
                $params[':status'] = $filters['status'];
            }

            if (!empty($filters['date_from'])) {
                $query .= " AND r.date_of_incident >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }

            if (!empty($filters['date_to'])) {
                $query .= " AND r.date_of_incident <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }

            $query .= " ORDER BY r.date_reported DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching reports: " . $e->getMessage());
            return [];
        }
    }

    // Get single report
    public function getReportById($id)
    {
        try {
            $query = "SELECT r.*, 
                             s.first_name, s.last_name, 
                             u.username as reported_by_name
                      FROM " . $this->table . " r
                      JOIN students s ON r.student_id = s.id
                      JOIN users u ON r.reported_by = u.id
                      WHERE r.id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching report: " . $e->getMessage());
            return null;
        }
    }

    // Update report
    public function updateReport($id, $data)
    {
        try {
            $query = "UPDATE " . $this->table . "
                      SET violation_type = :violation_type,
                          description = :description,
                          date_of_incident = :date_of_incident,
                          status = :status
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            // Sanitize inputs
            $data['violation_type'] = htmlspecialchars(strip_tags($data['violation_type']));
            $data['description'] = htmlspecialchars(strip_tags($data['description']));
            $data['date_of_incident'] = htmlspecialchars(strip_tags($data['date_of_incident']));
            $data['status'] = htmlspecialchars(strip_tags($data['status']));

            // Bind parameters
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':violation_type', $data['violation_type']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':date_of_incident', $data['date_of_incident']);
            $stmt->bindParam(':status', $data['status']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating report: " . $e->getMessage());
            return false;
        }
    }

    // Delete report
    public function deleteReport($id)
    {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting report: " . $e->getMessage());
            return false;
        }
    }

    // Get reports by student
    public function getReportsByStudent($student_id)
    {
        try {
            $query = "SELECT r.*, u.username as reported_by_name
                      FROM " . $this->table . " r
                      JOIN users u ON r.reported_by = u.id
                      WHERE r.student_id = :student_id 
                      ORDER BY r.date_reported DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching student reports: " . $e->getMessage());
            return [];
        }
    }

    // Get report statistics
    public function getReportStats()
    {
        try {
            $query = "SELECT 
                        COUNT(*) as total_reports,
                        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_reports,
                        SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress_reports,
                        SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved_reports,
                        COUNT(DISTINCT student_id) as total_students_reported
                      FROM " . $this->table;

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching report stats: " . $e->getMessage());
            return null;
        }
    }
}
?>