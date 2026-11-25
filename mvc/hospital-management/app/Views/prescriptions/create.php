
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Prescription</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/../partials/navigation.php'; ?>

<div class="container">

    <h2>Add New Prescription</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="/prescriptions/store" method="POST">

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
                        ' - ' . $appointment['appointment_date'] .
                        ' ' . $appointment['appointment_time']
                    ) ?>

                </option>
            <?php endforeach; ?>

        </select>

        <label>Prescription Notes</label>

        <textarea name="notes"><?= htmlspecialchars($old['notes'] ?? '') ?></textarea>

        <h3>Medicine Details</h3>

        <div id="medicineContainer">

            <?php
            $formItems = !empty($items) ? $items : [[
                'medicine_name' => '',
                'dosage' => '',
                'frequency' => '',
                'duration' => '',
                'instructions' => ''
            ]];
            ?>

            <?php foreach ($formItems as $item): ?>

                <div class="medicine-row">

                    <label>Medicine Name</label>
                    <input type="text"
                           name="medicine_name[]"
                           value="<?= htmlspecialchars($item['medicine_name'] ?? '') ?>"
                           required>

                    <label>Dosage</label>
                    <input type="text"
                           name="dosage[]"
                           value="<?= htmlspecialchars($item['dosage'] ?? '') ?>"
                           placeholder="Example: 500 mg">

                    <label>Frequency</label>
                    <input type="text"
                           name="frequency[]"
                           value="<?= htmlspecialchars($item['frequency'] ?? '') ?>"
                           placeholder="Example: Twice daily">

                    <label>Duration</label>
                    <input type="text"
                           name="duration[]"
                           value="<?= htmlspecialchars($item['duration'] ?? '') ?>"
                           placeholder="Example: 3 days">

                    <label>Instructions</label>
                    <textarea name="instructions[]"><?= htmlspecialchars($item['instructions'] ?? '') ?></textarea>

                    <button type="button" class="removeMedicine">
                        Remove Medicine
                    </button>

                    <hr>

                </div>

            <?php endforeach; ?>

        </div>

        <button type="button" id="addMedicine">
            + Add Medicine
        </button>

        <br><br>

        <button type="submit">Save Prescription</button>

    </form>

    <br>

    <a href="/prescriptions">Back to Prescriptions</a>

</div>



<script>
const container = document.getElementById('medicineContainer');

document.getElementById('addMedicine').addEventListener('click', function () {

    const row = document.createElement('div');

    row.className = 'medicine-row';

    row.innerHTML = `
        <label>Medicine Name</label>
        <input type="text" name="medicine_name[]" required>

        <label>Dosage</label>
        <input type="text" name="dosage[]" placeholder="Example: 500 mg">

        <label>Frequency</label>
        <input type="text" name="frequency[]" placeholder="Example: Twice daily">

        <label>Duration</label>
        <input type="text" name="duration[]" placeholder="Example: 3 days">

        <label>Instructions</label>
        <textarea name="instructions[]"></textarea>

        <button type="button" class="removeMedicine">
            Remove Medicine
        </button>

        <hr>
    `;

    container.appendChild(row);

});

container.addEventListener('click', function (event) {

    if (event.target.classList.contains('removeMedicine')) {

        const rows = container.querySelectorAll('.medicine-row');

        if (rows.length > 1) {
            event.target.closest('.medicine-row').remove();
        } else {
            alert('At least one medicine is required.');
        }

    }

});
</script>

</body>
</html>
