
<?php

class PatientController extends Controller
{
    private $patientModel;


    public function __construct()
    {
        $this->patientModel = new Patient();
    }


    // Check user permissions
    private function authorize()
    {
        Auth::requireRole(['admin','receptionist' ]);
    }


    
    // Display patient list
    public function index()
    {
        $this->authorize();

        $keyword = trim($_GET['search'] ?? '');

        if ($keyword !== '') {

            $patients =
                $this->patientModel->searchPatients($keyword);

        } else {

            $patients =
                $this->patientModel->getAllPatients();

        }

        $this->view('patients/index', [

            'patients' => $patients,

            'keyword' => $keyword,

            'csrfToken' => Auth::csrfToken()

        ]);
    }


    // Display registration form
    public function create()
    {
        $this->authorize();

        $this->view('patients/form', [

            'patient' => null,

            'pageTitle' => 'Register New Patient',

            'formAction' => '/patients/store',

            'csrfToken' => Auth::csrfToken(),

            'error' => null

        ]);
    }


    // Save patient
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

        $data = $this->getFormData();

        $error = $this->validatePatient($data);

        if ($error !== null) {

            $this->showFormError(
                $data,
                $error,
                false
            );

            return;
        }

        try {

            $patientNumber =
                $this->patientModel->addPatient($data);

            header(
                'Location: /patients?registered=' .
                urlencode($patientNumber)
            );

            exit;

        } catch (Throwable $e) {

            error_log($e->getMessage());

            $this->showFormError(
                $data,
                'Unable to register patient. Please try again.',
                false
            );
        }
    }


    // Display individual patient
    public function show()
    {
        $this->authorize();

        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$id || $id < 1) {
            http_response_code(400);
            exit('Invalid patient ID.');
        }

        $patient =
            $this->patientModel->getPatientById($id);

        if (!$patient) {
            http_response_code(404);
            exit('Patient not found.');
        }

        $this->view('patients/show', [

            'patient' => $patient

        ]);
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
            exit('Invalid patient ID.');
        }

        $patient =
            $this->patientModel->getPatientById($id);

        if (!$patient) {
            http_response_code(404);
            exit('Patient not found.');
        }

        $this->view('patients/form', [

            'patient' => $patient,

            'pageTitle' => 'Edit Patient',

            'formAction' => '/patients/update?id=' . $id,

            'csrfToken' => Auth::csrfToken(),

            'error' => null

        ]);
    }


    // Update patient
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

        if (!$id || $id < 1) {
            http_response_code(400);
            exit('Invalid patient ID.');
        }

        $patient =
            $this->patientModel->getPatientById($id);

        if (!$patient) {
            http_response_code(404);
            exit('Patient not found.');
        }

        $data = $this->getFormData();

        $error = $this->validatePatient($data);

        if ($error !== null) {

            $this->showFormError(
                $data,
                $error,
                true,
                $id
            );

            return;
        }

        try {

            $this->patientModel->updatePatient(
                $id,
                $data
            );

            header('Location: /patients');
            exit;

        } catch (Throwable $e) {

            error_log($e->getMessage());

            $this->showFormError(
                $data,
                'Unable to update patient.',
                true,
                $id
            );
        }
    }


    // Activate / Deactivate patient
    public function toggleStatus()
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

        $status = $_POST['status'] ?? '';

        if (
            !$id ||
            $id < 1 ||
            !in_array((string)$status, ['0', '1'], true)
        ) {
            http_response_code(400);
            exit('Invalid request.');
        }

        $patient =
            $this->patientModel->getPatientById($id);

        if (!$patient) {
            http_response_code(404);
            exit('Patient not found.');
        }

        $this->patientModel->changeStatus(
            $id,
            (int)$status
        );

        header('Location: /patients');
        exit;
    }


    // Collect form data
    private function getFormData()
    {
        return [

            'first_name' =>
                trim($_POST['first_name'] ?? ''),

            'last_name' =>
                trim($_POST['last_name'] ?? ''),

            'gender' =>
                $_POST['gender'] ?? '',

            'date_of_birth' =>
                $_POST['date_of_birth'] ?? '',

            'blood_group' =>
                $_POST['blood_group'] ?? 'Unknown',

            'phone' =>
                trim($_POST['phone'] ?? ''),

            'email' =>
                trim($_POST['email'] ?? ''),

            'address' =>
                trim($_POST['address'] ?? ''),

            'emergency_contact_name' =>
                trim($_POST['emergency_contact_name'] ?? ''),

            'emergency_contact_phone' =>
                trim($_POST['emergency_contact_phone'] ?? '')

        ];
    }


    // Validate patient details
    private function validatePatient($data)
    {
        if (
            $data['first_name'] === '' ||
            $data['last_name'] === '' ||
            $data['gender'] === '' ||
            $data['date_of_birth'] === '' ||
            $data['phone'] === ''
        ) {
            return 'Please fill in all required fields.';
        }


        if (
            !in_array(
                $data['gender'],
                ['Male', 'Female', 'Other'],
                true
            )
        ) {
            return 'Invalid gender selection.';
        }


        $bloodGroups = [
            'A+', 'A-', 'B+', 'B-',
            'AB+', 'AB-', 'O+', 'O-',
            'Unknown'
        ];

        if (!in_array($data['blood_group'], $bloodGroups, true)) {
            return 'Invalid blood group.';
        }


        if (
            $data['email'] !== '' &&
            !filter_var($data['email'], FILTER_VALIDATE_EMAIL)
        ) {
            return 'Please enter a valid email address.';
        }


        if (
            !preg_match(
                '/^\+?[0-9][0-9\s\-]{6,18}[0-9]$/',
                $data['phone']
            )
        ) {
            return 'Please enter a valid phone number.';
        }


        $date = DateTime::createFromFormat(
            '!Y-m-d',
            $data['date_of_birth']
        );

        if (
            !$date ||
            $date->format('Y-m-d') !== $data['date_of_birth'] ||
            $date > new DateTime('today')
        ) {
            return 'Please enter a valid date of birth.';
        }


        if (
            $data['emergency_contact_phone'] !== '' &&
            !preg_match(
                '/^\+?[0-9][0-9\s\-]{6,18}[0-9]$/',
                $data['emergency_contact_phone']
            )
        ) {
            return 'Please enter a valid emergency contact number.';
        }


        if (
            $data['emergency_contact_name'] === '' &&
            $data['emergency_contact_phone'] !== ''
        ) {
            return 'Enter the emergency contact name.';
        }

        return null;
    }


    // Display form with error
    private function showFormError(
        $data,
        $message,
        $isEdit,
        $id = null
    ) {
        $this->view('patients/form', [

            'patient' => $data,

            'pageTitle' => $isEdit
                ? 'Edit Patient'
                : 'Register New Patient',

            'formAction' => $isEdit
                ? '/patients/update?id=' . $id
                : '/patients/store',

            'csrfToken' => Auth::csrfToken(),

            'error' => $message

        ]);
    }

}
