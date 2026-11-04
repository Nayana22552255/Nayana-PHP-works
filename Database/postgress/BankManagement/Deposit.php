<?php

class Deposit
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    public function depositMoney(string $accountNumber,string $amount): array
    {
        /*if(!!preg_match('/^(0|[1-9][0-9]{0,12})(\.[0-9]{1,2})?$/', $amount)
            || !preg_match('/[1-9]/', $amount))
            {
                throw new InvalidArgumentException("Enter a valid amount greter than zero");
            }*/

            $this->conn->beginTransaction();

            try 
            {
                $sql="SELECT account_id,account_number,balance,account_status
                FROM accounts
                WHERE account_number=:account_number
                FOR UPDATE";

                $stmt=$this->conn->prepare($sql);
                $stmt->execute([':account_number'=>$accountNumber]);
                $account=$stmt->fetch(PDO::FETCH_ASSOC);

                if(!$account)
                    {
                        throw new RuntimeException("Account not found");
                    }

                if($account['account_status'] !== 'Active')
                    {
                        throw new RangeException("Account is not active.");
                    }

                $previousBalance=$account['balance'];

                $updateSql="UPDATE accounts SET balance=balance + CAST(:amount AS NUMERIC) WHERE account_id=:account_id";
                $updateStmt=$this->conn->prepare($updateSql);
                $updateStmt->execute([
                    ':amount'=>$amount,
                    ':account_id'=>$account['account_id']
                ]);

                $insertSql="INSERT INTO transactions(account_id,transaction_type,amount)
                VALUES(:account_id,'Deposit',CAST(:amount AS NUMERIC))
                RETURNING transaction_id";

                $insertStmt=$this->conn->prepare($insertSql);

                $insertStmt->execute([
                    ':account_id'=>$account['account_id'],
                    ':amount'=>$amount
                ]);

                $transactionId=$insertStmt->fetchColumn();

                $balanceSql="SELECT balance FROM accounts WHERE account_id=:account_id";

                $balanceStmt=$this->conn->prepare($balanceSql);

                $balanceStmt->execute([
                    ':account_id'=>$account['account_id']
                ]);

                $newBalance=$balanceStmt->fetchColumn();

                $this->conn->commit();

                return[
                    'transaction_id'=>$transactionId,
                    'account_number'=>$accountNumber,
                    'previous_balance'=>$previousBalance,
                    'deposit_amount'=>$amount,
                    'new_balance'=>$newBalance
                ];
            }catch(Throwable $e)
            {
                if($this->conn->inTransaction())
                    {
                        $this->conn->rollBack();
                    }
                throw $e;
            }
    }

    public function getBalance(string $accountNumber):array 
    {
        $sql="SELECT account_number,balance,account_status 
              FROM accounts WHERE account_number=:account_number";

        $stmt= $this->conn->prepare($sql);
        $stmt->execute([
            ':account_number'=>$accountNumber
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDepositoryHistory(string $accountNumber): array 
    {
        $sql="SELECT t.transaction_id,
                     a.account_number,
                     t.transaction_type,
                     t.amount,
                     t.transaction_date
                     FROM transactions t
                     INNER JOIN accounts a
                     ON t.account_id=a.account_id
                     WHERE a.account_number=:account_number
                     AND t.transaction_type='Deposit'
                     ORDER BY t.transaction_id DESC";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute([':account_number'=>$accountNumber]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}