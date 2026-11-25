
<?php

class DoctorController extends Controller
{
    private $doctorModel;
    private $patientModel;


    public function __construct()
    {
        $this->doctorModel = new Doctor();
        $this->patientModel= new Patient();
    }


    // Check Admin permission
    private function authorize()
    {
        Auth::requireRole('admin');
    }

    // Display patients assigned to the logged-in doctor
public function myPatients()
{
    // Only doctors can access this page
    Auth::requireRole('doctor');

    // Get logged-in user
    $user = Auth::user();

    if (!$user) {
        header('Location: /login');
        exit;
    }

    // Find doctor record using user ID
    $doctor = $this->doctorModel->getDoctorByUserId(
        $user['id']
    );

    if (!$doctor) {
        http_response_code(404);
        exit('Doctor profile not found.');
    }

    // Check whether doctor is active
    if ((int)$doctor['status'] !== 1) {
        http_response_code(403);
        exit('Your doctor account is inactive.');
    }

    // Retrieve patients assigned to this doctor
    $patients = $this->patientModel->getPatientsByDoctor(
        $doctor['id']
    );

    // Display My Patients page
    $this->view('doctors/my-patients', [
        'doctor' => $doctor,
        'patients' => $patients
    ]);
}

    // Display an individual patient to the assigned doctor
    public function patientDetails()
    {
        Auth::requireRole('doctor');

        $user = Auth::user();

        if (!$user) {
            header('Location: /login');
            exit;
        }

        $doctor = $this->doctorModel->getDoctorByUserId($user['id']);

        if (!$doctor) {
            http_response_code(404);
            exit('Doctor profile not found.');
        }

        if ((int)$doctor['status'] !== 1) {
            http_response_code(403);
            exit('Your doctor account is inactive.');
        }

        $patientId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$patientId || $patientId < 1) {
            http_response_code(400);
            exit('Invalid patient ID.');
        }

        $patient = $this->patientModel->getPatientForDoctor(
            (int)$patientId,
            (int)$doctor['id']
        );

        if (!$patient) {
            http_response_code(404);
            exit('Patient not found or you are not authorized to view this patient.');
        }

