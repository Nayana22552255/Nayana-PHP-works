
<?php

class NurseController extends Controller
{
    private $nurseModel;

    public function __construct()
    {
        $this->nurseModel = new Nurse();
    }

    // Only administrators can manage nurse records
    private function authorize()
    {
        Auth::requireRole(['admin']);
    }

    // Display all nurses
    public function index()
    {
        $this->authorize();

        $keyword = trim($_GET['keyword'] ?? '');

        $nurses = $this->nurseModel->getAllNurses($keyword);

        $this->view('nurses/index', [
            'title' => 'Nurse Management',
            'nurses' => $nurses,
            'keyword' => $keyword,
            'csrfToken' => Auth::csrfToken()
        ]);
    }

    // Display the add nurse form
    // Display the add nurse form
    public function create()
    {
        $this->authorize();

        $departments = $this->nurseModel->getActiveDepartments();

        $this->view('nurses/create', [
            'title' => 'Add New Nurse',
            'departments' => $departments,
            'csrfToken' => Auth::csrfToken()
        ]);
    }

    // Save a new nurse
    // Save a new nurse and automatically create login account
        public function store()
        {
            $this->authorize();

            if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
                http_response_code(419);
                exit('Invalid or expired form token.');
            }

            $data = [
                'nurse_number' => trim($_POST['nurse_number'] ?? ''),
                'username' => trim($_POST['username'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'department_id' => (int) ($_POST['department_id'] ?? 0),
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name' => trim($_POST['last_name'] ?? ''),
                'gender' => trim($_POST['gender'] ?? ''),
                'qualification' => trim($_POST['qualification'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'experience_years' => $_POST['experience_years'] ?? 0,
                'shift' => trim($_POST['shift'] ?? ''),
                'address' => trim($_POST['address'] ?? '')
            ];

            if (
                $data['nurse_number'] === '' ||
                $data['username'] === '' ||
                $data['password'] === '' ||
                $data['department_id'] <= 0 ||
                $data['first_name'] === '' ||
                $data['last_name'] === '' ||
                $data['email'] === ''
            ) {
                exit('Please fill in all required fields.');
            }

            if (strlen($data['password']) < 8) {
                exit('Password must contain at least 8 characters.');
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                exit('Please enter a valid email address.');
            }

            if (
                !is_numeric($data['experience_years']) ||
                $data['experience_years'] < 0 ||
                $data['experience_years'] > 999.9
            ) {
                exit('Please enter valid nursing experience.');
            }

            try {
                $this->nurseModel->addNurse($data);

                header('Location: /nurses');
                exit;

            } catch (PDOException $e) {
                error_log($e->getMessage());
                http_response_code(400);
                exit('Unable to add nurse. The username, email or nurse number may already exist.');
            }
        }

    // Display an individual nurse
    public function show()
    {
        $this->authorize();

        $id = (int) ($_GET['id'] ?? 0);

        $nurse = $this->nurseModel->getNurseById($id);

        if (!$nurse) {
            http_response_code(404);
            exit('Nurse not found.');
        }

        $this->view('nurses/show', [
            'title' => 'Nurse Details',
            'nurse' => $nurse
        ]);
    }

    // Display the edit nurse form
    public function edit()
    {
        $this->authorize();

        $id = (int) ($_GET['id'] ?? 0);

        $nurse = $this->nurseModel->getNurseById($id);

        if (!$nurse) {
            http_response_code(404);
            exit('Nurse not found.');
        }

        $departments = $this->nurseModel->getActiveDepartments();

        $this->view('nurses/edit', [
            'title' => 'Edit Nurse',
            'nurse' => $nurse,
            'departments' => $departments,
            'csrfToken' => Auth::csrfToken()
        ]);
    }

    // Update nurse details
    public function update()
    {
        $this->authorize();

        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(419);
            exit('Invalid or expired form token.');
        }

        $id = (int) ($_POST['id'] ?? 0);

        $data = [
            'department_id' => (int) ($_POST['department_id'] ?? 0),
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'gender' => trim($_POST['gender'] ?? ''),
            'qualification' => trim($_POST['qualification'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'experience_years' => $_POST['experience_years'] ?? 0,
            'shift' => trim($_POST['shift'] ?? ''),
            'address' => trim($_POST['address'] ?? '')
        ];

        if (
            $id <= 0 ||
            $data['department_id'] <= 0 ||
            $data['first_name'] === '' ||
            $data['last_name'] === ''
        ) {
            exit('Please fill in all required fields.');
        }

        if (
            !is_numeric($data['experience_years']) ||
            $data['experience_years'] < 0 ||
            $data['experience_years'] > 999.9
        ) {
            exit('Please enter valid nursing experience.');
        }

        $this->nurseModel->updateNurse($id, $data);

        header('Location: /nurses');
        exit;
    }

    // Activate or deactivate a nurse
    public function updateStatus()
    {
        $this->authorize();

        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(419);
            exit('Invalid or expired form token.');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if ($id <= 0 || !in_array($status, ['0', '1'], true)) {
            exit('Invalid nurse status.');
        }

        $this->nurseModel->updateStatus($id, (int) $status);

        header('Location: /nurses');
        exit;
    }
}
