
<?php
function prescriptionCreateEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= prescriptionCreateEscape($title) ?></title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .container {
            max-width: 900px;
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

        .medicine {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr 0.7fr 1.5fr auto;
            gap: 8px;
            align-items: end;
            margin-bottom: 15px;
        }

        .medicine label {
            font-size: 12px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            text-decoration: none;
        }

        .primary {
            background: #263b5e;
            color: white;
        }

        .danger {
            background: #dc2626;
            color: white;
        }

        .note {
            color: #666;
            font-size: 13px;
        }

        @media(max-width: 850px) {
            .medicine {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Create New Prescription</h2>

    <form method="POST" action="/prescriptions/store">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= prescriptionCreateEscape($csrfToken) ?>"
        >

        <label>Select Patient *</label>

        <select name="patient_id" required>
            <option value="">-- Select Patient --</option>

            <?php foreach ($patients as $patient): ?>

                <option value="<?= (int) $patient['id'] ?>">
                    <?= prescriptionCreateEscape(
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

        <p class="note">
            If you enter an appointment ID, it must belong to the selected patient and doctor.
        </p>

        <label>Prescription Date *</label>

        <input
            type="date"
            name="prescription_date"
            value="<?= date('Y-m-d') ?>"
            required
        >

        <label>Diagnosis</label>

        <textarea
            name="diagnosis"
            rows="3"
            placeholder="Enter doctor's diagnosis"
        ></textarea>

        <h3>Medicine Details</h3>

        <div id="medicines-container">

            <div class="medicine">

                <div>
                    <label>Medicine Name *</label>
                    <input
                        type="text"
                        name="items[0][medicine_name]"
                        required
                    >
                </div>

                <div>
                    <label>Dosage *</label>
                    <input
                        type="text"
                        name="items[0][dosage]"
                        placeholder="As prescribed"
                        required
                    >
                </div>

                <div>
                    <label>Frequency *</label>
                    <input
                        type="text"
                        name="items[0][frequency]"
                        placeholder="As prescribed"
                        required
                    >
                </div>

                <div>
                    <label>Duration *</label>
                    <input
                        type="text"
                        name="items[0][duration]"
                        placeholder="As prescribed"
                        required
                    >
                </div>

                <div>
                    <label>Quantity *</label>
                    <input
                        type="number"
                        name="items[0][quantity]"
                        value="1"
                        min="1"
                        required
                    >
                </div>

                <div>
                    <label>Instructions</label>
                    <input
                        type="text"
                        name="items[0][instructions]"
                        placeholder="Optional"
                    >
                </div>

                <button
                    type="button"
                    class="btn danger remove-medicine"
                    disabled
                >
                    Remove
                </button>

            </div>

        </div>

        <button
            type="button"
            class="btn primary"
            id="add-medicine"
        >
            + Add Another Medicine
        </button>

        <label>Additional Notes</label>

        <textarea
            name="notes"
            rows="3"
            placeholder="Additional instructions from the doctor"
        ></textarea>

        <br>

        <button type="submit" class="btn primary">
            Save Prescription
        </button>

        <a href="/prescriptions" class="btn primary">
            Cancel
        </a>

    </form>

</div>

<script>
    const medicineContainer =
        document.getElementById('medicines-container');

    const addMedicineButton =
        document.getElementById('add-medicine');

    let medicineIndex = 1;

    function updateRemoveButtons() {
        const buttons =
            medicineContainer.querySelectorAll('.remove-medicine');

        buttons.forEach(function(button) {
            button.disabled = buttons.length === 1;
        });
    }

    addMedicineButton.addEventListener('click', function() {

        const medicine = document.createElement('div');

        medicine.className = 'medicine';

        medicine.innerHTML = `
            <div>
                <label>Medicine Name *</label>
                <input
                    type="text"
                    name="items[${medicineIndex}][medicine_name]"
                    required
                >
            </div>

            <div>
                <label>Dosage *</label>
                <input
                    type="text"
                    name="items[${medicineIndex}][dosage]"
                    required
                >
            </div>

            <div>
                <label>Frequency *</label>
                <input
                    type="text"
                    name="items[${medicineIndex}][frequency]"
                    required
                >
            </div>

            <div>
                <label>Duration *</label>
                <input
                    type="text"
                    name="items[${medicineIndex}][duration]"
                    required
                >
            </div>

            <div>
                <label>Quantity *</label>
                <input
                    type="number"
                    name="items[${medicineIndex}][quantity]"
                    value="1"
                    min="1"
                    required
                >
            </div>

            <div>
                <label>Instructions</label>
                <input
                    type="text"
                    name="items[${medicineIndex}][instructions]"
                >
            </div>

            <button type="button" class="btn danger remove-medicine">
                Remove
            </button>
        `;

        medicineContainer.appendChild(medicine);

        medicineIndex++;

        updateRemoveButtons();
    });

    medicineContainer.addEventListener('click', function(event) {

        if (event.target.classList.contains('remove-medicine')) {

            event.target.closest('.medicine').remove();

            updateRemoveButtons();
        }

    });

    updateRemoveButtons();
</script>

</body>
</html>
