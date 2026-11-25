
<?php

class WardController extends Controller
{
    private $wardModel;

    public function __construct()
    {
        $this->wardModel = new Ward();
    }

    // Display all wards
    public function index()
    {
        Auth::requireRole(['admin']);

        $keyword = trim($_GET['keyword'] ?? '');

        $wards = $this->wardModel->getAllWards($keyword);

        $this->view('wards/index', [
            'title' => 'Ward Management',
            'wards' => $wards,
            'keyword' => $keyword,
            'csrfToken' => Auth::csrfToken()
        ]);
    }

    // Display ward creation form
    public function create()
    {
        Auth::requireRole(['admin']);

        $this->view('wards/create', [
            'title' => 'Add New Ward',
            'csrfToken' => Auth::csrfToken()
        ]);
    }

    // Save a new ward
    public function store()
    {
        Auth::requireRole(['admin']);

        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF token.');
        }

        $data = [
            'ward_name' => trim($_POST['ward_name'] ?? ''),
            'ward_code' => trim($_POST['ward_code'] ?? ''),
            'ward_type' => trim($_POST['ward_type'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];

        if (
            $data['ward_name'] === '' ||
            $data['ward_code'] === '' ||
            $data['ward_type'] === ''
        ) {
            die('Ward name, ward code and ward type are required.');
        }

        try {
            $this->wardModel->addWard($data);
            header('Location: /wards');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die('Unable to create ward. Please check whether the ward code already exists.');
        }
    }

    // Display one ward
    public function show()
    {
        Auth::requireRole(['admin']);

        $id = (int)($_GET['id'] ?? 0);

        $ward = $this->wardModel->getWardById($id);

        if (!$ward) {
            http_response_code(404);
            die('Ward not found.');
        }

        $this->view('wards/show', [
            'title' => 'Ward Details',
            'ward' => $ward
        ]);
    }

    // Display edit form
    public function edit()
    {
        Auth::requireRole(['admin']);

        $id = (int)($_GET['id'] ?? 0);

        $ward = $this->wardModel->getWardById($id);

        if (!$ward) {
            http_response_code(404);
            die('Ward not found.');
        }

        $this->view('wards/edit', [
            'title' => 'Edit Ward',
            'ward' => $ward,
            'csrfToken' => Auth::csrfToken()
        ]);
    }

    // Update ward
    public function update()
    {
        Auth::requireRole(['admin']);

        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF token.');
        }

        $id = (int)($_POST['id'] ?? 0);

        $data = [
            'ward_name' => trim($_POST['ward_name'] ?? ''),
            'ward_code' => trim($_POST['ward_code'] ?? ''),
            'ward_type' => trim($_POST['ward_type'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];

        if (
            $id <= 0 ||
            $data['ward_name'] === '' ||
            $data['ward_code'] === '' ||
            $data['ward_type'] === ''
        ) {
            die('Please provide valid ward details.');
        }

        try {
            $this->wardModel->updateWard($id, $data);
            header('Location: /wards');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die('Unable to update ward. Please check whether the ward code already exists.');
        }
    }

    // Activate or deactivate ward
    public function updateStatus()
    {
        Auth::requireRole(['admin']);

        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            die('Invalid CSRF token.');
        }

        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if ($id <= 0 || !in_array($status, ['0', '1'], true)) {
            die('Invalid ward status.');
        }

        $this->wardModel->updateStatus($id, (int)$status);

        header('Location: /wards');
        exit;
    }
}
