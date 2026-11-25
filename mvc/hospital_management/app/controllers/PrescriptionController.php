
<?php

class PrescriptionController extends Controller
{
    private $prescription;
    private $doctorModel;

    public function __construct()
    {
        $this->prescription = new Prescription();
        $this->doctorModel=new Doctor();
    }


    // Check user access
    private function authorize()
    {
        Auth::requireRole(['admin', 'doctor']);
    }


    // 1. Display all prescriptions
    public function index()
    {
        $this->authorize();

        $keyword = trim($_GET['search'] ?? '');

        $prescriptions = $this->prescription
            ->getAllPrescriptions($keyword);

        $this->view('prescriptions/index', [
            'title' => 'Prescription Management',
            'prescriptions' => $prescriptions,
            'keyword' => $keyword
        ]);
    }


    // 2. Display prescription creation form
    
public function create()
{
    // Allow only logged-in doctors
    Auth::requireRole('doctor');

    // Get logged-in user
    $user = Auth::user();

    // Find the doctor's profile
    $doctor = $this->doctorModel->getDoctorByUserId($user['id']);

    if (!$doctor || (int)$doctor['status'] !== 1) {
        http_response_code(403);
        exit('Active doctor profile not found.');
    }

    // Get only patients who have appointments with this doctor
    $patients = $this->prescription->getPatientsByDoctor(
        $doctor['id']
    );

    // Display prescription creation form
    $this->view('prescriptions/create', [
        'title' => 'Create Prescription',
        'patients' => $patients,
        'doctor' => $doctor,
        'csrfToken' => Auth::csrfToken()
    ]);
}


    // 3. Store a new prescription
    
public function store()
{
    Auth::requireRole('doctor');

    // Verify CSRF token
    if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        exit('Invalid security token.');
    }

    // Get logged-in doctor
    $user = Auth::user();

    $doctor = $this->doctorModel->getDoctorByUserId($user['id']);
   

    if (!$doctor || (int)$doctor['status'] !== 1) {
        http_response_code(403);
        exit('Active doctor profile not found.');
    }

    // Assign doctor ID automatically
    $data = [
        'patient_id' => (int)($_POST['patient_id'] ?? 0),
        'doctor_id' => (int)$doctor['id'],
        'appointment_id' => $_POST['appointment_id'] ?? null,
        'prescription_date' => trim($_POST['prescription_date'] ?? ''),
        'diagnosis' => trim($_POST['diagnosis'] ?? ''),
        'notes' => trim($_POST['notes'] ?? ''),
        'items' => $_POST['items'] ?? []
    ];

    // Validate prescription
    $error = $this->validatePrescription($data);

    if ($error !== null) {
        $this->showError($error);
        return;
    }

