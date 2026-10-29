<?php
declare(strict_types=1);

final class Resident
{
    public function __construct(private PDO $db) {}

    public function getResidents(): array
    {
        $stmt = $this->db->query(
            'SELECT resident_id, first_name, middle_name, last_name, sex, birth_date,
                    phone, purok, address, created_at
             FROM residents ORDER BY created_at DESC, resident_id DESC'
        );
        return $stmt->fetchAll();
    }

    public function getResident(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM residents WHERE resident_id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $resident = $stmt->fetch();
        return $resident ?: null;
    }

    public function addResident(array $data): int
    {
        // Transaction 1: add a resident and its audit record as one unit.
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO residents
                 (first_name, middle_name, last_name, sex, birth_date, phone, purok, address)
                 VALUES (:first_name, :middle_name, :last_name, :sex, :birth_date, :phone, :purok, :address)'
            );
            $stmt->execute($data);
            $id = (int)$this->db->lastInsertId();

            $this->logActivity('Created resident record #' . $id);
            $this->db->commit();
            return $id;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function updateResident(int $id, array $data): bool
    {
        // Transaction 2: update the resident and record the action together.
        $this->db->beginTransaction();
        try {
            $data['id'] = $id;
            $stmt = $this->db->prepare(
                'UPDATE residents SET first_name = :first_name, middle_name = :middle_name,
                 last_name = :last_name, sex = :sex, birth_date = :birth_date,
                 phone = :phone, purok = :purok, address = :address
                 WHERE resident_id = :id'
            );
            $stmt->execute($data);
            $changed = $stmt->rowCount() > 0;

            $this->logActivity('Updated resident record #' . $id);
            $this->db->commit();
            return $changed;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function deleteResident(int $id): bool
    {
        // Transaction 3: log the deletion and delete the resident atomically.
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('DELETE FROM residents WHERE resident_id = :id');
            $stmt->execute(['id' => $id]);
            $deleted = $stmt->rowCount() > 0;
            if ($deleted) {
                $this->logActivity('Deleted resident record #' . $id);
            }
            $this->db->commit();
            return $deleted;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function searchResidents(string $keyword): array
    {
        $term = '%' . $keyword . '%';
        $stmt = $this->db->prepare(
            'SELECT resident_id, first_name, middle_name, last_name, sex, birth_date,
                    phone, purok, address, created_at
             FROM residents
             WHERE CAST(resident_id AS CHAR) LIKE :id
                OR first_name LIKE :first_name
                OR middle_name LIKE :middle_name
                OR last_name LIKE :last_name
                OR sex LIKE :sex
                OR phone LIKE :phone
                OR purok LIKE :purok
                OR address LIKE :address
             ORDER BY created_at DESC, resident_id DESC'
        );
        $stmt->execute([
            'id' => $term, 'first_name' => $term, 'middle_name' => $term,
            'last_name' => $term, 'sex' => $term, 'phone' => $term,
            'purok' => $term, 'address' => $term
        ]);
        return $stmt->fetchAll();
    }

    public function getStats(): array
    {
        $total = (int)$this->db->query('SELECT COUNT(*) FROM residents')->fetchColumn();
        $puroks = (int)$this->db->query('SELECT COUNT(DISTINCT purok) FROM residents WHERE purok <> ""')->fetchColumn();
        $recent = (int)$this->db->query('SELECT COUNT(*) FROM residents WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)')->fetchColumn();
        return ['total' => $total, 'puroks' => $puroks, 'recent' => $recent];
    }

    public function getRecentActivity(int $limit = 6): array
    {
        $limit = max(1, min($limit, 20));
        return $this->db->query("SELECT description, created_at FROM activity_logs ORDER BY log_id DESC LIMIT {$limit}")->fetchAll();
    }

    private function logActivity(string $description): void
    {
        $stmt = $this->db->prepare('INSERT INTO activity_logs (description) VALUES (:description)');
        $stmt->execute(['description' => $description]);
    }
}
