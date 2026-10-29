<?php
class member
{
    private  PDO $conn;

    private const CATEGORIES = [
    'staff' => 'Staff',
    'undergraduate' => 'Undergraduate Student',
    'undergraduate student' => 'Undergraduate Student',
    'undergradute' => 'Undergraduate Student',
    'graduate student' => 'Graduate Student',
    'post graduate' => 'Graduate Student',
    'research' => 'Research Student',
    'research student' => 'Research Student'
    ];
    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    private function normalizeCategory(string $category): ?string
    {
        return self::CATEGORIES[strtolower(trim($category))] ?? null;
    }
    


        private function getFeeId(string $category): int
    {
    $categoryMap = [
        'staff' => 'Staff',
        'research' => 'Research',
        'research student' => 'Research',
        'post graduate' => 'Post Graduate',
        'graduate student' => 'Post Graduate',
        'undergraduate' => 'Undergraduate',
        'undergraduate student' => 'Undergraduate'
    ];

    $key = strtolower(trim($category));

    if (!isset($categoryMap[$key])) {
        throw new InvalidArgumentException(
            "Invalid category: " . $category
        );
    }

    $sql = "SELECT fee_id
            FROM member_fee
            WHERE LOWER(member_category) = LOWER(?)";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$categoryMap[$key]]);

    $feeId = $stmt->fetchColumn();

    if ($feeId === false) {
        throw new RuntimeException(
            "Fee ID not found for category: " . $category
        );
    }

    return (int) $feeId;
    }
 
    public function addMember(string $name,string $email,string $phone,string $category,int $maxBooks=3,int $loanDays=14)
    {
        try 
        {
            $category=$this->normalizeCategory($category);

            if($category === null)
                {
                    echo "Invalid member category.\n";
                    return;
                }

            $fee_id = $this->getFeeId($category);

            echo "Automatically assigned Fee ID: $fee_id\n";


            if($maxBooks < 1 || $loanDays <1)
                {
                    echo "Maximum Books  and Loan days must be greater than zero.\n";
                    return;
                }
        $sql = "INSERT INTO members(name, email, phone, category, max_books, loan_days, active, fee_id)
        VALUES (:name, :email, :phone, :category, :max_books,:loan_days, 1, :fee_id)";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':category' => $category,
            ':max_books' => $maxBooks,
            ':loan_days' => $loanDays,
            ':fee_id' => $fee_id]);

            echo "Member Added successfully\n";

