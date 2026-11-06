
<?php

class Fees
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function addFee(int $studentId,float $totalFee,float $paidAmount,?string $paymentDate = null): bool 
    {
        $this->validateAmounts($totalFee, $paidAmount);

        $status = $this->calculateStatus($totalFee, $paidAmount);

        $sql = "INSERT INTO fees
                (
                    student_id,
                    total_fee,
                    paid_amount,
                    payment_date,
                    payment_status
                )
                VALUES (?, ?, ?, COALESCE(?::DATE, CURRENT_DATE), ?)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([$studentId,$totalFee,$paidAmount,$paymentDate,$status]);
    }

   
    public function getFees(): array
    {
        $sql = "SELECT
                    f.fee_id,
                    f.student_id,
                    s.student_name,
                    s.registration_number,
                    f.total_fee,
                    f.paid_amount,
                    (f.total_fee - f.paid_amount) AS pending_amount,
                    f.payment_date,
                    f.payment_status
                    FROM fees f
                    INNER JOIN students s
                    ON f.student_id = s.student_id
                    ORDER BY f.fee_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getFeeById(int $id): ?array
    {
        $sql = "SELECT * FROM fees WHERE fee_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        $fee = $stmt->fetch(PDO::FETCH_ASSOC);

        return $fee === false ? null : $fee;
    }

    public function getFeesByStudent(int $studentId): array
    {
        $sql = "SELECT
                    fee_id,
                    student_id,
                    total_fee,
                    paid_amount,
                    (total_fee - paid_amount) AS pending_amount,
                    payment_date,
                    payment_status
                    FROM fees
                    WHERE student_id = ?
                    ORDER BY fee_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$studentId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function updateFee(int $id,float $totalFee, float $paidAmount, ?string $paymentDate = null): bool 
    {
        $this->validateAmounts($totalFee, $paidAmount);

        $status = $this->calculateStatus($totalFee, $paidAmount);

        $sql = "UPDATE fees
                SET total_fee = ?,
                    paid_amount = ?,
                    payment_date = COALESCE(?::DATE, payment_date),
                    payment_status = ?
                WHERE fee_id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([$totalFee,$paidAmount,$paymentDate,$status,$id]);

        return $stmt->rowCount() > 0;
    }

   
    public function recordPayment(int $id,float $amount,?string $paymentDate = null): bool 
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException(
                "Payment amount must be greater than zero."
            );
        }

        $this->conn->beginTransaction();

        try {
            $sql = "SELECT total_fee, paid_amount FROM fees
                    WHERE fee_id = ? FOR UPDATE";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);

            $fee = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$fee) {
                throw new RuntimeException("Fee record not found.");
            }

            $totalFee = (float) $fee['total_fee'];
            $paidAmount = (float) $fee['paid_amount'] + $amount;

            $this->validateAmounts($totalFee, $paidAmount);

            $status = $this->calculateStatus(
                $totalFee,
                $paidAmount
            );

            $updateSql = "UPDATE fees
                          SET paid_amount = ?,
                              payment_date = COALESCE(?::DATE, CURRENT_DATE),
                              payment_status = ?
                          WHERE fee_id = ?";

            $updateStmt = $this->conn->prepare($updateSql);

            $updateStmt->execute([$paidAmount,$paymentDate,$status,$id]);

            $this->conn->commit();

            return true;

        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }

   
    public function getPendingFees(): array
    {
        $sql = "SELECT
                    f.fee_id,
                    s.student_id,
                    s.student_name,
                    s.registration_number,
                    f.total_fee,
                    f.paid_amount,
                    (f.total_fee - f.paid_amount) AS pending_amount,
                    f.payment_status
                    FROM fees f
                    INNER JOIN students s
                    ON f.student_id = s.student_id
                    WHERE f.paid_amount < f.total_fee
                    ORDER BY s.student_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function deleteFee(int $id): bool
    {
        $sql = "DELETE FROM fees WHERE fee_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

  
    public function getTotalFees(): float
    {
        $sql = "SELECT COALESCE(SUM(total_fee), 0) FROM fees";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return (float) $stmt->fetchColumn();
    }

  
    public function getTotalPaidAmount(): float
    {
        $sql = "SELECT COALESCE(SUM(paid_amount), 0) FROM fees";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return (float) $stmt->fetchColumn();
    }

  
    public function getTotalPendingAmount(): float
    {
        $sql = "SELECT COALESCE(SUM(total_fee - paid_amount), 0)
                FROM fees";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return (float) $stmt->fetchColumn();
    }

   
    private function calculateStatus( float $totalFee,float $paidAmount): string 
    {

        if ($paidAmount == 0) {
            return "Pending";
        }

        if ($paidAmount == $totalFee) {
            return "Paid";
        }

        return "Partially Paid";
    }

   
    private function validateAmounts(float $totalFee,float $paidAmount): void 
    {

        if ($totalFee <= 0) {
            throw new InvalidArgumentException(
                "Total fee must be greater than zero."
            );
        }

        if ($paidAmount < 0 || $paidAmount > $totalFee) {
            throw new InvalidArgumentException(
                "Paid amount must be between zero and total fee."
            );
        }
    }
}
