<?php

class Accounts
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    private function generateAccountNumber(): string
    {
        return "AC". random_int(100000000000,999999999999);
    }

    public function  createAccount(int $customerId, string $accountType):bool 
    {
        $checkSql="SELECT customer_id FROM customers WHERE customer_id =:customer_id";
        $checkStmt=$this->conn->prepare($checkSql);
        $checkStmt->execute([':customer_id'=>$customerId]);
        if(!$checkStmt->fetch())
            {
                throw new  InvalidArgumentException("Customer does not exist.");
            }

        if(!in_array($accountType,['Savings','Current'],true))
            {
                throw new InvalidArgumentException("Invalid account type.");
            }

        $accountNumber=$this->generateAccountNumber();

        $sql="INSERT INTO accounts(customer_id,account_number,account_type,balance)
        VALUES(:customer_id,:account_number,:account_type,0)";

        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([
            ':customer_id'=>$customerId,
            ':account_number'=>$accountNumber,
            ':account_type'=>$accountType,
            
        ]);

    }

    public function getAllAccounts(): array
    {
        $sql="SELECT 
        a.account_id,
        a.account_number,
        a.account_type,
        a.balance,
        a.account_status,
        a.created_at,
        c.customer_id,
        c.customer_name,
        c.email,
        c.phone FROM accounts a
        INNER JOIN customers c ON c.customer_id=a.customer_id
        ORDER BY a.account_id";

        $stmt=$this->conn->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    }

    public function getAccountByNumber(string $accountNumber):array
    {
        $sql= "SELECT 
        a.*,
        c.customer_name,
        c.email,
        c.phone FROM accounts a
        INNER JOIN customers c ON a.customer_id=c.customer_id
        WHERE a.account_number=:account_number";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute([':account_number'=>$accountNumber]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getCustomerAccount(int $customerId): array
    {
        $sql="SELECT FROM accounts WHERE customer_id=:customer_id ORDER BY account_id";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute(['customer_id'=>$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateAccountType(string $accountNumber,string $accountType):bool
    {
        if(!in_array($accountType,['Savings','Current'],true))
            {
                throw new InvalidArgumentException("Invalid account type.");
            }

        $sql="UPDATE accounts 
              SET account_type= :account_type
              WHERE account_number=:account_number
              AND account_status='Active' ";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute([
            ':account_type'=>$accountType,
            ':account_number'=>$accountNumber
        ]);

        return $stmt->rowCount()>0;
    }

    public function closeAccount(string $accountNumber):bool
    {
        $sql="UPDATE accounts SET account_status='Closed'
              WHERE account_number=:account_number
              AND account_status='Active'
              AND balance=0";

        $stmt=$this->conn->prepare($sql);

        $stmt->execute([':account_number'=>$accountNumber]);

        return $stmt->rowCount() > 0;
    }

    public function getAccountsWithCustomer(int $customerId): array 
    {
        $sql= "SELECT 
                c.customer_id,
                c.customer_name,
                c.email,
                a.account_id,
                a.account_number,
                a.account_type,
                a.balance,
                a.account_status
                FROM customers c
                INNER JOIN  accounts a
                ON c.customer_id=a.customer_id
                WHERE c.customer_id=:customer_id";

                $stmt=$this->conn->prepare($sql);
                $stmt->execute(['customer_id'=>$customerId]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
