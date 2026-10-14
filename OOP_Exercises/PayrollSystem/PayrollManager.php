
<?php

require_once "Employee.php";

class PayrollManager
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;

        if (!file_exists($this->file)) {
            file_put_contents($this->file, "[]");
        }
    }

    private function loadData()
    {
        $json = file_get_contents($this->file);
        $data = json_decode($json, true);

        return is_array($data) ? $data : [];
    }

    private function saveData($data)
    {
        $json = json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
        );

        if (file_put_contents(
            $this->file,
            $json,
            LOCK_EX
        ) === false) {
            throw new RuntimeException("Unable to save employee data.");
        }
    }

    public function addEmployee(Employee $employee)
    {
        $employees = $this->loadData();

        foreach ($employees as $record) {
            if ((string) $record["id"] === (string) $employee->getId()) {
                echo "Employee ID already exists!\n";
                return;
            }
        }

        $employees[] = $employee->toArray();

        $this->saveData($employees);

        echo "Employee added successfully!\n";
    }

    public function displayEmployees()
    {
        $employees = $this->loadData();

        if (empty($employees)) {
            echo "No employees found.\n";
            return;
        }

        echo "\n========== EMPLOYEE LIST ==========\n";

        foreach ($employees as $record) {
            echo "ID: " . $record["id"] . "\n";
            echo "Name: " . $record["name"] . "\n";
            echo "Department: " . $record["department"] . "\n";
            echo "Net Salary: Rs. " .
                number_format(
                    $record["basicSalary"]
                    + $record["allowance"]
                    - $record["deduction"],
                    2
                ) . "\n";
            echo "-----------------------------------\n";
        }
    }

    public function searchEmployee($id)
    {
        $employees = $this->loadData();

        foreach ($employees as $record) {
            if ((string) $record["id"] === (string) $id) {
                $employee = new Employee(
                    $record["id"],
                    $record["name"],
                    $record["department"],
                    $record["basicSalary"],
                    $record["allowance"],
                    $record["deduction"]
                );

                $employee->displaySalarySlip();
                return;
            }
        }

        echo "Employee not found!\n";
    }
}
