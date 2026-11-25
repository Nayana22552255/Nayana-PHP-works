<?php


class Bill
{
    private $conn;
    public function __construct($db)
    {
        $this->conn=$db;
    }

    public function getAllBills()
    {
         $sql = "SELECT
                    b.*,
                    a.appointment_date,
                    pt.patient_name,
                    d.doctor_name,
                    COALESCE(SUM(pay.amount), 0) AS paid_amount
                FROM bills b
                JOIN appointments a
                    ON b.appointment_id = a.appointment_id
                JOIN patients pt
                    ON a.patient_id = pt.patient_id
                JOIN doctors d
                    ON a.doctor_id = d.doctor_id
                LEFT JOIN payments pay
                    ON b.bill_id = pay.bill_id
                GROUP BY b.bill_id
                ORDER BY b.bill_date DESC";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getBillById($id)
    {
            $sql = "SELECT
                    b.*,
                    a.appointment_date,
                    a.appointment_time,
                    pt.patient_name,
                    pt.phone,
                    d.doctor_name,
                    COALESCE(
                        (SELECT SUM(p.amount)
                         FROM payments p
                         WHERE p.bill_id = b.bill_id), 0
                    ) AS paid_amount
                FROM bills b
                JOIN appointments a
                    ON b.appointment_id = a.appointment_id
                JOIN patients pt
                    ON a.patient_id = pt.patient_id
                JOIN doctors d
                    ON a.doctor_id = d.doctor_id
                WHERE b.bill_id = ?";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBillItems($billId)
    {
        $sql="SELECT * FROM bill_items
                WHERE bill_id=?
                OREDER BY bill_items_id";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$billId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAppointments($billId = null)
    {
            $sql = "SELECT
                    a.appointment_id,
                    a.appointment_date,
                    a.appointment_time,
                    pt.patient_name,
                    d.doctor_name
                FROM appointments a
                JOIN patients pt
                    ON a.patient_id = pt.patient_id
                JOIN doctors d
                    ON a.doctor_id = d.doctor_id
                LEFT JOIN bills b
                    ON a.appointment_id = b.appointment_id
                WHERE a.status = 'Completed'
                  AND (b.bill_id IS NULL OR b.bill_id = ?)
                ORDER BY a.appointment_date DESC";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$billId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    private function insertBillItems($billId,$items)
    {
        $sql="INSERT INTO bill_items
                (bill_id,description,quantity,unit_price)
                VALUES(?, ?, ?, ?)";
        $stmt=$this->conn->prepare($sql);

        foreach($items as $item)
            {
                $stmt->execute([
                    $billId,
                    $item['description'],
                    $item['quantity'],
                    $item['unit_price']
                ]);
            }
    }

    private function calculateTotal($items)
    {
        $total=0;

        foreach($items as $item)
            {
                $total += $item['quantity'] * item['unit_price'];
             }
        return round($total,2);
    }

    private function updatePaymentStatus($billId)
    {
        $bill=$this->getBillById($billId);
        if(!$bill)
            {
                throw new Exception("Bill not Found");
            }
        $paid=(float)$bill['paid_amount'];
        $total=(float)$bill['total_amount'];

        if($paid <= 0)
            {
                $status='unpaid';
            }elseif($paid >= $total)
            {
                $status='paid';
            }else{
                $status='Partial';
            }

        $sql="UPDATE bills SET payment_status = ?
        WHERE bill_id=?";

        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([$status,$billId]);
    }

    public function addBill($data, $items)
    {
        try {
            $this->conn->beginTransaction();

            $total = $this->calculateTotal($items);

            $sql = "INSERT INTO bills
                    (appointment_id, total_amount, payment_status)
                    VALUES (?, ?, 'Unpaid')";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                $data['appointment_id'],
                $total
            ]);

            $billId = $this->conn->lastInsertId();

            $this->insertBillItems($billId, $items);

            $this->conn->commit();

            return $billId;

        } catch (Throwable $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }

     public function updateBill($id, $data, $items)
    {
        try {
            $this->conn->beginTransaction();

            $total = $this->calculateTotal($items);

            $sql = "UPDATE bills
                    SET appointment_id = ?,
                        total_amount = ?
                    WHERE bill_id = ?";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                $data['appointment_id'],
                $total,
                $id
            ]);
             $deleteSql = "DELETE FROM bill_items
                          WHERE bill_id = ?";

            $deleteStmt = $this->conn->prepare($deleteSql);
            $deleteStmt->execute([$id]);

            // Insert updated items
            $this->insertBillItems($id, $items);

            // Recalculate status after bill total changes
            $this->updatePaymentStatus($id);

            $this->conn->commit();

            return true;

        } catch (Throwable $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }
    public function addPayment($billId, $amount, $method)
    {
        try {
            $this->conn->beginTransaction();

            // Lock the bill while checking its balance
            $sql = "SELECT
                        b.total_amount,
                        COALESCE(SUM(p.amount), 0) AS paid_amount
                    FROM bills b
                    LEFT JOIN payments p
                        ON b.bill_id = p.bill_id
                    WHERE b.bill_id = ?
                    GROUP BY b.bill_id
                    FOR UPDATE";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$billId]);

            $bill = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$bill) {
                throw new Exception("Bill not found.");
            }

            $balance = round(
                (float)$bill['total_amount'] -
                (float)$bill['paid_amount'],
                2
            );

            if ($amount <= 0 || $amount > $balance) {
                throw new Exception("Payment amount exceeds the outstanding balance.");
            }

            $sql = "INSERT INTO payments
                    (bill_id, amount, payment_method)
                    VALUES (?, ?, ?)";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                $billId,
                $amount,
                $method
            ]);

            $this->updatePaymentStatus($billId);

            $this->conn->commit();

            return true;

        } catch (Throwable $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }

     public function getPayments($billId)
    {
        $sql = "SELECT *
                FROM payments
                WHERE bill_id = ?
                ORDER BY payment_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$billId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteBill($id)
    {
        $sql = "DELETE FROM bills WHERE bill_id = ?";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([$id]);
    }


     public function searchBills($keyword)
    {
        $sql = "SELECT
                    b.*,
                    a.appointment_date,
                    pt.patient_name,
                    d.doctor_name,
                    COALESCE(SUM(pay.amount), 0) AS paid_amount
                FROM bills b
                JOIN appointments a
                    ON b.appointment_id = a.appointment_id
                JOIN patients pt
                    ON a.patient_id = pt.patient_id
                JOIN doctors d
                    ON a.doctor_id = d.doctor_id
                LEFT JOIN payments pay
                    ON b.bill_id = pay.bill_id
                WHERE pt.patient_name LIKE ?
                   OR d.doctor_name LIKE ?
                GROUP BY b.bill_id
                ORDER BY b.bill_date DESC";

        $search = "%" . $keyword . "%";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$search, $search]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>