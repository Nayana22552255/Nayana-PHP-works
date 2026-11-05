<?php
class Transaction 
{
    private PDO $conn;
    
    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    public function displayTransactions()
    {
        $sql="SELECT 
                    t.transaction_id,
                    p.product_name,
                    t.transaction_type,
                    t.quantity,
                    t.transaction_date
                    FROM stock_transactions t
                    JOIN products p
                    ON t.product_id=p.product_id
                    ORDER BY t.transaction_date DESC";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    }

    public function filterTransaction(string $type)
    {
            $type=strtoupper($type);

            if(!in_array($type,['IN','OUT'],true))
                {
                    throw new InvalidArgumentException("Invalid transaction type");
                }
            $sql="SELECT t.*,p.product_name
                         FROM stock_transactions t
                         JOIN products p ON t.product_id=p.product_id
                         WHERE t.transaction_type=?
                         ORDER BY t.transaction_date DESC";
            $stmt=$this->conn->prepare($sql);
            $stmt->execute([$type]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}