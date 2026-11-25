
<?php

class AppointmentController extends Controller
{
    private $appointmentModel;

    public function __construct()
    {
        $this->appointmentModel = new Appointment();
    }


    // Check user permission
    private function authorize()
    {
        Auth::requireRole(['admin', 'receptionist']);
    }


    // Display all appointments
    public function index()
    {
        $this->authorize();

        $keyword = trim($_GET['search'] ?? '');

        $appointments = $this->appointmentModel
            ->getAllAppointments($keyword);

        $this->view('appointments/index', [
            'title' => 'Appointment Management',
            'appointments' => $appointments,
            'keyword' => $keyword
        ]);
    }


    // Display appointment booking form
    public function create()
    {
        $this->authorize();

        $patients = $this->appointmentModel->getActivePatients();

        $doctors = $this->appointmentModel->getActiveDoctors();

        $this->view('appointments/form', [
            'title' => 'Book New Appointment',
            'appointment' => [],
            'patients' => $patients,
            'doctors' => $doctors,
            'error' => null,
            'csrfToken' => Auth::csrfToken()
        ]);
    }


    // Save new appointment
    public function store()
    {
        $this->authorize();

        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }

        $data = $this->getFormData();

        $error = $this->validateAppointment($data);

        if ($error !== null) {
            $this->showFormError(
                $error,
                $data,
                'Book New Appointment'
            );
            return;
        }

