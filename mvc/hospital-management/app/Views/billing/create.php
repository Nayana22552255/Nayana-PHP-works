
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Bill</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navigation.php'; ?>

<div class="container">

    <h2>Create New Bill</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="/bills/store" method="POST">

        <label>Select Appointment</label>

        <select name="appointment_id" required>
            <option value="">Select Appointment</option>

            <?php foreach ($appointments as $appointment): ?>
                <option
                    value="<?= $appointment['appointment_id'] ?>"
                    <?= (($old['appointment_id'] ?? '') == $appointment['appointment_id']) ? 'selected' : '' ?>>

                    <?= htmlspecialchars(
                        $appointment['patient_name'] .
                        ' - Dr. ' . $appointment['doctor_name'] .
                        ' - ' . $appointment['appointment_date']
                    ) ?>

                </option>
            <?php endforeach; ?>
        </select>

        <h3>Bill Items</h3>

        <div id="itemContainer">

            <?php
            $formItems = !empty($items) ? $items : [[
                'description' => '',
                'quantity' => 1,
                'unit_price' => ''
            ]];
            ?>

            <?php foreach ($formItems as $item): ?>

                <div class="bill-row">

                    <label>Description</label>
                    <input
                        type="text"
                        name="description[]"
                        value="<?= htmlspecialchars($item['description'] ?? '') ?>"
                        required>

                    <label>Quantity</label>
                    <input
                        type="number"
                        name="quantity[]"
                        min="1"
                        value="<?= htmlspecialchars((string)($item['quantity'] ?? 1)) ?>"
                        required>

                    <label>Unit Price (₹)</label>
                    <input
                        type="number"
                        name="unit_price[]"
                        min="0"
                        step="0.01"
                        value="<?= htmlspecialchars((string)($item['unit_price'] ?? '')) ?>"
                        required>

                    <button type="button" class="removeItem">
                        Remove Item
                    </button>

                    <hr>

                </div>

            <?php endforeach; ?>

        </div>

        <button type="button" id="addItem">
            + Add Bill Item
        </button>

        <br><br>

        <h3>Total: ₹<span id="totalAmount">0.00</span></h3>

        <button type="submit">Create Bill</button>

    </form>

    <br>

    <a href="/bills">Back to Bills</a>

</div>

<script>
const container = document.getElementById('itemContainer');
const totalDisplay = document.getElementById('totalAmount');

function calculateTotal() {
    let total = 0;

    container.querySelectorAll('.bill-row').forEach(row => {
        const quantity = parseFloat(
            row.querySelector('[name="quantity[]"]').value
        ) || 0;

        const price = parseFloat(
            row.querySelector('[name="unit_price[]"]').value
        ) || 0;

        total += quantity * price;
    });

    totalDisplay.textContent = total.toFixed(2);
}

document.getElementById('addItem').addEventListener('click', function () {

    const row = document.createElement('div');

    row.className = 'bill-row';

    row.innerHTML = `
        <label>Description</label>
        <input type="text" name="description[]" required>

        <label>Quantity</label>
        <input type="number" name="quantity[]" min="1" value="1" required>

        <label>Unit Price (₹)</label>
        <input type="number" name="unit_price[]" min="0" step="0.01" required>

        <button type="button" class="removeItem">
            Remove Item
        </button>

        <hr>
    `;

    container.appendChild(row);
    calculateTotal();
});

container.addEventListener('input', calculateTotal);

container.addEventListener('click', function (event) {
    if (event.target.classList.contains('removeItem')) {

        const rows = container.querySelectorAll('.bill-row');

        if (rows.length > 1) {
            event.target.closest('.bill-row').remove();
            calculateTotal();
        } else {
            alert('At least one bill item is required.');
        }
    }
});

calculateTotal();
</script>

</body>
</html>
