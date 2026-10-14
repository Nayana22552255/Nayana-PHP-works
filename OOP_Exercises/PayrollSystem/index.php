
<?php

require_once "Employee.php";
require_once "PayrollManager.php";

$payroll = new PayrollManager("employees.json");

do {
    echo "\n========== PAYROLL MANAGEMENT SYSTEM ==========\n";
    echo "1. Add Employee\n";
    echo "2. View All Employees\n";
    echo "3. Search Employee and Generate Salary Slip\n";
    echo "4. Exit\n";
    echo "Enter your choice: ";

    $choice = trim(readline());

    switch ($choice) {

        case "1":
            echo "Enter Employee ID: ";
            $id = trim(readline());

            echo "Enter Employee Name: ";
            $name = trim(readline());

            echo "Enter Department: ";
            $department = trim(readline());

            echo "Enter Basic Salary: ";
            $basicSalary = filter_var(
                trim(readline()),
                FILTER_VALIDATE_FLOAT
            );

            echo "Enter Allowance: ";
            $allowance = filter_var(
                trim(readline()),
                FILTER_VALIDATE_FLOAT
            );

            echo "Enter Deduction: ";
            $deduction = filter_var(
                trim(readline()),
                FILTER_VALIDATE_FLOAT
            );

            if (
                $id === "" ||
                $name === "" ||
                $department === "" ||
                $basicSalary === false ||
                $allowance === false ||
                $deduction === false ||
                $basicSalary < 0 ||
                $allowance < 0 ||
                $deduction < 0 ||
                $deduction > $basicSalary + $allowance
            ) {
                echo "Invalid input. Please enter valid employee details and salary amounts.\n";
                break;
            }

            $employee = new Employee(
                $id,
                $name,
                $department,
                $basicSalary,
                $allowance,
                $deduction
            );

            $payroll->addEmployee($employee);
            break;

        case "2":
            $payroll->displayEmployees();
            break;

        case "3":
            echo "Enter Employee ID: ";
            $id = trim(readline());

            $payroll->searchEmployee($id);
            break;

        case "4":
            echo "Exiting Payroll Management System.\n";
            break;

        default:
            echo "Invalid choice. Try again.\n";
    }

} while ($choice !== "4");
