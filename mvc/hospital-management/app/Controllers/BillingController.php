
<?php

require_once __DIR__ . '/../Models/Bill.php';

class BillingController extends Controller
{
    private $bill;

    public function __construct($db)
    {
        $this->bill = new Bill($db);
    }

    // Display all bills
    public function index()
    {
        $bills = $this->bill->getAllBills();

        $this->view('billing/index', [
            'bills' => $bills
        ]);
    }

    // Display create form
    public function create()
    {
        $this->view('billing/create', [
            'appointments' => $this->bill->getAppointments(),
            'errors' => [],
            'old' => [],
            'items' => []
        ]);
    }

    // Save new bill
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        $data = [
            'appointment_id' => $_POST['appointment_id'] ?? ''
        ];

        $items = $this->getItemsFromPost();
        $errors = $this->validateBill($data, $items);

        if (!empty($errors)) {
            $this->view('billing/create', [
                'appointments' => $this->bill->getAppointments(),
                'errors' => $errors,
                'old' => $data,
                'items' => $items
            ]);
            return;
        }

        try {
            $id = $this->bill->addBill($data, $items);

            header("Location: /bills/show?id=" . $id);
            exit;

        } catch (PDOException $e) {
            $this->view('billing/create', [
                'appointments' => $this->bill->getAppointments(),
                'errors' => [
                    'Unable to create bill. This appointment may already have a bill.'
                ],
                'old' => $data,
                'items' => $items
            ]);
        }
    }

    // Display one bill
    public function show()
    {
        $id = (int)($_GET['id'] ?? 0);

        $bill = $this->bill->getBillById($id);

        if (!$bill) {
            http_response_code(404);
            exit('Bill Not Found');
        }

        $this->view('billing/show', [
            'bill' => $bill,
            'items' => $this->bill->getBillItems($id),
            'payments' => $this->bill->getPayments($id),
            'balance' => max(
                0,
                (float)$bill['total_amount'] - (float)$bill['paid_amount']
            ),
            'errors' => [],
            'success' => ''
        ]);
    }

    // Display edit form
    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);

        $bill = $this->bill->getBillById($id);

        if (!$bill) {
            http_response_code(404);
            exit('Bill Not Found');
        }

        $this->view('billing/edit', [
            'bill' => $bill,
            'appointments' => $this->bill->getAppointments($id),
            'items' => $this->bill->getBillItems($id),
            'errors' => []
        ]);
    }

    // Update bill
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        $id = (int)($_GET['id'] ?? 0);
        $bill = $this->bill->getBillById($id);

        if (!$bill) {
            http_response_code(404);
            exit('Bill Not Found');
        }

        $data = [
            'appointment_id' => $_POST['appointment_id'] ?? ''
        ];

        $items = $this->getItemsFromPost();
        $errors = $this->validateBill($data, $items);

        $availableAppointments = array_map(
            'intval',
            array_column(
                $this->bill->getAppointments($id),
                'appointment_id'
            )
        );

        if (!in_array(
            (int)$data['appointment_id'],
            $availableAppointments,
            true
        )) {
            $errors[] = 'Please select a valid appointment.';
        }

        if (!empty($errors)) {
            $this->view('billing/edit', [
                'bill' => $bill,
                'appointments' => $this->bill->getAppointments($id),
                'items' => $items,
                'errors' => $errors
            ]);
            return;
        }

        try {
            $this->bill->updateBill($id, $data, $items);

            header("Location: /bills/show?id=" . $id);
            exit;

        } catch (PDOException $e) {
            $this->view('billing/edit', [
                'bill' => $bill,
                'appointments' => $this->bill->getAppointments($id),
                'items' => $items,
                'errors' => ['Unable to update bill.']
            ]);
        }
    }

    // Record a payment
    public function payment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        $id = (int)($_GET['id'] ?? 0);
        $amount = $_POST['amount'] ?? '';
        $method = $_POST['payment_method'] ?? '';

        $bill = $this->bill->getBillById($id);

        if (!$bill) {
            http_response_code(404);
            exit('Bill Not Found');
        }

        $validMethods = [
            'Cash',
            'Card',
            'UPI',
            'Bank Transfer'
        ];

        if (
            !is_numeric($amount) ||
            !is_finite((float)$amount) ||
            (float)$amount <= 0
        ) {
            $errors = ['Enter a valid payment amount.'];

        } elseif (!in_array($method, $validMethods, true)) {
            $errors = ['Select a valid payment method.'];

        } else {
            try {
                $this->bill->addPayment(
                    $id,
                    round((float)$amount, 2),
                    $method
                );

                header("Location: /bills/show?id=" . $id);
                exit;

            } catch (Throwable $e) {
                $errors = [$e->getMessage()];
            }
        }

        $this->view('billing/show', [
            'bill' => $this->bill->getBillById($id),
            'items' => $this->bill->getBillItems($id),
            'payments' => $this->bill->getPayments($id),
            'balance' => max(
                0,
                (float)$bill['total_amount'] - (float)$bill['paid_amount']
            ),
            'errors' => $errors,
            'success' => ''
        ]);
    }

    // Delete bill
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        $id = (int)($_GET['id'] ?? 0);

        if ($id > 0) {
            $this->bill->deleteBill($id);
        }

        header("Location: /bills");
        exit;
    }

    // Search bills
    public function search()
    {
        $keyword = trim($_GET['keyword'] ?? '');

        $bills = $this->bill->searchBills($keyword);

        $this->view('billing/index', [
            'bills' => $bills,
            'keyword' => $keyword
        ]);
    }

    // Collect bill items from form
    private function getItemsFromPost()
    {
        $items = [];

        $descriptions = $_POST['description'] ?? [];

        if (!is_array($descriptions)) {
            return [];
        }

        foreach ($descriptions as $key => $description) {
            $items[] = [
                'description' => trim($description),
                'quantity' => $_POST['quantity'][$key] ?? '',
                'unit_price' => $_POST['unit_price'][$key] ?? ''
            ];
        }

        return $items;
    }

    // Validate bill data
    private function validateBill($data, $items)
    {
        $errors = [];

        if (!filter_var(
            $data['appointment_id'],
            FILTER_VALIDATE_INT
        )) {
            $errors[] = 'Please select a valid appointment.';
        }

        if (empty($items)) {
            $errors[] = 'Add at least one bill item.';
        }

        foreach ($items as $index => $item) {
            if ($item['description'] === '') {
                $errors[] = 'Description is required in row ' . ($index + 1);
            }

            if (
                filter_var($item['quantity'], FILTER_VALIDATE_INT) === false ||
                (int)$item['quantity'] <= 0
            ) {
                $errors[] = 'Enter a valid quantity in row ' . ($index + 1);
            }

            if (
                !is_numeric($item['unit_price']) ||
                !is_finite((float)$item['unit_price']) ||
                (float)$item['unit_price'] < 0
            ) {
                $errors[] = 'Enter a valid unit price in row ' . ($index + 1);
            }
        }

        return $errors;
    }
}
