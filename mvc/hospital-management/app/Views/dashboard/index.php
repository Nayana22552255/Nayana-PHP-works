<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management Dashboard</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php require __DIR__ . '/../partials/navigation.php'; ?>
    <main class="dashboard">
        <header class="dashboard-header">
            <div>
                <p class="dashboard-eyebrow">Hospital Management</p>
                <h1>Dashboard</h1>
                <p>Overview of patients, appointments, and billing.</p>
            </div>
        </header>

        <section class="dashboard-cards" aria-label="Hospital summary">
            <a class="dashboard-card" href="/patients">
                <span>Total patients</span>
                <strong><?= (int) $summary['total_patients'] ?></strong>
            </a>
            <a class="dashboard-card" href="/doctors">
                <span>Total doctors</span>
                <strong><?= (int) $summary['total_doctors'] ?></strong>
            </a>
            <a class="dashboard-card" href="/appointments">
                <span>Total appointments</span>
                <strong><?= (int) $summary['total_appointments'] ?></strong>
            </a>
            <a class="dashboard-card" href="/appointments">
                <span>Appointments today</span>
                <strong><?= (int) $summary['appointments_today'] ?></strong>
            </a>
            <a class="dashboard-card" href="/prescriptions">
                <span>Prescriptions</span>
                <strong><?= (int) $summary['total_prescriptions'] ?></strong>
            </a>
            <a class="dashboard-card" href="/bills">
                <span>Bills</span>
                <strong><?= (int) $summary['total_bills'] ?></strong>
            </a>
            <div class="dashboard-card">
                <span>Total billed</span>
                <strong><?= number_format((float) $summary['total_billed'], 2) ?></strong>
            </div>
            <div class="dashboard-card">
                <span>Total payments received</span>
                <strong><?= number_format((float) $summary['total_paid'], 2) ?></strong>
            </div>
        </section>

        <section class="dashboard-section">
            <div class="dashboard-section-heading">
                <div>
                    <p class="dashboard-eyebrow">Schedule</p>
                    <h2>Upcoming appointments</h2>
                </div>
                <a href="/appointments">View all appointments</a>
            </div>

            <div class="dashboard-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($appointments)): ?>
                            <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                                    <td><?= htmlspecialchars($appointment['appointment_time']) ?></td>
                                    <td><?= htmlspecialchars($appointment['patient_name']) ?></td>
                                    <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                                    <td><?= htmlspecialchars($appointment['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No upcoming appointments.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
