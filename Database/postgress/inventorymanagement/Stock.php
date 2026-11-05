<?php
class Stock 
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    public function manageStock(int $productId, string $type,int $quantity): bool 
    {
        if($productId <= 0)
            {
                throw new InvalidArgumentException("Product ID must be greater than zero");
            }

        if($quantity <= 0)
            {
                throw new InvalidArgumentException("Quantity must be greater than Zero");
            }

        $type=strtoupper($type);

        if(!in_array($type,['IN','OUT'],true))
            {
                throw new InvalidArgumentException("Invalid transaction type");
            }

        $this->conn->beginTransaction();

        try
            {
                if($type === 'IN')
                    {
                        $sql="UPDATE products
                              SET quantity=quantity+?
                              WHERE product_id=?";
                        $stmt=$this->conn->prepare($sql);
                        $stmt->execute([$quantity,$productId]);
                    }
                else
                    {
                        $sql="UPDATE products
                              SET quantity=quantity-?
                              WHERE product_id=? AND quantity>=?";
                        $stmt=$this->conn->prepare($sql);
                        $stmt->execute([$quantity,$productId,$quantity]);
                    }

                if($stmt->rowCount() !== 1)
                    {
                        $check=$this->conn->prepare("SELECT 1 FROM products WHERE product_id=?");
                        $check->execute([$productId]);
                        if(!$check->fetchColumn())
                            {
                                throw new RuntimeException("Product not found");
                            }

                        throw new RuntimeException("Insufficient stock for this stock-out operation");
                    }

                $stmt=$this->conn->prepare(
                    "INSERT INTO stock_transactions(product_id,transaction_type,quantity)
                     VALUES(?,?,?)"
                );
                $stmt->execute([$productId,$type,$quantity]);

                $this->conn->commit();
                return true;
            }
        catch(Throwable $e)
            {
                if($this->conn->inTransaction())
                    {
                        $this->conn->rollBack();
                    }

                throw $e;
            }
    }

    public function lowStock()
    {
        $sql="SELECT product_id,product_name,quantity,minimum_stock
                     FROM products WHERE quantity <= minimum_stock";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}