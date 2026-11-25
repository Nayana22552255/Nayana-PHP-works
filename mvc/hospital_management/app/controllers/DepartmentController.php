
<?php

class DepartmentController extends Controller
{
    private $departmentModel;


    // Constructor only admin can manage this
    public function __construct()
    {
        $this->departmentModel = new Department();
    }

    // Check permission when an action is requested
    private function authorize()
    {
        Auth::requireRole('admin');
    }


    // Display all departments
    public function index()
    {
        
        $this->authorize();
        $departments =
            $this->departmentModel->getAllDepartments();

        $this->view('departments/index', [
            'departments' => $departments,
            'csrfToken' => Auth::csrfToken()
        ]);
    }


    // Display add department form
    public function create()
    {
        
        $this->authorize();
        $this->view('departments/form', [
            'department' => null,
            'pageTitle' => 'Add Department',
            'formAction' => '/departments/store',
            'csrfToken' => Auth::csrfToken(),
            'error' => null
        ]);
    }


    // Save department
    public function store()
    {
        
        $this->authorize();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(419);
            exit('Invalid form token.');
        }

        $name = trim($_POST['department_name'] ?? '');

        $description = trim($_POST['description'] ?? '');

        if ($name === '') {

            $this->view('departments/form', [
                'department' => $_POST,
                'pageTitle' => 'Add Department',
                'formAction' => '/departments/store',
                'csrfToken' => Auth::csrfToken(),
                'error' => 'Department name is required.'
            ]);

            return;
        }

        try {

            $this->departmentModel->addDepartment(
                $name,
                $description
            );

            header('Location: /departments');
            exit;

        } catch (PDOException $e) {

            if ($e->getCode() == '23000') {

                $error = 'This department already exists.';

            } else {

                error_log($e->getMessage());

                $error = 'Unable to save department.';
            }

            $this->view('departments/form', [
                'department' => $_POST,
                'pageTitle' => 'Add Department',
                'formAction' => '/departments/store',
                'csrfToken' => Auth::csrfToken(),
                'error' => $error
            ]);
        }
    }


    // Display edit form
    public function edit()
    {
        $this->authorize();
        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$id || $id < 1) {
            http_response_code(400);
            exit('Invalid department ID.');
        }

        $department =
            $this->departmentModel->getDepartmentById($id);

        if (!$department) {
            http_response_code(404);
            exit('Department not found.');
        }

        $this->view('departments/form', [
            'department' => $department,
            'pageTitle' => 'Edit Department',
            'formAction' => '/departments/update?id=' . $id,
            'csrfToken' => Auth::csrfToken(),
            'error' => null
        ]);
    }


    // Update department
    public function update()
    {
        
        $this->authorize();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(419);
            exit('Invalid form token.');
        }

        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        $name = trim($_POST['department_name'] ?? '');

        $description = trim($_POST['description'] ?? '');

        $status = $_POST['status'] ?? '';

        if (
            !$id || $id < 1 ||
            $name === '' ||
            !in_array((string)$status, ['0', '1'], true)
        ) {
            http_response_code(422);
            exit('Invalid department information.');
        }

        $department =
            $this->departmentModel->getDepartmentById($id);

        if (!$department) {
            http_response_code(404);
            exit('Department not found.');
        }

        try {

            $this->departmentModel->updateDepartment(
                $id,
                $name,
                $description,
                (int)$status
            );

            header('Location: /departments');
            exit;

        } catch (PDOException $e) {

            if ($e->getCode() == '23000') {
                http_response_code(409);
                exit('This department name already exists.');
            }

            error_log($e->getMessage());

            http_response_code(500);
            exit('Unable to update department.');
        }
    }


    // Delete department
    public function delete()
    {
        
        $this->authorize();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(419);
            exit('Invalid form token.');
        }

        $id = filter_input(
            INPUT_POST,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$id || $id < 1) {
            http_response_code(400);
            exit('Invalid department ID.');
        }

        $department =
            $this->departmentModel->getDepartmentById($id);

        if (!$department) {
            http_response_code(404);
            exit('Department not found.');
        }

        try {

            $this->departmentModel->deleteDepartment($id);

            header('Location: /departments');
            exit;

        } catch (PDOException $e) {

            error_log($e->getMessage());

            http_response_code(409);

            exit(
                'This department cannot be deleted because it may be associated with other records.'
            );
        }
    }

}
