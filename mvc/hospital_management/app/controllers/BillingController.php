
<?php

class BillingController extends Controller
{
    private $billing;

    public function __construct()
    {
        $this->billing = new Billing();
    }

    // Check user access
    private function authorize()
    {
        Auth::requireRole(['admin', 'receptionist']);
    }


    // 1. Display all bills
    public function index()
    {
        $this->authorize();

        $keyword = trim($_GET['search'] ?? '');

        $bills = $this->billing->getAllBills($keyword);

        $this->view('billing/index', [
            'title' => 'Billing Management',
            'bills' => $bills,
            'keyword' => $keyword
        ]);
    }


    // 2. Display create bill form
    public function create()
    {
        $this->authorize();

        $patients = $this->billing->getActivePatients();

        $this->view('billing/create', [
            'title' => 'Create New Bill',
            'patients' => $patients,
            'csrfToken' => Auth::csrfToken()
        ]);
    }


    // 3. Store a new bill
    public function store()
    {
        $this->authorize();

        if (
            !isset($_POST['csrf_token']) ||
            !Auth::verifyCsrf($_POST['csrf_token'])
        ) {
            http_response_code(403);
            exit('Invalid security token. Please refresh the page and try again.');
        }

        $data = [
            'patient_id' => (int) ($_POST['patient_id'] ?? 0),
            'appointment_id' => $_POST['appointment_id'] ?? null,
            'bill_date' => trim($_POST['bill_date'] ?? ''),
            'discount' => $_POST['discount'] ?? 0,
            'tax' => $_POST['tax'] ?? 0,
            'notes' => trim($_POST['notes'] ?? ''),
            'items' => $_POST['items'] ?? [],
            'created_by' => Auth::user()['id']
        ];

        $error = $this->validateBill($data);

        if ($error !== null) {
            $this->showError($error);
            return;
        }

        try {
            $billId = $this->billing->createBill($data);

            header('Location: /billing/show?id=' . $billId);
            exit;

        } catch (Throwable $e) {
            // Detailed error output is useful during local development.
            $this->showError('Unable to create bill: ' . $e->getMessage());
        }
    }


    // 4. Display bill details
    public function show()
    {
        $this->authorize();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            $this->showError('Invalid bill ID.');
            return;
        }

        $bill = $this->billing->getBillById($id);

        if (!$bill) {
            $this->showError('Bill not found.');
            return;
        }

        $items = $this->billing->getBillItems($id);
        $payments = $this->billing->getPayments($id);

        $this->view('billing/show', [
            'title' => 'Bill Details',
            'bill' => $bill,
            'items' => $items,
            'payments' => $payments,
            'csrfToken' => Auth::csrfToken()
        ]);
    }


    // 5. Record a payment
    public function storePayment()
    {
        $this->authorize();

        if (
            !isset($_POST['csrf_token']) ||
            !Auth::verifyCsrf($_POST['csrf_token'])
        ) {
            http_response_code(403);
            exit('Invalid security token. Please refresh the page and try again.');
        }

        $data = [
            'bill_id' => (int) ($_POST['bill_id'] ?? 0),
            'amount' => $_POST['amount'] ?? '',
            'payment_method' => trim($_POST['payment_method'] ?? ''),
            'payment_date' => trim($_POST['payment_date'] ?? ''),
            'transaction_reference' => trim(
                $_POST['transaction_reference'] ?? ''
            ),
            'notes' => trim($_POST['notes'] ?? ''),
            'received_by' => Auth::user()['id']
        ];

        $error = $this->validatePayment($data);

        if ($error !== null) {
            $this->showError($error);
            return;
        }

        try {
            $this->billing->recordPayment($data);

            header('Location: /billing/show?id=' . $data['bill_id']);
            exit;

        } catch (Throwable $e) {
            $this->showError('Unable to record payment: ' . $e->getMessage());
        }
    }


    // 6. Validate bill details
    private function validateBill($data)
    {
        if ($data['patient_id'] < 1) {
            return 'Please select a patient.';
        }

        if (empty($data['bill_date'])) {
            return 'Please select a bill date.';
        }

        $date = DateTime::createFromFormat('Y-m-d', $data['bill_date']);

        if (!$date || $date->format('Y-m-d') !== $data['bill_date']) {
            return 'Please enter a valid bill date.';
        }

        if (!is_numeric($data['discount']) || !is_numeric($data['tax'])) {
            return 'Discount and tax must be valid numbers.';
        }

        if ((float) $data['discount'] < 0 || (float) $data['tax'] < 0) {
            return 'Discount and tax cannot be negative.';
        }

        if (!is_array($data['items']) || count($data['items']) === 0) {
            return 'Please add at least one bill item.';
        }

        foreach ($data['items'] as $item) {
            if (
                !isset($item['item_name']) ||
                trim($item['item_name']) === ''
            ) {
                return 'Every bill item must have a name.';
            }

            if (
                !isset($item['quantity']) ||
                filter_var($item['quantity'], FILTER_VALIDATE_INT) === false ||
                (int) $item['quantity'] < 1
            ) {
                return 'Each item must have a valid quantity.';
            }

            if (
                !isset($item['unit_price']) ||
                !is_numeric($item['unit_price']) ||
                (float) $item['unit_price'] < 0
            ) {
                return 'Each item must have a valid price.';
            }
        }

        return null;
    }


    // 7. Validate payment details
    private function validatePayment($data)
    {
        if ($data['bill_id'] < 1) {
            return 'Invalid bill.';
        }

        if (!is_numeric($data['amount']) || (float) $data['amount'] <= 0) {
            return 'Enter a valid payment amount.';
        }

        $allowedMethods = [
            'Cash',
            'Card',
            'UPI',
            'Bank Transfer'
        ];

        if (!in_array($data['payment_method'], $allowedMethods, true)) {
            return 'Please select a valid payment method.';
        }

        if (empty($data['payment_date'])) {
            return 'Please select a payment date and time.';
        }

        $date = DateTime::createFromFormat(
            'Y-m-d\TH:i',
            $data['payment_date']
        );

        if (
            !$date ||
            $date->format('Y-m-d\TH:i') !== $data['payment_date']
        ) {
            return 'Please enter a valid payment date and time.';
        }

        return null;
    }


    // 8. Display an error message
    private function showError($message)
    {
        http_response_code(400);

        echo '<h2>Billing Error</h2>';
        echo '<p>' . htmlspecialchars(
            $message,
            ENT_QUOTES,
            'UTF-8'
        ) . '</p>';

        echo '<p><a href="/billing">Back to Billing</a></p>';
    }
}
