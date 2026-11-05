<?php
class Product 
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    public function addProduct(string $name,int $category,int $supplier,float $price,int $quantity,int $minimumstock): bool
    {
        $sql="INSERT INTO products(product_name,category_id,supplier_id,price,quantity,minimum_stock)
        VALUES(?,?,?,?,?,?)";

        $stmt=$this->conn->prepare($sql);

        return $stmt->execute([$name,$category,$supplier,$price,$quantity,$minimumstock]);
    }

    public function getCategories(): array
    {
        $sql="SELECT category_id, category_name
              FROM categories
              ORDER BY category_id";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProduct()
    {
            $sql="SELECT 
                        p.product_id,
                        p.product_name,
                        c.category_name,
                        s.supplier_name,
                        p.price,
                        p.quantity,
                        p.minimum_stock
                        FROM products p
                        JOIN categories c
                        ON p.category_id=c.category_id
                        JOIN suppliers s
                        ON p.supplier_id=s.supplier_id
                        ORDER  BY p.product_id";

            return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updatePrice(int $id,float $price): bool
    {
        $sql="UPDATE products SET price=? WHERE product_id=?";

        $stmt=$this->conn->Prepare($sql);

        return $stmt->execute([$price,$id]);
    }

    public function deleteProduct(int $id): bool 
    {
        $sql="DELETE FROM products WHERE product_id=?";
        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

}