    try {
        $prescriptionId = $this->prescription->createPrescription($data);

       header('Location: /doctor/prescriptions');
exit;

    } catch (Throwable $e) {
        $this->showError(
            'Unable to create prescription: ' . $e->getMessage()
        );
    }
}


    // 4. Display prescription details
    public function show()
    {
        $this->authorize();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            $this->showError('Invalid prescription ID.');
            return;
        }

        $prescription = $this->prescription
            ->getPrescriptionById($id);

        if (!$prescription) {
            $this->showError('Prescription not found.');
            return;
        }

        $items = $this->prescription
            ->getPrescriptionItems($id);

        $this->view('prescriptions/show', [
            'title' => 'Prescription Details',
            'prescription' => $prescription,
            'items' => $items,
            'csrfToken' => Auth::csrfToken()
        ]);
    }


    // 5. Update prescription status
    public function updateStatus()
    {
        $this->authorize();

        if (
            !isset($_POST['csrf_token']) ||
            !Auth::verifyCsrf($_POST['csrf_token'])
        ) {
            http_response_code(403);
            exit('Invalid security token. Please refresh the page and try again.');
        }

        $id = filter_var(
            $_POST['prescription_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $status = trim($_POST['status'] ?? '');

        if (!$id || $id < 1) {
            $this->showError('Invalid prescription ID.');
            return;
        }

        try {
            $this->prescription->updateStatus($id, $status);

            //header('/');

            header(
                'Location: /prescriptions/show?id=' . $id
            );
            exit;

        } catch (Throwable $e) {
            $this->showError(
                'Unable to update prescription: ' . $e->getMessage()
            );
        }
    }


    // 6. Validate prescription details
    private function validatePrescription($data)
    {
      
    if ($data['patient_id'] < 1) {
            return 'Please select a patient.';
        }

        if (
    !isset($data['doctor_id']) ||
    (int)$data['doctor_id'] < 1
) {
    return 'Doctor information is missing. Please log in again.';
}

        if (empty($data['prescription_date'])) {
            return 'Please select a prescription date.';
        }

        $date = DateTime::createFromFormat(
            'Y-m-d',
            $data['prescription_date']
        );

        if (
            !$date ||
            $date->format('Y-m-d') !== $data['prescription_date']
        ) {
            return 'Please enter a valid prescription date.';
        }

        if (
            !empty($data['appointment_id']) &&
            (
                filter_var(
                    $data['appointment_id'],
                    FILTER_VALIDATE_INT
                ) === false ||
                (int) $data['appointment_id'] < 1
            )
        ) {
            return 'Please enter a valid appointment ID.';
        }

        if (
            !is_array($data['items']) ||
            count($data['items']) === 0
        ) {
            return 'Please add at least one medicine.';
        }

        foreach ($data['items'] as $item) {

            if (
                !isset($item['medicine_name']) ||
                trim($item['medicine_name']) === ''
            ) {
                return 'Every medicine must have a name.';
            }

            if (
                empty($item['dosage']) ||
                empty($item['frequency']) ||
                empty($item['duration'])
            ) {
                return 'Dosage, frequency and duration are required for every medicine.';
            }

            if (
                !isset($item['quantity']) ||
                filter_var(
                    $item['quantity'],
                    FILTER_VALIDATE_INT
                ) === false ||
                (int) $item['quantity'] < 1
            ) {
                return 'Every medicine must have a valid quantity.';
            }
        }

        return null;
    }


    // 7. Display an error message
    private function showError($message)
    {
        http_response_code(400);

        echo '<h2>Prescription Error</h2>';

        echo '<p>' . htmlspecialchars(
            $message,
            ENT_QUOTES,
            'UTF-8'
        ) . '</p>';

        echo '<p><a href="/prescriptions">Back to Prescriptions</a></p>';
    }

    
public function doctorPrescriptions()
{
    // Allow only doctors
    Auth::requireRole('doctor');

    $user = Auth::user();

    if (!$user) {
        header('Location: /login');
        exit;
    }

    // Get the logged-in doctor's profile
    $doctor = $this->doctorModel->getDoctorByUserId($user['id']);

    if (!$doctor) {
        http_response_code(404);
        exit('Doctor profile not found.');
    }

    // Check doctor account status
    if ((int)$doctor['status'] !== 1) {
        http_response_code(403);
        exit('Your doctor account is inactive.');
    }

    // Retrieve prescriptions belonging to this doctor
    $prescriptions = $this->prescription
        ->getPrescriptionsByDoctor($doctor['id']);

    $data = [
        'doctor' => $doctor,
        'prescriptions' => $prescriptions
    ];

    if (
        isset($_GET['partial']) &&
        $_GET['partial'] === '1'
    ) {
        $this->view('prescriptions/doctor-index-content', $data);
        return;
    }

    // Full page view
    $this->view('prescriptions/doctor-index', $data);
}


public function doctorPrescriptionDetails()
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

    // Validate prescription ID
    $prescriptionId = isset($_GET['id'])
        ? filter_var($_GET['id'], FILTER_VALIDATE_INT)
        : false;

    if (!$prescriptionId || $prescriptionId <= 0) {
        http_response_code(400);
        exit('Invalid prescription ID.');
    }

    // Retrieve prescription and medicine details
    $prescription = $this->prescription
        ->getPrescriptionDetailsForDoctor(
            $prescriptionId,
            $doctor['id']
        );

    if (!$prescription) {
        http_response_code(404);
        exit('Prescription not found.');
    }

    $data = [
        'doctor' => $doctor,
        'prescription' => $prescription
    ];

    // Partial content for dashboard
    if (
        isset($_GET['partial']) &&
        $_GET['partial'] === '1'
    ) {
        $this->view('prescriptions/doctor-show-content', $data);
        return;
    }

    // Full page view
    $this->view('prescriptions/doctor-show', $data);
}


}