        try {

            $data['created_by'] = Auth::user()['id'];

            $id = $this->appointmentModel->createAppointment($data);

            header('Location: /appointments/show?id=' . $id);
            exit;

        } catch (Exception $e) {

            error_log($e->getMessage());

            $this->showFormError(
                $e->getMessage(),
                $data,
                'Book New Appointment'
            );
        }
    }


    // Display one appointment
    public function show()
    {
        $this->authorize();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            exit('Invalid appointment ID.');
        }

        $appointment = $this->appointmentModel
            ->getAppointmentById($id);

        if (!$appointment) {
            http_response_code(404);
            exit('Appointment not found.');
        }

        $this->view('appointments/show', [
            'title' => 'Appointment Details',
            'appointment' => $appointment,
            'csrfToken' => Auth::csrfToken()
        ]);
    }


    // Display edit form
    public function edit()
    {
        $this->authorize();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            exit('Invalid appointment ID.');
        }

        $appointment = $this->appointmentModel
            ->getAppointmentById($id);

        if (!$appointment) {
            http_response_code(404);
            exit('Appointment not found.');
        }

        if (!in_array(
            $appointment['status'],
            ['Scheduled', 'Confirmed'],
            true
        )) {
            exit('This appointment cannot be edited.');
        }

        $this->view('appointments/form', [
            'title' => 'Edit Appointment',
            'appointment' => $appointment,
            'patients' => $this->appointmentModel->getActivePatients(),
            'doctors' => $this->appointmentModel->getActiveDoctors(),
            'error' => null,
            'csrfToken' => Auth::csrfToken()
        ]);
    }


    // Update appointment
    public function update()
    {
        $this->authorize();

        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }

        $id = filter_var(
            $_POST['id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$id) {
            http_response_code(400);
            exit('Invalid appointment ID.');
        }

        $appointment = $this->appointmentModel
            ->getAppointmentById($id);

        if (!$appointment) {
            http_response_code(404);
            exit('Appointment not found.');
        }

        $data = $this->getFormData();

        $error = $this->validateAppointment($data);

        if ($error !== null) {
            $data['id'] = $id;

            $this->showFormError(
                $error,
                $data,
                'Edit Appointment'
            );
            return;
        }

        try {

            $this->appointmentModel->updateAppointment(
                $id,
                $data
            );

            header('Location: /appointments/show?id=' . $id);
            exit;

        } catch (Exception $e) {

            error_log($e->getMessage());

            $data['id'] = $id;

            $this->showFormError(
                $e->getMessage(),
                $data,
                'Edit Appointment'
            );
        }
    }


    // Update appointment status
    public function updateStatus()
    {
        $this->authorize();

        if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }

        $id = filter_var(
            $_POST['id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $status = $_POST['status'] ?? '';

        if (!$id) {
            http_response_code(400);
            exit('Invalid appointment ID.');
        }

        try {

            $this->appointmentModel->updateStatus(
                $id,
                $status
            );

            header('Location: /appointments/show?id=' . $id);
            exit;

        } catch (Exception $e) {

            error_log($e->getMessage());

            http_response_code(400);
            exit('Unable to update appointment status.');
        }
    }


    // Collect form data
    private function getFormData()
    {
        return [
            'patient_id' => trim($_POST['patient_id'] ?? ''),
            'doctor_id' => trim($_POST['doctor_id'] ?? ''),
            'appointment_date' => trim($_POST['appointment_date'] ?? ''),
            'appointment_time' => trim($_POST['appointment_time'] ?? ''),
            'duration_minutes' => trim($_POST['duration_minutes'] ?? '30'),
            'reason' => trim($_POST['reason'] ?? ''),
            'notes' => trim($_POST['notes'] ?? '')
        ];
    }


    // Validate appointment form
    private function validateAppointment($data)
    {
        if (
            $data['patient_id'] === '' ||
            $data['doctor_id'] === '' ||
            $data['appointment_date'] === '' ||
            $data['appointment_time'] === ''
        ) {
            return 'Please fill in all required fields.';
        }

        if (
            !ctype_digit((string) $data['patient_id']) ||
            !ctype_digit((string) $data['doctor_id'])
        ) {
            return 'Invalid patient or doctor selected.';
        }

        // Validate appointment date
        $date = DateTime::createFromFormat(
            '!Y-m-d',
            $data['appointment_date']
        );

        if (
            !$date ||
            $date->format('Y-m-d') !== $data['appointment_date']
        ) {
            return 'Please enter a valid appointment date.';
        }

        if ($data['appointment_date'] < date('Y-m-d')) {
            return 'Appointment date cannot be in the past.';
        }

        // Validate appointment time
        if (
            !preg_match(
                '/^(?:[01]\d|2[0-3]):[0-5]\d$/',
                $data['appointment_time']
            )
        ) {
            return 'Please enter a valid appointment time.';
        }

        // Validate duration
        if (
            !ctype_digit((string) $data['duration_minutes']) ||
            (int) $data['duration_minutes'] < 10 ||
            (int) $data['duration_minutes'] > 240
        ) {
            return 'Appointment duration must be between 10 and 240 minutes.';
        }

        // Check patient exists and is active
        $patients = $this->appointmentModel->getActivePatients();

        $patientIds = array_column($patients, 'id');

        if (!in_array(
            (int) $data['patient_id'],
            array_map('intval', $patientIds),
            true
        )) {
            return 'Please select a valid active patient.';
        }

        // Check doctor exists and is active
        $doctors = $this->appointmentModel->getActiveDoctors();

        $doctorIds = array_column($doctors, 'id');

        if (!in_array(
            (int) $data['doctor_id'],
            array_map('intval', $doctorIds),
            true
        )) {
            return 'Please select a valid active doctor.';
        }

        return null;
    }


    // Display form again when validation fails
    private function showFormError($error, $data, $title)
    {
        $this->view('appointments/form', [
            'title' => $title,
            'appointment' => $data,
            'patients' => $this->appointmentModel->getActivePatients(),
            'doctors' => $this->appointmentModel->getActiveDoctors(),
            'error' => $error,
            'csrfToken' => Auth::csrfToken()
        ]);
    }


// Display logged-in doctor's schedule

public function doctorSchedule()
{
    Auth::requireRole('doctor');

    $user = Auth::user();

    $doctorId = $this->appointmentModel
        ->getDoctorIdByUserId($user['id']);

    if (!$doctorId) {
        http_response_code(404);
        exit('Doctor profile not found. Please contact the administrator.');
    }

    $appointments = $this->appointmentModel
        ->getDoctorAppointments($doctorId);

    $this->view('appointments/doctor-schedule', [
        'title' => 'My Appointments',
        'appointments' => $appointments,
        'doctorId' => $doctorId,
        'user' => $user,
        'csrfToken' => Auth::csrfToken()
    ]);
}

// Mark a doctor's appointment as completed
public function completeAppointment()
{
    Auth::requireRole('doctor');

    // Verify CSRF token
    if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }

    // Validate appointment ID
    $appointmentId = filter_var(
        $_POST['appointment_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$appointmentId || $appointmentId <= 0) {
        http_response_code(400);
        exit('Invalid appointment ID.');
    }

    // Get logged-in doctor
    $user = Auth::user();

    $doctorId = $this->appointmentModel->getDoctorIdByUserId(
        $user['id']
    );

    if (!$doctorId) {
        http_response_code(403);
        exit('Doctor profile not found.');
    }

    // Update appointment status
    $updated = $this->appointmentModel
        ->markDoctorAppointmentCompleted($appointmentId, $doctorId);

    if (!$updated) {
        http_response_code(400);
        exit('Appointment could not be marked as completed. It may already be completed or unavailable.');
    }

    // Redirect after successful update
    header('Location: /doctor/schedule?status=completed');
    exit;
}
// Display patient appointment booking form
public function patientCreate()
{
    Auth::requireRole('patient');

    $doctorId = filter_input(
        INPUT_GET,
        'doctor_id',
        FILTER_VALIDATE_INT
    );

    if (!$doctorId) {
        header('Location: /patient/dashboard');
        exit;
    }

    $doctor = $this->appointmentModel
        ->getActiveDoctorById($doctorId);

    if (!$doctor) {
        http_response_code(404);
        exit('Doctor not found or is not currently active.');
    }

    $this->view('appointments/patient-form', [
        'title' => 'Book Appointment',
        'doctor' => $doctor,
        'error' => null,
        'csrfToken' => Auth::csrfToken()
    ]);
}


// Save patient appointment
public function patientStore()
{
    Auth::requireRole('patient');

    if (!Auth::verifyCsrf($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }

    $user = Auth::user();

    // Get patient ID from logged-in account
    $patientId = $this->appointmentModel
        ->getPatientIdByUserId($user['id']);

    if (!$patientId) {
        http_response_code(403);
        exit('Patient profile not found or inactive.');
    }

    $doctorId = filter_var(
        $_POST['doctor_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    $date = trim($_POST['appointment_date'] ?? '');
    $time = trim($_POST['appointment_time'] ?? '');
    $reason = trim($_POST['reason'] ?? '');

    // Verify selected doctor is still active
    $doctor = $doctorId
        ? $this->appointmentModel->getActiveDoctorById($doctorId)
        : false;

    $error = null;

    if (!$doctor) {
        $error = 'Please select a valid active doctor.';
    }

    // Validate appointment date
    if ($error === null) {
        $dateObject = DateTime::createFromFormat('!Y-m-d', $date);

        if (
            !$dateObject ||
            $dateObject->format('Y-m-d') !== $date
        ) {
            $error = 'Please select a valid appointment date.';
        } elseif ($date < date('Y-m-d')) {
            $error = 'Appointment date cannot be in the past.';
        }
    }

    // Validate appointment time
    if ($error === null) {
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) {
            $error = 'Please select a valid appointment time.';
        } elseif (
            $date === date('Y-m-d') &&
            strtotime($date . ' ' . $time) <= time()
        ) {
            $error = 'Please select a future appointment time.';
        }
    }

    if ($error === null && strlen($reason) > 5000) {
        $error = 'The reason must not exceed 5000 characters.';
    }

    if ($error !== null) {
        $this->view('appointments/patient-form', [
            'title' => 'Book Appointment',
            'doctor' => $doctor ?: [],
            'error' => $error,
            'csrfToken' => Auth::csrfToken(),
            'appointmentDate' => $date,
            'appointmentTime' => $time,
            'reason' => $reason
        ]);
        return;
    }

    try {
        $this->appointmentModel->createAppointment([
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'appointment_date' => $date,
            'appointment_time' => $time,
            'duration_minutes' => 30,
            'reason' => $reason,
            'notes' => null,
            'created_by' => $user['id']
        ]);

        header('Location: /patient/dashboard?booking=success');
        exit;

    } catch (Exception $e) {
        error_log($e->getMessage());

        $this->view('appointments/patient-form', [
            'title' => 'Book Appointment',
            'doctor' => $doctor,
            'error' => $e->getMessage(),
            'csrfToken' => Auth::csrfToken(),
            'appointmentDate' => $date,
            'appointmentTime' => $time,
            'reason' => $reason
        ]);
    }
}

}
