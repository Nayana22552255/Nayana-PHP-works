
<?php
function billingCreateEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= billingCreateEscape($title) ?></title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .container {
            max-width: 850px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 8px;
        }

        h2, h3 {
            color: #263b5e;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 7px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
            gap: 10px;
            align-items: end;
            margin-bottom: 12px;
        }

        .item label {
            font-size: 13px;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }

        .primary {
            background: #263b5e;
            color: white;
        }

        .danger {
            background: #dc2626;
            color: white;
        }

        .total {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
        }

        @media(max-width: 650px) {
            .item {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Create New Bill</h2>

    <form method="POST" action="/billing/store">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= billingCreateEscape($csrfToken) ?>"
        >

        <label>Select Patient *</label>

        <select name="patient_id" required>

            <option value="">-- Select Patient --</option>

            <?php foreach ($patients as $patient): ?>

                <option value="<?= (int) $patient['id'] ?>">

                    <?= billingCreateEscape(
                        $patient['patient_number'] . ' - ' .
                        $patient['first_name'] . ' ' .
                        $patient['last_name']
                    ) ?>

                </option>

            <?php endforeach; ?>

        </select>

        <label>Appointment ID (Optional)</label>

        <input
            type="number"
            name="appointment_id"
            min="1"
            placeholder="Enter appointment ID if applicable"
        >

        <label>Bill Date *</label>

        <input
            type="date"
            name="bill_date"
            value="<?= date('Y-m-d') ?>"
            required
        >

        <h3>Bill Items</h3>

        <div id="items-container">

            <div class="item">

                <div>
                    <label>Item Name</label>
                    <input
                        type="text"
                        name="items[0][item_name]"
                        placeholder="Consultation Fee"
                        required
                    >
                </div>

                <div>
                    <label>Quantity</label>
                    <input
                        type="number"
                        name="items[0][quantity]"
                        value="1"
                        min="1"
                        required
                    >
                </div>

                <div>
                    <label>Unit Price (₹)</label>
                    <input
                        type="number"
                        name="items[0][unit_price]"
                        value="0"
                        min="0"
                        step="0.01"
                        required
                    >
                </div>

                <div>
                    <label>Description</label>
                    <input
                        type="text"
                        name="items[0][description]"
                        placeholder="Optional"
                    >
                </div>

                <button
                    type="button"
                    class="btn danger remove-item"
                    disabled
                >
                    Remove
                </button>

            </div>

        </div>

        <button
            type="button"
            class="btn primary"
            id="add-item"
        >
            + Add Another Item
        </button>

        <label>Discount (₹)</label>

        <input
            type="number"
            name="discount"
            value="0"
            min="0"
            step="0.01"
            required
        >

        <label>Tax (₹)</label>

        <input
            type="number"
            name="tax"
            value="0"
            min="0"
            step="0.01"
            required
        >

        <label>Notes</label>

        <textarea
            name="notes"
            rows="3"
            placeholder="Additional bill information"
        ></textarea>

        <p class="total">
            Subtotal: ₹<span id="subtotal">0.00</span>
        </p>

        <p class="total">
            Estimated Total: ₹<span id="grand-total">0.00</span>
        </p>

        <button type="submit" class="btn primary">
            Create Bill
        </button>

        <a href="/billing" class="btn primary">
            Cancel
        </a>

    </form>

</div>

<script>
    const container = document.getElementById('items-container');
    const addButton = document.getElementById('add-item');

    let itemIndex = 1;

    function updateTotals() {
        let subtotal = 0;

        container.querySelectorAll('.item').forEach(function(item) {
            const quantity = parseFloat(
                item.querySelector('[name$="[quantity]"]').value
            ) || 0;

            const price = parseFloat(
                item.querySelector('[name$="[unit_price]"]').value
            ) || 0;

            subtotal += quantity * price;
        });

        const discount = parseFloat(
            document.querySelector('[name="discount"]').value
        ) || 0;

        const tax = parseFloat(
            document.querySelector('[name="tax"]').value
        ) || 0;

        document.getElementById('subtotal').textContent =
            subtotal.toFixed(2);

        document.getElementById('grand-total').textContent =
            Math.max(0, subtotal - discount + tax).toFixed(2);
    }

    function updateRemoveButtons() {
        const buttons = container.querySelectorAll('.remove-item');

        buttons.forEach(function(button) {
            button.disabled = buttons.length === 1;
        });
    }

    addButton.addEventListener('click', function() {
        const item = document.createElement('div');

        item.className = 'item';

        item.innerHTML = `
            <div>
                <label>Item Name</label>
                <input
                    type="text"
                    name="items[${itemIndex}][item_name]"
                    required
                >
            </div>

            <div>
                <label>Quantity</label>
                <input
                    type="number"
                    name="items[${itemIndex}][quantity]"
                    value="1"
                    min="1"
                    required
                >
            </div>

            <div>
                <label>Unit Price (₹)</label>
                <input
                    type="number"
                    name="items[${itemIndex}][unit_price]"
                    value="0"
                    min="0"
                    step="0.01"
                    required
                >
            </div>

            <div>
                <label>Description</label>
                <input
                    type="text"
                    name="items[${itemIndex}][description]"
                >
            </div>

            <button type="button" class="btn danger remove-item">
                Remove
            </button>
        `;

        container.appendChild(item);
        itemIndex++;

        updateRemoveButtons();
        updateTotals();
    });

    container.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-item')) {
            event.target.closest('.item').remove();
            updateRemoveButtons();
            updateTotals();
        }
    });

    document.querySelector('form').addEventListener('input', updateTotals);

    updateTotals();
</script>

</body>
</html>
