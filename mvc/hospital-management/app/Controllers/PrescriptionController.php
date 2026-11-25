
<?php

require_once __DIR__ . '/../Models/Prescription.php';

class PrescriptionController extends Controller
{
    private $prescription;

    public function __construct($db)
    {
        $this->prescription = new Prescription($db);
    }

    // Display all prescriptions
    public function index()
    {
        $prescriptions =
            $this->prescription->getAllPrescriptions();

        $this->view('prescriptions/index', [
            'prescriptions' => $prescriptions
        ]);
    }

    // Display create form
    public function create()
    {
        $appointments =
            $this->prescription->getAppointments();

        $this->view('prescriptions/create', [
            'appointments' => $appointments,
            'errors' => [],
            'old' => [],
            'items' => []
        ]);
    }

    // Save new prescription
    public function store()
    {
        $data = [
            'appointment_id' => $_POST['appointment_id'] ?? '',
            'notes' => trim($_POST['notes'] ?? '')
        ];

        $items = $this->getItemsFromPost();

        $errors = $this->validatePrescription($data, $items);

        if (!empty($errors)) {
            $this->view('prescriptions/create', [
                'appointments' => $this->prescription->getAppointments(),
                'errors' => $errors,
                'old' => $data,
                'items' => $items
            ]);

            return;
        }

        try {
            $id = $this->prescription->addPrescription(
                $data,
                $items
            );

            header("Location: /prescriptions/show?id=" . $id);
            exit;

        } catch (PDOException $e) {
            $errors[] = "Unable to save prescription. Check whether this appointment already has a prescription.";

            $this->view('prescriptions/create', [
                'appointments' => $this->prescription->getAppointments(),
                'errors' => $errors,
                'old' => $data,
                'items' => $items
            ]);
        }
    }

    // Display one prescription
    public function show()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        $prescription = $id
            ? $this->prescription->getPrescriptionById($id)
            : false;

        if (!$prescription) {
            http_response_code(404);
            echo "Prescription not found";
            return;
        }

        $items = $this->prescription->getPrescriptionItems($id);

        $this->view('prescriptions/show', [
            'prescription' => $prescription,
            'items' => $items
        ]);
    }

    // Display edit form
    public function edit()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        $prescription = $id
            ? $this->prescription->getPrescriptionById($id)
            : false;

        if (!$prescription) {
            http_response_code(404);
            echo "Prescription not found";
            return;
        }

        $items = $this->prescription->getPrescriptionItems($id);

        $this->view('prescriptions/edit', [
            'prescription' => $prescription,
            'appointments' => $this->prescription->getAppointments($id),
            'items' => $items,
            'errors' => []
        ]);
    }

    // Update prescription
    public function update()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        $prescription = $id
            ? $this->prescription->getPrescriptionById($id)
            : false;

        if (!$prescription) {
            http_response_code(404);
            echo "Prescription not found";
            return;
        }

        $data = [
            'appointment_id' => $_POST['appointment_id'] ?? '',
            'notes' => trim($_POST['notes'] ?? '')
        ];

        $items = $this->getItemsFromPost();

        $errors = $this->validatePrescription($data, $items);

        $availableAppointments = array_map(
            'intval',
            array_column(
                $this->prescription->getAppointments($id),
                'appointment_id'
            )
        );

        if (
            !in_array(
                (int) $data['appointment_id'],
                $availableAppointments,
                true
            )
        ) {
            $errors[] = "Please select a valid appointment.";
        }

        if (!empty($errors)) {
            $this->view('prescriptions/edit', [
                'prescription' => array_merge($prescription, $data),
                'appointments' => $this->prescription->getAppointments($id),
                'items' => $items,
                'errors' => $errors
            ]);

            return;
        }

        try {
            $this->prescription->updatePrescription(
                $id,
                $data,
                $items
            );

            header("Location: /prescriptions/show?id=" . $id);
            exit;

        } catch (PDOException $e) {
            $errors[] = "Unable to update prescription. Please check the selected appointment.";

            $this->view('prescriptions/edit', [
                'prescription' => array_merge($prescription, $data),
                'appointments' => $this->prescription->getAppointments($id),
                'items' => $items,
                'errors' => $errors
            ]);
        }
    }

    // Delete prescription
    public function delete()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id) {
            $this->prescription->deletePrescription($id);
        }

        header("Location: /prescriptions");
        exit;
    }

    // Search prescriptions
    public function search()
    {
        $keyword = trim($_GET['keyword'] ?? '');

        $prescriptions =
            $this->prescription->searchPrescriptions($keyword);

        $this->view('prescriptions/index', [
            'prescriptions' => $prescriptions,
            'keyword' => $keyword
        ]);
    }

    // Collect medicine rows from the form
    private function getItemsFromPost()
    {
        $items = [];

        $names = $_POST['medicine_name'] ?? [];

        if (!is_array($names)) {
            return [];
        }

        foreach ($names as $key => $name) {
            $items[] = [
                'medicine_name' => trim($name),
                'dosage' => trim($_POST['dosage'][$key] ?? ''),
                'frequency' => trim($_POST['frequency'][$key] ?? ''),
                'duration' => trim($_POST['duration'][$key] ?? ''),
                'instructions' => trim($_POST['instructions'][$key] ?? '')
            ];
        }

        return $items;
    }

    // Validate prescription form
    private function validatePrescription($data, $items)
    {
        $errors = [];

        if (
            !filter_var(
                $data['appointment_id'],
                FILTER_VALIDATE_INT
            )
        ) {
            $errors[] = "Please select a valid appointment.";
        } else {
            $validAppointments = array_column(
                $this->prescription->getAppointments(),
                'appointment_id'
            );

            // The appointment must be available for a new prescription.
            // The edit method also permits its existing appointment.
            if (!in_array(
                (int) $data['appointment_id'],
                array_map('intval', $validAppointments),
                true
            )) {
                $errors[] = "The selected appointment is not available for a new prescription.";
            }
        }

        if (empty($items)) {
            $errors[] = "Please add at least one medicine.";
        }

        foreach ($items as $index => $item) {
            if ($item['medicine_name'] === '') {
                $errors[] = "Medicine name is required for medicine row " . ($index + 1) . ".";
            }
        }

        return $errors;
    }
}
