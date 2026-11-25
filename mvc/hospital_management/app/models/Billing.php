
<?php

class Billing extends Model
{
    // 1. Get all bills with patient information
    public function getAllBills($keyword = '')
    {
        $sql = "
            SELECT
                b.*,
                p.patient_number,
                p.first_name,
                p.last_name,
                p.phone
            FROM bills b
            INNER JOIN patients p
                ON b.patient_id = p.id
        ";

        if (!empty($keyword)) {
            $sql .= "
                WHERE
                    b.bill_number LIKE :bill_number
                    OR p.patient_number LIKE :patient_number
                    OR p.first_name LIKE :first_name
                    OR p.last_name LIKE :last_name
                    OR p.phone LIKE :phone
            ";
        }

        $sql .= " ORDER BY b.id DESC";

        $stmt = $this->db->prepare($sql);

        if (!empty($keyword)) {
            $search = '%' . $keyword . '%';

            $stmt->execute([
                ':bill_number' => $search,
                ':patient_number' => $search,
                ':first_name' => $search,
                ':last_name' => $search,
                ':phone' => $search
            ]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // 2. Get one bill by ID
    public function getBillById($id)
    {
        $sql = "
            SELECT
                b.*,
                p.patient_number,
                p.first_name,
                p.last_name,
                p.gender,
                p.phone,
                p.email,
                p.address
            FROM bills b
            INNER JOIN patients p
                ON b.patient_id = p.id
            WHERE b.id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // 3. Get all items belonging to a bill
    public function getBillItems($billId)
    {
        $sql = "
            SELECT *
            FROM bill_items
            WHERE bill_id = :bill_id
            ORDER BY id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':bill_id' => $billId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // 4. Get payment history for a bill
    public function getPayments($billId)
    {
        $sql = "
            SELECT
                p.*,
                u.username AS received_by_name
            FROM payments p
            LEFT JOIN users u
                ON p.received_by = u.id
            WHERE p.bill_id = :bill_id
            ORDER BY p.id DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':bill_id' => $billId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // 5. Get active patients
    public function getActivePatients()
    {
        $sql = "
            SELECT
                id,
                patient_number,
                first_name,
                last_name,
                phone
            FROM patients
            WHERE status = 1
            ORDER BY first_name ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // 6. Get appointments for a selected patient
    public function getAppointmentsByPatient($patientId)
    {
        $sql = "
            SELECT
                a.id,
                a.appointment_number,
                a.appointment_date,
                a.appointment_time,
                a.status,
                d.first_name AS doctor_first_name,
                d.last_name AS doctor_last_name
            FROM appointments a
            INNER JOIN doctors d
                ON a.doctor_id = d.id
            WHERE
                a.patient_id = :patient_id
                AND a.status IN ('Scheduled', 'Confirmed', 'Completed')
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':patient_id' => $patientId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // 7. Create a bill with multiple bill items
    public function createBill($data)
    {
        $this->db->beginTransaction();

        try {
            $subtotal = 0;

            foreach ($data['items'] as $item) {
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];

                if ($quantity < 1 || $unitPrice < 0) {
                    throw new Exception('Invalid bill item quantity or price.');
                }

                $subtotal += $quantity * $unitPrice;
            }

            $discount = (float) $data['discount'];
            $tax = (float) $data['tax'];

            if (
                $discount < 0 ||
                $tax < 0 ||
                $discount > $subtotal
            ) {
                throw new Exception('Invalid discount or tax amount.');
            }

            $totalAmount = round($subtotal - $discount + $tax, 2);

            if ($totalAmount <= 0) {
                throw new Exception('The bill total must be greater than zero.');
            }

            // Insert the main bill first
            $temporaryNumber = 'TEMP-' . bin2hex(random_bytes(12));

            $sql = "
                INSERT INTO bills (
                    bill_number,
                    patient_id,
                    appointment_id,
                    bill_date,
                    subtotal,
                    discount,
                    tax,
                    total_amount,
                    paid_amount,
                    balance_amount,
                    payment_status,
                    notes,
                    created_by
                ) VALUES (
                    :bill_number,
                    :patient_id,
                    :appointment_id,
                    :bill_date,
                    :subtotal,
                    :discount,
                    :tax,
                    :total_amount,
                    0.00,
                    :balance_amount,
                    'Pending',
                    :notes,
                    :created_by
                )
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':bill_number' => $temporaryNumber,
                ':patient_id' => $data['patient_id'],
                ':appointment_id' => $data['appointment_id'] ?: null,
                ':bill_date' => $data['bill_date'],
                ':subtotal' => $subtotal,
                ':discount' => $discount,
                ':tax' => $tax,
                ':total_amount' => $totalAmount,
                ':balance_amount' => $totalAmount,
                ':notes' => $data['notes'] ?? null,
                ':created_by' => $data['created_by']
            ]);

            $billId = (int) $this->db->lastInsertId();

            // Generate the final bill number
            $billNumber = 'BILL' . date('Y') . str_pad(
                $billId,
                6,
                '0',
                STR_PAD_LEFT
            );

            $updateBill = $this->db->prepare("
                UPDATE bills
                SET bill_number = :bill_number
                WHERE id = :id
            ");

            $updateBill->execute([
                ':bill_number' => $billNumber,
                ':id' => $billId
            ]);

            // Insert all bill items
            $itemSql = "
                INSERT INTO bill_items (
                    bill_id,
                    item_name,
                    description,
                    quantity,
                    unit_price,
                    total_price
                ) VALUES (
                    :bill_id,
                    :item_name,
                    :description,
                    :quantity,
                    :unit_price,
                    :total_price
                )
            ";

            $itemStmt = $this->db->prepare($itemSql);

            foreach ($data['items'] as $item) {
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];

                $itemStmt->execute([
                    ':bill_id' => $billId,
                    ':item_name' => $item['item_name'],
                    ':description' => $item['description'] ?? null,
                    ':quantity' => $quantity,
                    ':unit_price' => $unitPrice,
                    ':total_price' => round($quantity * $unitPrice, 2)
                ]);
            }

            $this->db->commit();

            return $billId;

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }


    // 8. Record a payment
    public function recordPayment($data)
    {
        $this->db->beginTransaction();

        try {
            // Lock the bill while recording payment
            $stmt = $this->db->prepare("
                SELECT *
                FROM bills
                WHERE id = :id
                FOR UPDATE
            ");

            $stmt->execute([':id' => $data['bill_id']]);

            $bill = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$bill) {
                throw new Exception('Bill not found.');
            }

            $amount = round((float) $data['amount'], 2);
            $balance = round((float) $bill['balance_amount'], 2);

            if ($amount <= 0) {
                throw new Exception('Payment amount must be greater than zero.');
            }

            if ($amount > $balance) {
                throw new Exception('Payment cannot exceed the outstanding balance.');
            }

            $paidAmount = round(
                (float) $bill['paid_amount'] + $amount,
                2
            );

            $newBalance = round(
                (float) $bill['total_amount'] - $paidAmount,
                2
            );

            $paymentStatus = 'Partially Paid';

            if ($newBalance <= 0) {
                $newBalance = 0;
                $paymentStatus = 'Paid';
            }

            // Insert payment with a temporary unique number
            $temporaryNumber = 'TEMP-' . bin2hex(random_bytes(12));

            $paymentSql = "
                INSERT INTO payments (
                    bill_id,
                    payment_number,
                    amount,
                    payment_method,
                    payment_date,
                    transaction_reference,
                    notes,
                    received_by
                ) VALUES (
                    :bill_id,
                    :payment_number,
                    :amount,
                    :payment_method,
                    :payment_date,
                    :transaction_reference,
                    :notes,
                    :received_by
                )
            ";

            $paymentStmt = $this->db->prepare($paymentSql);

            $paymentStmt->execute([
                ':bill_id' => $data['bill_id'],
                ':payment_number' => $temporaryNumber,
                ':amount' => $amount,
                ':payment_method' => $data['payment_method'],
                ':payment_date' => $data['payment_date'],
                ':transaction_reference' => $data['transaction_reference'] ?? null,
                ':notes' => $data['notes'] ?? null,
                ':received_by' => $data['received_by']
            ]);

            $paymentId = (int) $this->db->lastInsertId();

            // Generate final payment number
            $paymentNumber = 'PAY' . date('Y') . str_pad(
                $paymentId,
                6,
                '0',
                STR_PAD_LEFT
            );

            $numberStmt = $this->db->prepare("
                UPDATE payments
                SET payment_number = :payment_number
                WHERE id = :id
            ");

            $numberStmt->execute([
                ':payment_number' => $paymentNumber,
                ':id' => $paymentId
            ]);

            // Update bill payment totals
            $updateStmt = $this->db->prepare("
                UPDATE bills
                SET
                    paid_amount = :paid_amount,
                    balance_amount = :balance_amount,
                    payment_status = :payment_status
                WHERE id = :id
            ");

            $updateStmt->execute([
                ':paid_amount' => $paidAmount,
                ':balance_amount' => $newBalance,
                ':payment_status' => $paymentStatus,
                ':id' => $data['bill_id']
            ]);

            $this->db->commit();

            return $paymentId;

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }
}
