<?php

require_once 'Database.php';
require_once 'Product.php';
require_once 'Stock.php';
require_once 'Supplier.php';
require_once 'Transaction.php';



    $database=new Database();
    $conn=$database->connect();
    $product=new Product($conn);
    $stock=new Stock($conn);
    $supplier=new Supplier($conn);
    $transaction=new Transaction($conn);

function showData(array $data): void 
{
        if(empty($data))
            {
                echo "No records Found!\n";
                return;
            }
        foreach($data as $row)
            {
                foreach($row as $key =>$values)
                    {
                        echo ucfirst($key). ":".($values ?? "NULL"). "\n";
                    }

                echo "--------------------------------------\n";
            }
}

function productSection(Product $product, Supplier $supplier): void
{

            echo "\n-----------------------------\n";
            echo "-------Product Management-----\n";
            echo "1. Add a new Product\n";
            echo "2. View all Product\n";
            echo "3. Update the Product Price\n";
            echo "4. Delete Product\n";
            echo "----------------------------------\n";

            $choice=(int)readline("Enter a Choice: ");

            switch($choice)
                {
                        case 1:

                                    $name=readline("Enter the product name: ");
                                    $categories=$product->getCategories();
                                    if(empty($categories))
                                        {
                                            echo "No categories found. Add a category before adding a product.\n";
                                            break;
                                        }

                                    echo "Available Categories:\n";
                                    foreach($categories as $categoryRecord)
                                        {
                                            echo $categoryRecord['category_id']. ". ".$categoryRecord['category_name']."\n";
                                        }

                                    $category=(int)readline("Enter the Category ID: ");
                                    $categoryExists=false;
                                    foreach($categories as $categoryRecord)
                                        {
                                            if((int)$categoryRecord['category_id']===$category)
                                                {
                                                    $categoryExists=true;
                                                    break;
                                                }
                                        }
                                    if(!$categoryExists)
                                        {
                                            echo "Invalid category ID. Product was not added.\n";
                                            break;
                                        }

                                    $suppliers=$supplier->displaySuppliers();
                                    if(empty($suppliers))
                                        {
                                            echo "No suppliers found. Add a supplier before adding a product.\n";
                                            break;
                                        }

                                    echo "Available Suppliers:\n";
                                    foreach($suppliers as $supplierRecord)
                                        {
                                            echo $supplierRecord['supplier_id']. ". ".$supplierRecord['supplier_name']."\n";
                                        }

                                    $supplierId=(int)readline("Enter the Supplier ID: ");
                                    $supplierExists=false;
                                    foreach($suppliers as $supplierRecord)
                                        {
                                            if((int)$supplierRecord['supplier_id']===$supplierId)
                                                {
                                                    $supplierExists=true;
                                                    break;
                                                }
                                        }
                                    if(!$supplierExists)
                                        {
                                            echo "Invalid supplier ID. Product was not added.\n";
                                            break;
                                        }

                                    $price=(float) readline("Enter the Price: ");
                                    $quantity=(int)readline("Enter the product Quantity: ");
                                    $minimumstock=(int)readline("Enter The Minimum Stocks: ");

                                    if($product->addProduct($name,$category,$supplierId,$price,$quantity,$minimumstock))
                                        {
                                            echo "Product added successfully\n";
                                        }
                                    else
                                        {
                                            echo "Product could not be added\n";
                                        }
                                    break;

                        case 2:

                                    showData($product->getProduct());
                                    break;

                        case 3:

                                    $id=(int)readline("Enter the Product Id: ");
                                    $price=(float)readline("Enter the Price: ");
                                    $product->updatePrice($id,$price);
                                    echo "Price updated successfully\n";
                                    break;

                        case 4:

                                    $id=(int)readline("Enter the product ID: ");
                                    $product->deleteProduct($id);
                                    echo "Product Deleted Successfully\n";
                                    break;

                        default: 
                                    echo "Inavlid choice !\n";

                }
}

function supplierSection(Supplier $supplier): void
{
            echo "\n----------------------------\n";
            echo "--------Supplier Management-------\n";
            echo "1. Add Suppliers\n";
            echo "2. View All suppliers\n";
            echo "3. Update supplier Phone number\n";
            echo "----------------------------------------\n";

            $choice=(int)readline("Enter yor choice: ");

            switch($choice)
            {
                case 1:

                            $name=readline("Enter the Supplier name: ");
                            $phone=readline("Enter the Supplier Phone number: ");
                            $email=readline("Enter the Suppler email: ");
                            $address=readline("Enter the suppliers Address: ");
                            $supplier->addSuppliers($name,$phone,$email,$address);
                            echo "Suppliers added Successfully\n";
                            break;

                case 2 :

                            showData($supplier->displaySuppliers());
                            break;

                case 3 :

                            $id=(int)readline("Enter the Supplier Id: ");
                            $phone=readline("Enter the Phone Number: ");
                            if($supplier->updatePhone($id,$phone))
                                {
                                    echo "Supplier phone updated successfully\n";
                                }
                            else
                                {
                                    echo "Supplier phone was not updated\n";
                                }
                            break;

                default:
                            echo "Invalid Choice!\n";

            }
}

function stockSection(Stock $stock): void
{
                echo "\n-------------------------------\n";
                echo "--------Stock Management------------\n";
                echo "1. Stock IN\n";
                echo "2. Stock OUT\n";
                echo "3. Low Stock Alert\n";

                $choice=(int)readline("Enter Choice: ");
                switch($choice)
                {
                        case 1 :

                        case 2 :
                                    $id=(int)readline("Product ID: ");
                                    $quantity=(int)readline("Quantity: ");
                                    $type=($choice == 1)? 'IN' : 'OUT';
                                    try
                                        {
                                            if($stock->manageStock($id,$type,$quantity))
                                                {
                                                    echo "Stock updated successfully!\n";
                                                }
                                        }
                                    catch(Throwable $e)
                                        {
                                            echo "Stock update failed: ".$e->getMessage()."\n";
                                        }
                                    break;

                        case 3 :
                                    showData($stock->lowStock());
                                    break;
                        default :
                                    echo "Invalid Choice!";
                }
}

function  transactionSection(Transaction $transaction): void
{
            echo "\n--------------------------\n";
            echo "-----Transaction Management-------\n";
            echo "1. Display All Transaction\n";
            echo "2. Display Stock IN Transactions\n";
            echo "3. Display Stock OUT Transactions\n";
            echo "---------------------------------------\n";

            $choice=(int)readline("Enter choice: ");

            switch($choice)
            {
                        case 1 :
                                    showData($transaction->displayTransactions());
                                    break;

                        case 2 :

                                    showData($transaction->filterTransaction("IN"));
                                    break;

                        case 3 :
                                    showData($transaction->filterTransaction("OUT"));
                                    break;
                        default:
                                    echo "Invalid Choice!";
            }
}

while(true)
    {
        echo "=========Inventory Management System==========\n";
        echo "1. Product Management\n";
        echo "2. Supplier Management\n";
        echo "3. Stock Management\n";
        echo "4. Transaction Management\n";
        echo "0. Exit\n";

        $choice=(int)readline("Enter Your Choice: ");
        switch($choice)
        {
            case 1 :
                        productSection($product,$supplier);
                        break;
            case 2 :
                        supplierSection($supplier);
                        break;
            case 3 :
                        stockSection($stock);
                        break;
            case 4 :
                        transactionSection($transaction);
                        break;
            default :
                        echo "Invalid choice!";

        }
    }