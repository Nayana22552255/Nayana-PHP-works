
<?php

class Ward extends Model
{
    // Get all wards with optional search
    public function getAllWards($keyword = '')
    {
        $sql = "
            SELECT
                w.*,
                COUNT(DISTINCT r.id) AS total_rooms,
                COUNT(DISTINCT b.id) AS total_beds,
                SUM(
                    CASE
                        WHEN b.bed_status = 'Available' THEN 1
                        ELSE 0
                    END
                ) AS available_beds,
                SUM(
                    CASE
                        WHEN b.bed_status = 'Occupied' THEN 1
                        ELSE 0
                    END
                ) AS occupied_beds
            FROM wards w
            LEFT JOIN rooms r ON w.id = r.ward_id
            LEFT JOIN beds b ON r.id = b.room_id
        ";

        if ($keyword !== '') {
            $sql .= "
                WHERE
                    w.ward_name LIKE :keyword
                    OR w.ward_code LIKE :keyword
                    OR w.ward_type LIKE :keyword
            ";
        }

        $sql .= "
            GROUP BY w.id
            ORDER BY w.id DESC
        ";

        $stmt = $this->db->prepare($sql);

        if ($keyword !== '') {
            $stmt->bindValue(
                ':keyword',
                '%' . $keyword . '%'
            );
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a single ward
    public function getWardById($id)
    {
        $sql = "
            SELECT
                w.*,
                COUNT(DISTINCT r.id) AS total_rooms,
                COUNT(DISTINCT b.id) AS total_beds
            FROM wards w
            LEFT JOIN rooms r ON w.id = r.ward_id
            LEFT JOIN beds b ON r.id = b.room_id
            WHERE w.id = :id
            GROUP BY w.id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add a new ward
    public function addWard($data)
    {
        $sql = "
            INSERT INTO wards (
                ward_name,
                ward_code,
                ward_type,
                description
            )
            VALUES (
                :ward_name,
                :ward_code,
                :ward_type,
                :description
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':ward_name' => $data['ward_name'],
            ':ward_code' => $data['ward_code'],
            ':ward_type' => $data['ward_type'],
            ':description' => $data['description']
        ]);
    }

    // Update ward details
    public function updateWard($id, $data)
    {
        $sql = "
            UPDATE wards
            SET
                ward_name = :ward_name,
                ward_code = :ward_code,
                ward_type = :ward_type,
                description = :description
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':ward_name' => $data['ward_name'],
            ':ward_code' => $data['ward_code'],
            ':ward_type' => $data['ward_type'],
            ':description' => $data['description'],
            ':id' => $id
        ]);
    }

    // Activate or deactivate a ward
    public function updateStatus($id, $status)
    {
        $sql = "
            UPDATE wards
            SET status = :status
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }
}