        ;
        
        }catch(Exception $e) 
        {
            echo "Error adding members. ".$e->getMessage(). "\n";
        }
    }

    public function showAll()
    {
        try 
        {
            $sql="SELECT member_id,name,email,phone,category,max_books,loan_days,active
                  FROM members ORDER BY member_id";

            $stmt=$this->conn->query($sql);
            $members=$stmt->fetchAll(PDO::FETCH_ASSOC);


            if(empty($members))
                {
                    echo "No members found\n";
                    return;
                }

            echo "--------Member List-------\n";
            foreach($members as $member)
                {
                    echo "Member Id: ".$member['member_id'], "\n";
                    echo "Name: ".$member['name']. "\n";
                    echo "Email: ".$member['email']. "\n";
                    echo "Phone: ".$member['phone']. "\n";
                    echo "Category: ".$member['category']. "\n";
                    echo "Maximum Books: ".$member['max_books']. "\n";
                    echo "Loan Days: ".$member['loan_days']. "\n";
                    echo "Status: ".((int)$member['active']===1 ? 'Active' : 'Inactive'). "\n";
                    echo "-----------------------------------------------------------------\n";
                }
        }catch(Exception $e)
        {
            echo "Error display members ".$e->getMessage(). "\n";
        }
    }

    public function searchMember(string $keyword)
    {
        try 
        {
             $sql="SELECT member_id,name,email,phone,category,max_books,loan_days,active FROM members
                    WHERE name LIKE :name OR email LIKE :email ORDER BY member_id";
                    
            $stmt=$this->conn->prepare($sql);

            $stmt->execute([':name'=> '%'. $keyword. "%",
                            ':email'=>'%'. $keyword. "%" ]);

            $members=$stmt->fetchAll(PDO::FETCH_ASSOC);

            if(empty($members))
                {
                    echo "No matching members found\n";
                    return;
                }

            foreach($members as $member)
                {
                    echo "Member Id: ". $member['member_id']. "\n";
                    echo "Name: ". $member['name']. "\n";
                    echo "Email: ". $member['email']. "\n";
                    echo "Phone: ". $member['phone']. "\n";
                    echo "Category: ". $member['category']. "\n";
                    echo "Status: ".((int)$member['active']===1 ? 'Active' : 'Inactive'). "\n";
                    echo "-----------------------------------------------------------------\n";
                 }
        }catch(Exception $e)
        {
            echo "Erroe Seacrhing members.". $e->getMessage(). "\n";
        }
    }

    public function updateMembers(int $memberId,string $name,string $email,string $phone,string $category,int $maxBooks,int $loanDays)
    {
        try 
        {
                $category=$this->normalizeCategory($category);

                if($category === null)
                {
                    echo "Invalid member category. Use staff, Undergraduate Student, Graduate Student, or Research Student.\n";
                    return;
                }

                 if($maxBooks < 1 || $loanDays <1)
                {
                    echo "Maximum Books  and Loan days must be greater than zero.\n";
                    return;
                }

    
                    $fee_id = $this->getFeeId($category);
                    echo "Automatically assigned Fee ID: $fee_id\n";

                    $sql = "UPDATE members SET
                        name = :name,
                        email = :email,
                        phone = :phone,
                        category = :category,
                        max_books = :max_books,
                        loan_days = :loan_days,
                        fee_id = :fee_id
                    WHERE member_id = :id";
                $stmt=$this->conn->prepare($sql);

                $stmt->execute([':name'=>$name,
                            ':email'=>$email,
                            ':phone'=>$phone,
                            ':category'=>$category,
                            ':max_books'=>$maxBooks,
                            ':loan_days'=>$loanDays,
                            ':fee_id' => $fee_id,
                            ':id'=>$memberId]);

                if($stmt->rowCount()>0) 
                    {
                        echo "Member Updated successfully\n";

                    }else
                    {
                        echo "No chnages made.\n";
                    }

        }catch(Exception $e)
        {
            echo "Error updating members.".$e->getMessage(). "\n";
        }
    }

    public function setStatus(int $memberId,bool $active)
    {
        try 
        {
            $check=$this->conn->prepare("SELECT active FROM members WHERE member_id=:id");
            $check->execute([':id'=>$memberId]);
            $currentStatus=$check->fetchColumn();

            if($currentStatus === false)
            {
                echo "Member ID not found.\n";
                return;
            }

            $newStatus=$active ? 1 : 0;
            if((int)$currentStatus === $newStatus)
            {
                echo "Member is ".($active ? 'active' : 'inactive').".\n";
                return;
            }

            $sql="UPDATE members SET active= :status WHERE member_id=:id";

            $stmt=$this->conn->prepare($sql);

            $stmt->execute([':status'=>$newStatus, ':id'=>$memberId]);

            if($stmt->rowCount()>0) 
                {
                    echo "Member status updated successfully\n";
                }
        }catch(Exception $e)
        {
            echo "Error updating Status". $e->getMessage(). "\n";
        }
    }
    public function deleteMember(int $memberId)
    {
        try 
        {
            $check=$this->conn->prepare("SELECT COUNT(*) FROM borrowings Where member_id=:id");

            $check->execute([':id'=>$memberId]);
            if((int)$check->fetchColumn()>0) 
                {
                    echo "cannot delete this member because borrowing recode exist\n";
                    echo "You can not deactive the member instead\n";
                    return;
                }

                $stmt=$this->conn->prepare("DELETE FROM members WHERE member_id=:id");

                $stmt->execute([':id'=>$memberId]);

                if($stmt->rowCount()>0) 
                    {
                        echo "Member deleted successfully\n";
                    }else{
                        echo "Member id  not fount\n";
                    }
        }catch(Exception $e)
        {
                echo "Error deleting member".$e->getMessage(). "\n";
        }
    }
}
?>