        $this->view('patients/doctor-patient-details', [
            'doctor' => $doctor,
            'patient' => $patient
        ]);
    }

    // Display all doctors
    public function index()
    {
        $this->authorize();

        $doctors = $this->doctorModel->getAllDoctors();

        $this->view('doctors/index', [
            'doctors' => $doctors,
            'csrfToken' => Auth::csrfToken()
        ]);
    }


    // Display doctor registration form
    public function create()
    {
        $this->authorize();

        $departments =
            $this->doctorModel->getDepartments();

        $this->view('doctors/form', [
            'doctor' => null,
            'departments' => $departments,
            'pageTitle' => 'Add New Doctor',
            'formAction' => '/doctors/store',
            'csrfToken' => Auth::csrfToken(),
            'error' => null
        ]);
    }


    // Save new doctor
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

        $error = $this->validateDoctor($data, true);

        if ($error !== null) {
            $this->showFormError($data, $error, false);
            return;
        }

        try {

            $this->doctorModel->addDoctor($data);

            header('Location: /doctors');
            exit;

        } catch (PDOException $e) {

            error_log($e->getMessage());

            if ($e->getCode() == '23000') {
                $message =
                    'Username, email or registration number already exists.';
            } else {
                $message = 'Unable to register doctor.';
            }

            $this->showFormError($data, $message, false);
        }
    }


    // Display edit doctor form
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
            exit('Invalid doctor ID.');
        }

        $doctor = $this->doctorModel->getDoctorById($id);

        if (!$doctor) {
            http_response_code(404);
            exit('Doctor not found.');
        }

        $this->view('doctors/form', [
            'doctor' => $doctor,
            'departments' =>
                $this->doctorModel->getDepartments(),
            'pageTitle' => 'Edit Doctor',
            'formAction' => '/doctors/update?id=' . $id,
            'csrfToken' => Auth::csrfToken(),
            'error' => null
        ]);
    }


    // Update doctor
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
            exit('Invalid doctor ID.');
        }

        $doctor = $this->doctorModel->getDoctorById($id);

        if (!$doctor) {
            http_response_code(404);
            exit('Doctor not found.');
        }

        $data = $this->getFormData();

        $error = $this->validateDoctor($data, false);

        if ($error !== null) {
            $this->showFormError($data, $error, true, $id);
            return;
        }

        try {

            $this->doctorModel->updateDoctor($id, $data);

            header('Location: /doctors');
            exit;

        } catch (PDOException $e) {

            error_log($e->getMessage());

            if ($e->getCode() == '23000') {
                $message =
                    'Email or registration number already exists.';
            } else {
                $message = 'Unable to update doctor.';
            }

            $this->showFormError($data, $message, true, $id);
        }
    }


    // Activate or deactivate doctor
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
            !$id || $id < 1 ||
            !in_array((string)$status, ['0', '1'], true)
        ) {
            http_response_code(400);
            exit('Invalid request.');
        }

        try {

            $this->doctorModel->changeStatus(
                $id,
                (int)$status
            );

            header('Location: /doctors');
            exit;

        } catch (Throwable $e) {

            error_log($e->getMessage());

            http_response_code(500);
            exit('Unable to change doctor status.');
        }
    }


    // Collect form values
    private function getFormData()
    {
        return [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'department_id' => $_POST['department_id'] ?? '',
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'gender' => $_POST['gender'] ?? '',
            'date_of_birth' => $_POST['date_of_birth'] ?? '',
            'phone' => trim($_POST['phone'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'qualification' => trim($_POST['qualification'] ?? ''),
            'specialization' => trim($_POST['specialization'] ?? ''),
            'experience' => $_POST['experience'] ?? '',
            'registration_number' =>
                trim($_POST['registration_number'] ?? '')
        ];
    }


    // Validate doctor information
    private function validateDoctor($data, $isCreate)
    {
        foreach ([
            'first_name',
            'last_name',
            'email',
            'department_id',
            'gender',
            'date_of_birth',
            'phone',
            'qualification',
            'specialization',
            'experience',
            'registration_number'
        ] as $field) {

            if (trim((string)$data[$field]) === '') {
                return 'Please fill in all required fields.';
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'Please enter a valid email address.';
        }

        if (
            $isCreate &&
            (
                $data['username'] === '' ||
                strlen($data['password']) < 12
            )
        ) {
            return 'Username is required and password must contain at least 12 characters.';
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

        $departmentId = filter_var(
            $data['department_id'],
            FILTER_VALIDATE_INT
        );

        if (!$departmentId || $departmentId < 1) {
            return 'Please select a valid department.';
        }

        $experience = filter_var(
            $data['experience'],
            FILTER_VALIDATE_INT
        );

        if (
            $experience === false ||
            $experience < 0 ||
            $experience > 80
        ) {
            return 'Experience must be between 0 and 80 years.';
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
            $date >= new DateTime('today')
        ) {
            return 'Please enter a valid date of birth.';
        }

        return null;
    }


    // Re-display form with validation errors
    private function showFormError(
        $data,
        $message,
        $isEdit,
        $id = null
    ) {
        $this->view('doctors/form', [
            'doctor' => $data,
            'departments' =>
                $this->doctorModel->getDepartments(),
            'pageTitle' => $isEdit
                ? 'Edit Doctor'
                : 'Add New Doctor',
            'formAction' => $isEdit
                ? '/doctors/update?id=' . $id
                : '/doctors/store',
            'csrfToken' => Auth::csrfToken(),
            'error' => $message
        ]);
    }



    // Display doctor password reset page
    public function showResetPassword()
    {
        $this->authorize();

        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!$id || $id < 1) {
            http_response_code(400);
            exit('Invalid doctor ID.');
        }

        $doctor = $this->doctorModel->getDoctorById($id);

        if (!$doctor) {
            http_response_code(404);
            exit('Doctor not found.');
        }

        $this->view('doctors/reset-password', [
            'doctor' => $doctor,
            'csrfToken' => Auth::csrfToken(),
            'error' => null
        ]);
    }



    // Reset doctor's password
    public function resetPassword($id)
    {
        // Only admin can reset doctor passwords
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method not allowed.');
        }

        // Verify CSRF token
        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(419);
            exit('Invalid security token. Please try again.');
        }

        $newPassword = $_POST['new_password'] ?? '';

        // Validate password
        if (strlen($newPassword) < 8) {
            $_SESSION['error'] = 'Password must contain at least 8 characters.';
           header('Location: /doctors');
            exit;
        }

        try {
            $this->doctorModel->resetDoctorPassword(
                $id,
                $newPassword
            );

            $_SESSION['success'] = 'Doctor password reset successfully.';

        } catch (Throwable $e) {

            $_SESSION['error'] = 'Unable to reset doctor password.';
        }

        header('Location: /doctors');
        exit;    
        }

      

}
