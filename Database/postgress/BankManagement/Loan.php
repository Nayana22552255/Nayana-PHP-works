
<?php

class Loan
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Validate NUMERIC(15,2) values
    private function validateAmount(string $value): bool
    {
        return preg_match(
            '/^(0|[1-9][0-9]{0,12})(\.[0-9]{1,2})?$/',
            $value
        ) === 1;
    }


    public function applyLoan(int $customerId, string $amount,string $interestRate ): bool 
    {
        if ($customerId <= 0) {
            throw new InvalidArgumentException("Invalid customer ID.");
        }

        if (!$this->validateAmount($amount) || (float)$amount <= 0) {
            throw new InvalidArgumentException("Invalid loan amount.");
        }

        if (!$this->validateAmount($interestRate) || (float)$interestRate < 0) {
            throw new InvalidArgumentException("Invalid interest rate.");
        }

        $checkSql = "SELECT customer_id FROM customers WHERE customer_id = :customer_id";

        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute([':customer_id' => $customerId ]);

        if (!$checkStmt->fetch()) {
            throw new RuntimeException("Customer not found.");
        }

      
        $sql = "INSERT INTO loans (customer_id, loan_amount, interest_rate, loan_status)
                VALUES(:customer_id, CAST(:amount AS NUMERIC),CAST(:interest_rate AS NUMERIC), 'Pending')";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':customer_id' => $customerId,
            ':amount' => $amount,
            ':interest_rate' => $interestRate
        ]);
    }


    public function getAllLoans(): array
    {
        $sql = "SELECT
                    l.loan_id,
                    l.customer_id,
                    c.customer_name,
                    l.loan_amount,
                    l.interest_rate,
                    l.loan_status,
                    l.created_at
                FROM loans l
                INNER JOIN customers c
                ON l.customer_id = c.customer_id
                ORDER BY l.loan_id";

        $stmt = $this->conn->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

  
    public function getLoanById(int $id): array
    {
        $sql = "SELECT
                    l.loan_id,
                    l.customer_id,
                    c.customer_name,
                    l.loan_amount,
                    l.interest_rate,
                    l.loan_status,
                    l.created_at
                FROM loans l
                INNER JOIN customers c
                ON l.customer_id = c.customer_id
                WHERE l.loan_id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateLoanStatus(int $id,string $status): bool {

        $allowedStatuses = [
            'Pending',
            'Approved',
            'Rejected',
            'Active',
            'Closed'
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new InvalidArgumentException("Invalid loan status.");
        }

        $sql = "UPDATE loans SET loan_status = :status WHERE loan_id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }

  
    public function deleteLoan(int $id): bool
    {
        $sql = "DELETE FROM loans WHERE loan_id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->rowCount() > 0;
    }
}


