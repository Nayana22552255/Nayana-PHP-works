
<?php

$user = $user ?? [];
$appointments = $appointments ?? [];

function scheduleEscape($value)
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

$doctorName = $user['username'] ?? 'Doctor';

if (!empty($appointments)) {
    $doctorName = $appointments[0]['doctor_first_name'] . ' ' .
                  $appointments[0]['doctor_last_name'];
}

$totalAppointments = count($appointments);
$scheduled = 0;
$confirmed = 0;
$completed = 0;
$cancelled = 0;

foreach ($appointments as $appointment) {
    switch ($appointment['status']) {
        case 'Scheduled':
            $scheduled++;
            break;
        case 'Confirmed':
            $confirmed++;
            break;
        case 'Completed':
            $completed++;
            break;
        case 'Cancelled':
            $cancelled++;
            break;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= scheduleEscape($title ?? 'My Appointments') ?></title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
    color: #1e293b;
}

.dashboard {
    display: flex;
    min-height: 100vh;
}

/* Sidebar */

.sidebar {
    position:fixed;
    width: 245px;
    min-width: 245px;
    background: #14599b;
    color: white;
    padding: 25px 15px;
    min-height: 100vh;

}

.logo {
    text-align: center;
    font-size: 21px;
    margin-bottom: 40px;
    font-weight:bold;
}

.logo small {
    font-size: 12px;
}

.sidebar-menu {
    list-style: none;
}

.sidebar-menu li {
    margin-bottom: 12px;
}

.sidebar-menu a {
    display: block;
    padding: 14px 15px;
    color: white;
    text-decoration: none;
    border-radius: 7px;
    font-size: 14px;
    transition: 0.3s;
}

.sidebar-menu a:hover,
.sidebar-menu a.active {
    background: #0f477e;
}

/* Main area */

.main-content {
    flex: 1;
    min-width: 0;
    margin-left: 245px;
    padding: 90px 35px 85px;
}

/* Fixed header */

.topbar {
    position: fixed;
    top: 0;
    right: 0;
    left: 245px;
    height: 65px;
    background: #ffffff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 35px;
    gap: 15px;
    z-index: 1000;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.navbar-title {
    color: #14599b;
    font-size: 20px;
    font-weight: bold;
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 15px;
}

.doctor-account {
    color: #475569;
    font-size: 14px;
}

.logout-form {
    margin: 0;
}

.logout-btn {
    background: #dc2626;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 9px 15px;
    cursor: pointer;
}

.logout-btn:hover {
    background: #b91c1c;
}

/* Fixed footer */

.dashboard-footer {
    position: fixed;
    bottom: 0;
    right: 0;
    width: calc(100% - 245px);
    height: 55px;
    background: white;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #64748b;
    font-size: 13px;
    box-shadow: 0 -2px 6px rgba(0,0,0,0.05);
    z-index: 1000;
}

/* Page heading */

.page-heading {
    background: white;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.page-heading h1 {
    color: #14599b;
    font-size: 25px;
    margin-bottom: 9px;
}

.page-heading p {
    color: #64748b;
    line-height: 1.5;
}

/* Statistics */

.statistics {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    padding: 22px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.stat-card p {
    color: #64748b;
    font-size: 13px;
    margin-bottom: 12px;
}

.stat-card h2 {
    color: #14599b;
    font-size: 29px;
}

/* Appointment table */

.appointment-section {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.section-heading {
    margin-bottom: 20px;
}

.section-heading h2 {
    font-size: 20px;
    color: #334155;
    margin-bottom: 7px;
}

.section-heading p {
    color: #64748b;
    font-size: 13px;
}

/* Search and filter */

.filters {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.filters input,
.filters select {
    padding: 11px 13px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 13px;
    outline: none;
}

.filters input {
    flex: 1;
    min-width: 200px;
}

.filters select {
    min-width: 160px;
    background: white;
}

.filters input:focus,
.filters select:focus {
    border-color: #2563eb;
}

/* Table */

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.appointment-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 950px;
}

.appointment-table th,
.appointment-table td {
    padding: 15px 12px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
    font-size: 13px;
}

.appointment-table th {
    background: #f8fafc;
    color: #64748b;
    white-space: nowrap;
}

.appointment-table td {
    color: #334155;
}

.appointment-table tbody tr:hover {
    background: #f8fafc;
}

.patient-name {
    color: #14599b !important;
    font-weight: bold;
}

/* Status */

.status {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: bold;
    white-space: nowrap;
}

.status-scheduled {
    background: #dbeafe;
    color: #1d4ed8;
}

.status-confirmed {
    background: #dcfce7;
    color: #15803d;
}

.status-completed {
    background: #e0e7ff;
    color: #4338ca;
}

.status-cancelled {
    background: #fee2e2;
    color: #b91c1c;
}

.status-no-show {
    background: #fef3c7;
    color: #92400e;
}

/* Empty state */

.empty-message {
    text-align: center;
    padding: 40px 15px;
    color: #64748b;
}

.empty-message h3 {
    color: #334155;
    margin-bottom: 10px;
}

.no-results {
    display: none;
    padding: 25px;
    text-align: center;
    color: #64748b;
}

/* Responsive */

@media (max-width: 1100px) {
    .statistics {
        grid-template-columns: repeat(2, 1fr);
    }

    .main-content {
        padding-left: 20px;
        padding-right: 20px;
    }
}
.complete-btn {
    background: #16a34a;
    color: #ffffff;
    border: none;
    padding: 9px 15px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
}

.complete-btn:hover {
    background: #15803d;
}

.status-completed {
    display: inline-block;
    background: #dcfce7;
    color: #166534;
    padding: 7px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
}

@media (max-width: 700px) {

    .dashboard {
        display: block;
    }

    .sidebar {
        width: 100%;
        min-width: 0;
        min-height: auto;
        padding: 15px;
    }

    .logo {
        margin-bottom: 15px;
    }

    .sidebar-menu {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .sidebar-menu li {
        margin: 0;
    }

    .sidebar-menu a {
        padding: 10px;
        font-size: 12px;
    }

    .topbar {
        left: 0;
        width: 100%;
        height: 60px;
        padding: 0 15px;
    }

    .main-content {
        margin-left: 0;
        padding: 80px 15px 75px;
    }

    .dashboard-footer {
        width: 100%;
        height: 50px;
    }

    .statistics {
        gap: 10px;
    }

    .stat-card {
        padding: 15px;
    }

    .stat-card h2 {
        font-size: 24px;
    }

    .appointment-section {
        padding: 15px;
    }
}

</style>

</head>

<body>

<div class="dashboard">

    <!-- Sidebar -->

    <aside class="sidebar">

        <div class="logo">
            Younseii Hospital
        </div>

        <ul class="sidebar-menu">

            <li>
                <a href="/doctor/dashboard">
                    ▣ Dashboard
                </a>
            </li>

            <li>
                <a href="/doctor/schedule" class="active">
                    ▦ My Appointments
                </a>
            </li>

            <li>
                <a href="/doctor/patients">
                    ♙ My Patients
                </a>
            </li>

            

            <li>
                <a href="/doctor/prescriptions">
                    ▧ Prescriptions
                </a>
            </li>

        </ul>

    </aside>


    <!-- Main Content -->

    <main class="main-content">

        <!-- Fixed Header -->

        <header class="topbar">

            <div class="navbar-title"></div>

            <div class="topbar-right">
                <span class="doctor-account">
                    <?= scheduleEscape($doctorName) ?>
                </span>

                <form method="POST"
                      action="/logout"
                      class="logout-form">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= scheduleEscape($csrfToken ?? '') ?>"
                    >

                    <button type="submit" class="logout-btn">
                        Logout
                    </button>

                </form>
            </div>

        </header>


        <!-- Page Heading -->

        <section class="page-heading">

            <h1>My Appointments</h1>

            <p>
                View and manage the appointments assigned to you.
                Check patient information, appointment dates and statuses.
            </p>

        </section>


        <!-- Statistics -->

        <section class="statistics">

            <div class="stat-card">
                <p>Total Appointments</p>
                <h2><?= $totalAppointments ?></h2>
            </div>

            <div class="stat-card">
                <p>Scheduled</p>
                <h2><?= $scheduled ?></h2>
            </div>

            <div class="stat-card">
                <p>Confirmed</p>
                <h2><?= $confirmed ?></h2>
            </div>

            <div class="stat-card">
                <p>Completed</p>
                <h2><?= $completed ?></h2>
            </div>

        </section>


        <!-- Appointment List -->

        <section class="appointment-section">

            <div class="section-heading">

                <h2>Appointment List</h2>

                <p>
                    All appointments assigned to your doctor account.
                </p>

            </div>


            <?php if (!empty($appointments)): ?>

                <!-- Search and Filter -->

                <div class="filters">

                    <input
                        type="text"
                        id="appointmentSearch"
                        placeholder="Search patient name, ID or appointment number..."
                    >

                    <select id="statusFilter">

                        <option value="">All Statuses</option>
                        <option value="Scheduled">Scheduled</option>
                        <option value="Confirmed">Confirmed</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="No-show">No-show</option>

                    </select>

                </div>


                <div class="table-wrapper">

                    <table class="appointment-table" id="appointmentTable">

                        <thead>

                            <tr>
                                <th>Appointment No.</th>
                                <th>Patient ID</th>
                                <th>Patient Name</th>
                                <th>Phone</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Duration</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($appointments as $appointment): ?>

                                <?php
                                $statusClass = strtolower(
                                    str_replace(
                                        ' ',
                                        '-',
                                        $appointment['status']
                                    )
                                );
                                ?>

                                <tr class="appointment-row">

                                    <td>
                                        <?= scheduleEscape($appointment['appointment_number']) ?>
                                    </td>

                                    <td>
                                        <?= scheduleEscape($appointment['patient_number']) ?>
                                    </td>

                                    <td class="patient-name">

                                        <?= scheduleEscape(
                                            $appointment['patient_first_name'] . ' ' .
                                            $appointment['patient_last_name']
                                        ) ?>

                                    </td>

                                    <td>
                                        <?= scheduleEscape($appointment['patient_phone']) ?>
                                    </td>

                                    <td>
                                        <?= scheduleEscape(
                                            date(
                                                'd M Y',
                                                strtotime($appointment['appointment_date'])
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= scheduleEscape(
                                            date(
                                                'h:i A',
                                                strtotime($appointment['appointment_time'])
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= (int)$appointment['duration_minutes'] ?> minutes
                                    </td>

                                    <td>
                                        <?= scheduleEscape(
                                            $appointment['reason'] ?: 'Not specified'
                                        ) ?>
                                    </td>

                                    <td>

                                        <span class="status status-<?= scheduleEscape($statusClass) ?>">
                                            <?= scheduleEscape($appointment['status']) ?>
                                        </span>

                                    </td>

                                </tr>
                                <td>
                                <?php if (in_array($appointment['status'], ['Scheduled', 'Confirmed'], true)): ?>

                                    <form method="POST" action="/doctor/appointments/complete">

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="appointment_id"
                                            value="<?= (int)$appointment['id'] ?>"
                                        >

                                        <button type="submit" class="complete-btn">
                                            Mark Complete
                                        </button>

                                    </form>

                                <?php elseif ($appointment['status'] === 'Completed'): ?>

                                    <span class="status-completed">Completed</span>

                                <?php else: ?>

                                    <span>—</span>

                                <?php endif; ?>
                            </td>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <div class="no-results" id="noResults">
                    No appointments match your search.
                </div>

            <?php else: ?>

                <div class="empty-message">

                    <h3>No Appointments Found</h3>

                    <p>
                        You do not have any appointments assigned to you yet.
                    </p>

                </div>

            <?php endif; ?>

        </section>


        <!-- Footer -->

        <footer class="dashboard-footer">

            &copy; <?= date('Y') ?> Younseii Hospital.
            All Rights Reserved.

        </footer>

    </main>

</div>


<!-- Search and Status Filter -->

<script>

const searchInput = document.getElementById('appointmentSearch');
const statusFilter = document.getElementById('statusFilter');
const tableRows = document.querySelectorAll('.appointment-row');
const noResults = document.getElementById('noResults');

function filterAppointments() {

    if (!searchInput || !statusFilter) {
        return;
    }

    const searchValue = searchInput.value.toLowerCase().trim();
    const selectedStatus = statusFilter.value.toLowerCase();

    let visibleCount = 0;

    tableRows.forEach(function(row) {

        const rowText = row.textContent.toLowerCase();

        const statusElement = row.querySelector('.status');

        const rowStatus = statusElement
            ? statusElement.textContent.trim().toLowerCase()
            : '';

        const matchesSearch = rowText.includes(searchValue);

        const matchesStatus =
            selectedStatus === '' || rowStatus === selectedStatus;

        if (matchesSearch && matchesStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }

    });

    if (noResults) {
        noResults.style.display =
            visibleCount === 0 ? 'block' : 'none';
    }

}

if (searchInput) {
    searchInput.addEventListener('input', filterAppointments);
}

if (statusFilter) {
    statusFilter.addEventListener('change', filterAppointments);
}

</script>

</body>
</html>
