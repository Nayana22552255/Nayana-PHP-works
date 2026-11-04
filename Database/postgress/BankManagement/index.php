<?php

require_once 'Database.php';
require_once 'Customer.php';
require_once 'Accounts.php';
require_once 'Deposit.php';
require_once 'Employee.php';
require_once 'Loan.php';

try {
    $database = new Database();
    $conn = $database->connect();
    $customer = new Customer($conn);
    $account = new Accounts($conn);
    $deposit = new Deposit($conn);
    $employee=new Employee($conn);
    $loan=new Loan($conn);

    while (true) {
        echo "\n================================\n";
        echo "Bank Management System\n";
        echo "================================\n";
        echo "1. Customer Management\n";
        echo "2. Account Management\n";
        echo "3. Deposit Management\n";
        echo "4. Employee Management\n";
        echo "5. Loan Management\n";
        echo "0. Exit\n";
        echo "Select a section: ";

        $section = trim((string) fgets(STDIN));

        if ($section === '0') {
            echo "Goodbye.\n";
            break;
        }

        if ($section === '1') {
            while (true) {
                echo "\nCustomer Management\n";
                echo "1. Display all customers\n";
                echo "2. Add a customer\n";
                echo "3. Search customers by name or email\n";
                echo "4. Update a customer\n";
                echo "5. Delete a customer\n";
                echo "0. Back to main menu\n";
                echo "Select an option: ";

                $choice = trim((string) fgets(STDIN));

                if ($choice === '0') {
                    break;
                }

                try {
                    switch ($choice) {
                        case '1':
                            $customers = $customer->getCustomers();
                            if (empty($customers)) {
                                echo "No customers found.\n";
                            } else {
                                foreach ($customers as $customerRecord) {
                                    print_r($customerRecord);
                                    echo "-------------------------\n";
                                }
                            }
                            break;

                        case '2':
                            echo "Customer name: ";
                            $name = trim((string) fgets(STDIN));
                            echo "Email: ";
                            $email = trim((string) fgets(STDIN));
                            echo "Phone: ";
                            $phone = trim((string) fgets(STDIN));
                            echo "Address: ";
                            $address = trim((string) fgets(STDIN));

                            if ($customer->addCustomer($name, $email, $phone, $address)) {
                                echo "Customer added successfully.\n";
                            } else {
                                echo "Customer could not be added.\n";
                            }
                            break;

                        case '3':
                            echo "Search name or email: ";
                            $keyword = trim((string) fgets(STDIN));
                            $results = $customer->searchCustomers($keyword);
                            if (empty($results)) {
                                echo "No matching customers found.\n";
                            } else {
                                foreach ($results as $customerRecord) {
                                    print_r($customerRecord);
                                    echo "-------------------------\n";
                                }
                            }
                            break;

                        case '4':
                            echo "Customer ID to update: ";
                            $customerId = filter_var(trim((string) fgets(STDIN)), FILTER_VALIDATE_INT);
                            if ($customerId === false || $customerId < 1) {
                                echo "Enter a valid positive customer ID.\n";
                                break;
                            }

                            $existingCustomer = $customer->getCustomerById($customerId);
                            if (!$existingCustomer) {
                                echo "Customer not found.\n";
                                break;
                            }

                            echo "New customer name: ";
                            $name = trim((string) fgets(STDIN));
                            echo "New email: ";
                            $email = trim((string) fgets(STDIN));
                            echo "New phone: ";
                            $phone = trim((string) fgets(STDIN));
                            echo "New address: ";
                            $address = trim((string) fgets(STDIN));

                            if ($customer->updateCustomer($customerId, $name, $email, $phone, $address)) {
                                echo "Customer updated successfully.\n";
                            } else {
                                echo "Customer could not be updated.\n";
                            }
                            break;

                        case '5':
                            echo "Customer ID to delete: ";
                            $customerId = filter_var(trim((string) fgets(STDIN)), FILTER_VALIDATE_INT);
                            if ($customerId === false || $customerId < 1) {
                                echo "Enter a valid positive customer ID.\n";
                                break;
                            }

                            if ($customer->deleteCustomer($customerId)) {
                                echo "Customer deleted successfully.\n";
                            } else {
                                echo "Customer could not be deleted.\n";
                            }
                            break;

                        default:
                            echo "Invalid option. Select 0 through 5.\n";
                    }
                } catch (Throwable $e) {
                    echo "Customer operation failed: " . $e->getMessage() . "\n";
                }
            }
        } elseif ($section === '2') {
            while (true) {
                echo "\nAccount Management\n";
                echo "1. Display accounts\n";
                echo "2. Create an account\n";
                echo "3. Find an account by account number\n";
                echo "4. Display a customer's accounts\n";
                echo "5. Update account type\n";
                echo "6. Close an account\n";
                echo "7. Display accounts with customer details\n";
                echo "0. Back to main menu\n";
                echo "Select an option: ";

                $choice = trim((string) fgets(STDIN));

                if ($choice === '0') {
                    break;
                }

                try {
                    switch ($choice) {
                        case '1':
                            $accounts = $account->getAllAccounts();
                            if (empty($accounts)) {
                                echo "No accounts found.\n";
                            } else {
                                print_r($accounts);
                            }
                            break;

                        case '2':
                            echo "Customer ID: ";
                            $customerId = filter_var(trim((string) fgets(STDIN)), FILTER_VALIDATE_INT);
                            if ($customerId === false || $customerId < 1) {
                                echo "Enter a valid positive customer ID.\n";
                                break;
                            }

                            echo "Account type (Savings or Current): ";
                            $accountType = trim((string) fgets(STDIN));
                            if ($account->createAccount($customerId, $accountType)) {
                                echo "Account created successfully.\n";
                            } else {
                                echo "Account could not be created.\n";
                            }
                            break;

                        case '3':
                            echo "Account number: ";
                            $accountNumber = trim((string) fgets(STDIN));
                            $accountRecord = $account->getAccountByNumber($accountNumber);
                            if (!$accountRecord) {
                                echo "Account not found.\n";
                            } else {
                                print_r($accountRecord);
                            }
                            break;

                        case '4':
                            echo "Customer ID: ";
                            $customerId = filter_var(trim((string) fgets(STDIN)), FILTER_VALIDATE_INT);
                            if ($customerId === false || $customerId < 1) {
                                echo "Enter a valid positive customer ID.\n";
                                break;
                            }

                            $customerAccounts = $account->getCustomerAccount($customerId);
                            if (empty($customerAccounts)) {
                                echo "No accounts found for this customer.\n";
                            } else {
                                print_r($customerAccounts);
                            }
                            break;

                        case '5':
                            echo "Account number: ";
                            $accountNumber = trim((string) fgets(STDIN));
                            echo "New account type (Savings or Current): ";
                            $accountType = trim((string) fgets(STDIN));
                            if ($account->updateAccountType($accountNumber, $accountType)) {
                                echo "Account type updated successfully.\n";
                            } else {
                                echo "Account type was not updated.\n";
                            }
                            break;

                        case '6':
                            echo "Account number to close: ";
                            $accountNumber = trim((string) fgets(STDIN));
                            if ($account->closeAccount($accountNumber)) {
                                echo "Account closed successfully.\n";
                            } else {
                                echo "Account was not closed. It may be inactive, not found, or have a non-zero balance.\n";
                            }
                            break;

                        case '7':
                            echo "Customer ID: ";
                            $customerId = filter_var(trim((string) fgets(STDIN)), FILTER_VALIDATE_INT);
                            if ($customerId === false || $customerId < 1) {
                                echo "Enter a valid positive customer ID.\n";
                                break;
                            }

                            $customerAccounts = $account->getAccountsWithCustomer($customerId);
                            if (empty($customerAccounts)) {
                                echo "No accounts found for this customer.\n";
                            } else {
                                print_r($customerAccounts);
                            }
                            break;

                        default:
                            echo "Invalid option. Select 0 through 7.\n";
                    }
                } catch (Throwable $e) {
                    echo "Account operation failed: " . $e->getMessage() . "\n";
                }
            }
        } elseif ($section === '3') {
            while (true) {
                echo "\nDeposit Management\n";
                echo "1. Deposit money\n";
                echo "2. Check account balance\n";
                echo "3. View deposit history\n";
                echo "0. Back to main menu\n";
                echo "Select an option: ";

                $choice = trim((string) fgets(STDIN));

                if ($choice === '0') {
                    break;
                }

                try {
                    switch ($choice) {
                        case '1':
                            echo "Account number: ";
                            $accountNumber = trim((string) fgets(STDIN));
                            echo "Deposit amount: ";
                            $amount = trim((string) fgets(STDIN));
                            $result = $deposit->depositMoney($accountNumber, $amount);
                            echo "Deposit completed successfully.\n";
                            print_r($result);
                            break;

                        case '2':
                            echo "Account number: ";
                            $accountNumber = trim((string) fgets(STDIN));
                            $balance = $deposit->getBalance($accountNumber);
                            if (!$balance) {
                                echo "Account not found.\n";
                            } else {
                                print_r($balance);
                            }
                            break;

                        case '3':
                            echo "Account number: ";
                            $accountNumber = trim((string) fgets(STDIN));
                            $history = $deposit->getDepositoryHistory($accountNumber);
                            if (empty($history)) {
                                echo "No deposit history found.\n";
                            } else {
                                print_r($history);
                            }
                            break;

                        default:
                            echo "Invalid option. Select 0 through 3.\n";
                    }
                } catch (Throwable $e) {
                    echo "Deposit operation failed: " . $e->getMessage() . "\n";
                }
            }
        } elseif($section === '4'){
            
            while(true)
                {
                    echo "\nEmployee Management\n";
                    echo "1.Add an Employee\n";
                    echo "2.View All Employees\n";
                    echo "3.Search Employee\n";
                    echo "4.Update Employee\n";
                    echo "5.Delete Employee\n";
                    echo "0 Back to main menu\n";
                    echo "Select an option: ";

                    $choice=trim((string)fgets(STDIN));
                    
                    if($choice == '0')
                        {
                            break;
                        }
                    try{

                            switch($choice){
                                    case '1':
                                                $name=readline("Enter employee name: ");
                                                $email=readline("Enter email: ");
                                                $role=readline("Enter employee role: ");

                                                if(!filter_var($email,FILTER_VALIDATE_EMAIL))
                                                    {
                                                        echo "Invalid email address\n";
                                                        break;
                                                    }
                                                if(strlen($name)> 50 || strlen($email) >50 || strlen($role)> 30)
                                                    {
                                                        echo "Input exceeds the allowed column length\n";
                                                        break;
                                                    }
                                                $result=$employee->addEmployees($name,$email,$role);
                                                echo  $result ? "Employee added successfully!\n" : "Employee could not be added\n";
                                                break;

                                    case '2':

                                                $employees=$employee->getAllEmployees();
                                                if(empty($employees))
                                                    {
                                                        echo "No Employees Found\n";
                                                        break;
                                                    }
                                                foreach($employees as $emp)
                                                    {
                                                        echo "\n--------------------------\n";
                                                        echo "Employee ID: ".$emp['employee_id']. "\n";
                                                        echo "Name: ".$emp['employee_name']. "\n";
                                                        echo "Email: ".$emp['email']. "\n";
                                                        echo "Role: ".$emp['role']. "\n";

                                                    }
                                                    break;

                                    case '3':
                                                    $id=(int)readline("Enter employe ID: ");
                                                    $emp=$employee->getEmployeeById($id);
                                                    if($emp)
                                                        {
                                                            print_r($emp);
                                                        }else{
                                                            echo "Employee Not Found\n";
                                                        }
                                                    break;
                                    case '4':
                                                    $id=(int)readline("Enter employee Id: ");
                                                    $existing=$employee->getEmployeeById($id);
                                                    if(!$existing)
                                                        {
                                                            echo "Employee not found\n";
                                                            break;
                                                        }
                                                    $name=readline("Enter new employee name: ");
                                                    $email=readline("Enter the email: ");
                                                    $role=readline("enter the role: ");

                                                    if(!filter_var($email,FILTER_VALIDATE_EMAIL))
                                                        {
                                                            echo "Invalid email address.\n";
                                                            break;
                                                        }
                                                    if(strlen($name)> 50 || strlen($email) >50 || strlen($role)> 30)
                                                    {
                                                        echo "Input exceeds the allowed column length\n";
                                                        break;
                                                    }
                                                    $result=$employee->updateEmployee($id,$name,$email,$role);
                                                    echo $result ? "Employee Updated Successfully!\n": "No changes made \n";
                                                    break;

                                    case '5':
                                                $id=(int)readline("Enter employee id to delete: ");
                                                $result=$employee->deleteEmployee($id);
                                                echo $result ? "Employee deleted successfully!\n": "Employee not found\n";
                                                break;
                                    default:
                                                echo "Invalid choice\n";

                            }
                    }catch(Throwable $e)
                    {
                        echo "Employeee management failed".$e->getMessage(). "\n";
                    }


                }
            }elseif($section === '5')
            {
                    while(true)
                        {
                            echo "\nLoan Management\n";
                            echo "1.Apply for Loan\n";
                            echo "2.View All Loans\n";
                            echo "3.Search Loan\n";
                            echo "4.Update Loan Status\n";
                            echo "5.Delete Loan\n";
                            echo "0 Back to main menu\n";
                            echo "Select an option: ";
                
                        
                                $choice=trim((string)fgets(STDIN));
                        
                                if($choice == '0')
                                    {
                                        break;
                                    }
                        try{

                            switch($choice)
                            {
                                 case '1':
                                                $customerId=(int)readline("Enter Customer ID: ");
                                                $amount=readline("Enter Loan amount: ");
                                                $rate=readline("Enter Intrerest Rate(%): ");
                                                $result = $loan->applyLoan(
                                                                $customerId,
                                                                $amount,
                                                                $rate);
                                                echo $result ? "Loan application submitted successfully\n":"Loan application failed\n";
                                                break;
                                case '2':
                                                $loans=$loan->getAllLoans();
                                                if(empty($loans))
                                                    {
                                                        echo "No loan records found\n";
                                                        break;
                                                    }
                                                foreach($loans as $record)
                                                    {
                                                        echo "\n-----------------------\n";
                                                        echo "Loan Id: ".$record['loan_id']. "\n";
                                                        echo "Customer Id: ".$record['customer_id']. "\n";
                                                        echo "Customer Name: ".$record['customer_name']. "\n";
                                                        echo "Loan Amount: ".$record['loan_amount']. "\n";
                                                        echo "Interest Rate: ".$record['interest_rate']. "\n";
                                                        echo "Status: ".$record['loan_status']. "\n";
                                                        echo "Created At: ".$record['created_at']. "\n";                            
                                                    }
                                                    break;
                                case '3':
                                                $id=(int)readline("Enter Loan ID: ");
                                                $record=$loan->getLoanById($id);
                                                if($record)
                                                    {
                                                        print_r($record);
                                                    }else{
                                                        echo "Loan not found\n";
                                                    }
                                                break;

                                case '4':
                                                $id=(int)readline("Enter Loan ID: ");
                                                $record=$loan->getLoanById($id);
                                                if(!$record)
                                                    {
                                                        echo "Loan not found\n";
                                                        break;
                                                    }

                                                echo "\nAvailable Status:\n";
                                                echo "1.Pending\n";
                                                echo "2.Approved\n";
                                                echo "3.Rejected\n";
                                                echo "4.Active\n";
                                                echo "5.Closed\n";

                                                $statusChoice=readline("Select status: ");

                                                $statuses=['1'=>'Pending',
                                                            '2'=>'Approved',
                                                            '3'=>'Rejected',
                                                            '4'=>'Active',
                                                            '5'=>'Closed'];

                                                if(!isset($statuses[$statusChoice]))
                                                    {
                                                        echo "invalid status choice \n";
                                                        break;
                                                    }

                                                $result=$loan->updateLoanStatus($id,$statuses[$statusChoice]);
                                                echo $result ? "Loan status updated successfully!\n": "No changes made\n";
                                                break;
                                case '5':

                                                $id=(int)readline("Enter loan id to delete: ");
                                                $result=$loan->deleteLoan($id);
                                                echo $result ? "Loan deleted successfully!\n": "loan not fount\n";
                                                break;

                                default:
                                                echo "please select a correct choice\n";

                                        

                            }
                        }catch(Throwable $e)
                        {
                            echo "Error in loan management". $e->getMessage(). "\n";
                        }
                    }
        
        }else 
        {
            echo "Invalid section.\n";
        }
    }
} catch (Throwable $e) {
    fwrite(STDERR, "Bank Management System error: " . $e->getMessage() . "\n");
    exit(1);
}
