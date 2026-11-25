
<?php

require_once __DIR__ . '/../Models/Appointment.php';

class AppointmentController extends Controller
{
    private $appointment;

    public function __construct($db)
    {
        $this->appointment = new Appointment($db);
    }

    // Display appointments
    public function index()
    {
        $appointments = $this->appointment->getAllAppointments();

        $this->view('appointments/index', [
            'appointments' => $appointments
        ]);
    }

    // Show registration form
    public function create()
    {
        $patients = $this->appointment->getPatients();
        $doctors = $this->appointment->getDoctors();

        $this->view('appointments/create', [
            'patients' => $patients,
            'doctors' => $doctors,
            'errors' => []
        ]);
    }

    // Store appointment
    public function store()
    {
        $data = [
            'patient_id' => $_POST['patient_id'] ?? '',
            'doctor_id' => $_POST['doctor_id'] ?? '',
            'appointment_date' => $_POST['appointment_date'] ?? '',
            'appointment_time' => $_POST['appointment_time'] ?? '',
            'reason' => trim($_POST['reason'] ?? ''),
            'status' => $_POST['status'] ?? 'Scheduled'
        ];

        $errors = $this->validateAppointment($data);

        if (!empty($errors)) {
            $patients = $this->appointment->getPatients();
            $doctors = $this->appointment->getDoctors();

            $this->view('appointments/create', [
                'patients' => $patients,
                'doctors' => $doctors,
                'errors' => $errors,
                'old' => $data
            ]);

            return;
        }

        $this->appointment->addAppointment($data);

        header("Location: /appointments");
        exit;
    }

    // Show one appointment
    public function show()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        $appointment =
            $id ? $this->appointment->getAppointmentById($id) : false;

        if (!$appointment) {
            http_response_code(404);
            echo "Appointment not found";
            return;
        }

        $this->view('appointments/show', [
            'appointment' => $appointment
        ]);
    }

    // Show edit form
    public function edit()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        $appointment =
            $id ? $this->appointment->getAppointmentById($id) : false;

        if (!$appointment) {
            http_response_code(404);
            echo "Appointment not found";
            return;
        }

        $this->view('appointments/edit', [
            'appointment' => $appointment,
            'patients' => $this->appointment->getPatients(),
            'doctors' => $this->appointment->getDoctors(),
            'errors' => []
        ]);
    }

    // Update appointment
    public function update()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        $data = [
            'patient_id' => $_POST['patient_id'] ?? '',
            'doctor_id' => $_POST['doctor_id'] ?? '',
            'appointment_date' => $_POST['appointment_date'] ?? '',
            'appointment_time' => $_POST['appointment_time'] ?? '',
            'reason' => trim($_POST['reason'] ?? ''),
            'status' => $_POST['status'] ?? 'Scheduled'
        ];

        $appointment =
            $id ? $this->appointment->getAppointmentById($id) : false;

        if (!$appointment) {
            http_response_code(404);
            echo "Appointment not found";
            return;
        }

        $errors = $this->validateAppointment($data);

        if (!empty($errors)) {
            $this->view('appointments/edit', [
                'appointment' => array_merge($appointment, $data),
                'patients' => $this->appointment->getPatients(),
                'doctors' => $this->appointment->getDoctors(),
                'errors' => $errors
            ]);

            return;
        }

        $this->appointment->updateAppointment($id, $data);

        header("Location: /appointments");
        exit;
    }

    // Delete appointment
    public function delete()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id) {
            $this->appointment->deleteAppointment($id);
        }

        header("Location: /appointments");
        exit;
    }

    // Search appointments
    public function search()
    {
        $keyword = trim($_GET['keyword'] ?? '');

        $appointments =
            $this->appointment->searchAppointments($keyword);

        $this->view('appointments/index', [
            'appointments' => $appointments,
            'keyword' => $keyword
        ]);
    }

    // Validate form data
    private function validateAppointment($data)
    {
        $errors = [];

        if (
            !filter_var($data['patient_id'], FILTER_VALIDATE_INT) ||
            !in_array($data['patient_id'], array_column(
                $this->appointment->getPatients(), 'patient_id'
            ))
        ) {
            $errors[] = "Please select a valid patient.";
        }

        if (
            !filter_var($data['doctor_id'], FILTER_VALIDATE_INT) ||
            !in_array($data['doctor_id'], array_column(
                $this->appointment->getDoctors(), 'doctor_id'
            ))
        ) {
            $errors[] = "Please select a valid doctor.";
        }

        $date = DateTime::createFromFormat(
            '!Y-m-d',
            $data['appointment_date']
        );

        if (
            !$date ||
            $date->format('Y-m-d') !== $data['appointment_date']
        ) {
            $errors[] = "Please enter a valid appointment date.";
        }

        if (
            !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/',
                $data['appointment_time'])
        ) {
            $errors[] = "Please enter a valid appointment time.";
        }

        $validStatuses = [
            'Scheduled',
            'Confirmed',
            'Completed',
            'Cancelled'
        ];

        if (!in_array($data['status'], $validStatuses, true)) {
            $errors[] = "Please select a valid status.";
        }

        return $errors;
    }
}
