
<?php

$user = $user ?? [];
$appointments = $appointments ?? [];
$doctorProfileFound = $doctorProfileFound ?? false;

function doctorEscape($value)
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

// Doctor name
$doctorName = 'Doctor';

if (!empty($appointments)) {
    $doctorName = $appointments[0]['doctor_first_name'] . ' ' .
                  $appointments[0]['doctor_last_name'];
} else {
    $doctorName = $user['username'] ?? 'Doctor';
}

// Current date
$today = date('Y-m-d');

// Dashboard statistics
$totalAppointments = count($appointments);

$totalPatients = count(array_unique(array_column(
    $appointments,
    'patient_number'
)));

$todaysAppointments = [];
$completedAppointments = 0;
$upcomingAppointments = [];

foreach ($appointments as $appointment) {

    if ($appointment['appointment_date'] === $today) {
        $todaysAppointments[] = $appointment;
    }

    if ($appointment['status'] === 'Completed') {
        $completedAppointments++;
    }

    if (
        $appointment['appointment_date'] >= $today &&
        in_array($appointment['status'], ['Scheduled', 'Confirmed'], true)
    ) {
        $upcomingAppointments[] = $appointment;
    }
}

// Weekly calendar dates (Monday to Sunday)
$monday = new DateTime('monday this week');

$weekDays = [];

for ($i = 0; $i < 7; $i++) {

    $date = clone $monday;
    $date->modify("+$i days");

    $dateString = $date->format('Y-m-d');

    $appointmentCount = 0;

    foreach ($appointments as $appointment) {
        if ($appointment['appointment_date'] === $dateString) {
            $appointmentCount++;
        }
    }

    $weekDays[] = [
        'date' => $dateString,
        'day' => $date->format('D'),
        'number' => $date->format('d'),
        'count' => $appointmentCount
    ];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?= doctorEscape($title ?? 'Doctor Dashboard') ?></title>

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

        /* Main Layout */

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */

        .sidebar {
            position:fixed;
            top:0;
            left:0;
            width: 245px;
            min-width: 245px;
            height:100vh;
            overflow-y:auto;
            background: #14599b;
            color: white;
            z-index:1000;
            padding: 25px 15px;
        }

        .logo {
            text-align: center;
            font-size: 21px;
            font-weight: bold;
            margin-bottom: 40px;
            letter-spacing: 0.5px;
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

        .sidebar-menu span {
            margin-right: 10px;
            font-size: 17px;
        }

        /* Main Content */

        .main-content {
                margin-left: 250px;
                width: calc(100% - 250px);
                min-height: 100vh;
                padding: 95px 30px 30px;
        }

        /* Topbar */

        .topbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 250px;
            height: 70px;
            z-index: 900;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            padding: 0 30px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
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
            font-size: 13px;
        }

        .logout-btn:hover {
            background: #b91c1c;
        }

        /* Welcome Card */

        .welcome-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.06);
            margin-bottom: 30px;
        }

        .welcome-card h1 {
            color: #14599b;
            font-size: 25px;
            margin-bottom: 10px;
        }

        .welcome-card p {
            color: #64748b;
            line-height: 1.6;
        }

        .welcome-date {
            margin-top: 12px;
            color: #64748b;
            font-size: 13px;
        }

        /* Statistics */

        .statistics-heading {
            margin-bottom: 15px;
            color: #334155;
            font-size: 19px;
        }

        .statistics {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 30px;
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
            font-size: 30px;
        }

        /* Schedule */

        .schedule-section {
            background: white;
            padding: 23px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .section-title {
            font-size: 19px;
            color: #334155;
            margin-bottom: 20px;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 12px;
        }

        .calendar-day {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 15px 5px;
            text-align: center;
            color: #64748b;
        }

        .calendar-day strong {
            display: block;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .calendar-day .day-number {
            font-size: 21px;
            font-weight: bold;
            color: #334155;
        }

        .calendar-day.today {
            background: #14599b;
            border-color: #14599b;
            color: white;
        }

        .calendar-day.today .day-number {
            color: white;
        }

        .appointment-count {
            display: block;
            font-size: 11px;
            margin-top: 8px;
        }

        /* Appointments */

        .appointments-section {
            background: white;
            padding: 23px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .appointment-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .appointment-heading h2 {
            font-size: 19px;
            color: #334155;
        }

        .appointment-heading a {
            color: #14599b;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .appointment-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        .appointment-table th,
        .appointment-table td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
        }

        .appointment-table th {
            color: #64748b;
            font-weight: bold;
            background: #f8fafc;
        }

        .appointment-table td {
            color: #334155;
        }

        .appointment-table tbody tr:hover {
            background: #f8fafc;
        }

        .patient-name {
            color: #14599b;
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

        /* Empty State */

        .empty-message {
            text-align: center;
            padding: 35px 15px;
            color: #64748b;
        }

        #medical-records {
            scroll-margin-top: 90px;
        }

        #medical-records a {
            color: #14599b;
            font-weight: bold;
            text-decoration: none;
        }

        .empty-message h3 {
            color: #334155;
            margin-bottom: 10px;
        }

        .profile-warning {
            background: #fee2e2;
            color: #991b1b;
            padding: 18px;
            border-radius: 8px;
            margin-bottom: 20px;
        }


/* Medical Records Section */

.medical-records-section {
    background: #ffffff;
    padding: 28px;
    border-radius: 12px;
    margin-bottom: 30px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
}

.medical-records-heading {
    margin-bottom: 20px;
}

.medical-records-heading h2 {
    color: #14599b;
    font-size: 21px;
    margin-bottom: 8px;
}

.medical-records-heading p {
    color: #64748b;
    font-size: 14px;
    line-height: 1.6;
}

.medical-records-content {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 25px;
    text-align: center;
}

.medical-records-icon {
    font-size: 38px;
    margin-bottom: 12px;
}

.medical-records-content h3 {
    color: #334155;
    font-size: 18px;
    margin-bottom: 10px;
}

.medical-records-content p {
    color: #64748b;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 22px;
}

.medical-records-actions {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}

.medical-record-btn {
    display: inline-block;
    padding: 12px 22px;
    border-radius: 7px;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
    transition: 0.3s ease;
}

.view-patients-btn {
    background: #14599b;
    color: #ffffff !important;
}

.view-patients-btn:hover {
    background: #0f477e;
    transform: translateY(-2px);
}

.view-prescriptions-btn {
    background: #ffffff;
    color: #14599b;
    border: 1px solid #14599b;
}

.view-prescriptions-btn:hover {
    background: #eaf3fc;
    transform: translateY(-2px);
}

@media (max-width: 600px) {
    .medical-records-section {
        padding: 18px;
    }

    .medical-records-actions {
        flex-direction: column;
    }

    .medical-record-btn {
        text-align: center;
        width: 100%;
    }
}


        /* Responsive */

        @media (max-width: 1100px) {

            .statistics {
                grid-template-columns: repeat(2, 1fr);
            }

            .main-content {
                padding: 90px 20px 20px;
            }

        }

        @media (max-width: 768px) {

            .dashboard {
                display: block;
            }

            .sidebar {
                width: 220px;
                min-width: 0;
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

            .main-content {
                        margin-left: 220px;
                        width: calc(100% - 220px);
                        padding: 90px 20px 20px;
            }

            .topbar {
                left: 220px;
                padding: 0 20px;
            }

            .welcome-card {
                padding: 22px;
            }

            .welcome-card h1 {
                font-size: 21px;
            }

            .statistics {
                gap: 10px;
            }

            .stat-card {
                padding: 15px;
            }

            .stat-card h2 {
                font-size: 25px;
            }

            .calendar {
                grid-template-columns: repeat(4, 1fr);
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
                <a href="/doctor/dashboard" class="active">
                    <span>⌂</span> Dashboard
                </a>
            </li>

            <li>
                <a href="/doctor/schedule">
                    <span>▦</span> My Appointments
                </a>
            </li>

            <li>
                <a href="/doctor/patients">
                    <span>♙</span> My Patients
                </a>
            </li>

            <li>
                <a href="#medical-records">
                    <span>▤</span> Medical Records
                </a>
            </li>

            <li>
                <a href="/doctor/prescriptions">
                    <span>▧</span> Prescriptions
                </a>
            </li>

        </ul>

    </aside>


    <!-- Main Content -->

    <main class="main-content">

        <!-- Topbar -->

        <div class="topbar">

            <div class="navbar-title"></div>

            <div class="topbar-right">
                <span class="doctor-account">
                    <?= doctorEscape($doctorName) ?>
                </span>

                <form method="POST"
                      action="/logout"
                      class="logout-form">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= doctorEscape($csrfToken ?? '') ?>"
                    >

                    <button type="submit" class="logout-btn">
                        Logout
                    </button>

                </form>
            </div>

        </div>


        <?php if (!$doctorProfileFound): ?>

            <div class="profile-warning">
                Your doctor profile could not be found.
                Please contact the hospital administrator.
            </div>

        <?php else: ?>


            <!-- Welcome -->

            <section class="welcome-card">

                <h1>
                    Good Morning, Dr. <?= doctorEscape($doctorName) ?>
                </h1>

                <p>
                    Welcome to your doctor dashboard.
                    Manage your appointments and patient consultations here.
                </p>

                <div class="welcome-date">
                    <?= date('l, d F Y') ?>
                </div>

            </section>


            <!-- Statistics -->

            <h2 class="statistics-heading">
                Appointment Reports
            </h2>

            <section class="statistics">

                <div class="stat-card">

                    <p>Total Patients</p>

                    <h2>
                        <?= $totalPatients ?>
                    </h2>

                </div>


                <div class="stat-card">

                    <p>Total Appointments</p>

                    <h2>
                        <?= $totalAppointments ?>
                    </h2>

                </div>


                <div class="stat-card">

                    <p>Completed Appointments</p>

                    <h2>
                        <?= $completedAppointments ?>
                    </h2>

                </div>


                <div class="stat-card">

                    <p>Upcoming Appointments</p>

                    <h2>
                        <?= count($upcomingAppointments) ?>
                    </h2>

                </div>

            </section>


            <!-- Weekly Schedule -->

            <section class="schedule-section">

                <h2 class="section-title">
                    Weekly Schedule
                </h2>

                <div class="calendar">

                    <?php foreach ($weekDays as $day): ?>

                        <div class="calendar-day <?= $day['date'] === $today ? 'today' : '' ?>">

                            <strong>
                                <?= doctorEscape($day['day']) ?>
                            </strong>

                            <span class="day-number">
                                <?= doctorEscape($day['number']) ?>
                            </span>

                            <span class="appointment-count">
                                <?= (int)$day['count'] ?>
                                appointments
                            </span>

                        </div>

                    <?php endforeach; ?>

                </div>

            </section>


            <!-- Medical Records -->
        
<!-- Medical Records Section -->

<section class="medical-records-section" id="medical-records">

    <div class="medical-records-heading">

        <h2>Medical Records</h2>

        <p>
            Access patient information, previous consultations
            and prescription details.
        </p>

    </div>


    <div class="medical-records-content">

        <div class="medical-records-icon">
            🩺
        </div>

        <h3>Manage Patient Medical History</h3>

        <p>
            View your patients' information and access their
            prescription details to support patient care.
        </p>


        <div class="medical-records-actions">

            <a href="/doctor/patients"
               class="medical-record-btn view-patients-btn">

                ♙ View Patients

            </a>


            <a href="/doctor/prescriptions"
               class="medical-record-btn view-prescriptions-btn">

                ▤ View Prescriptions

            </a>

        </div>

    </div>

</section>

           


            <!-- Today's Appointments -->

            <section class="appointments-section" id="appointments">

                <div class="appointment-heading">

                    <h2>Today's Appointments</h2>

                    <a href="/doctor/schedule">
                        View All Appointments
                    </a>

                </div>


                <?php if (!empty($todaysAppointments)): ?>

                    <div class="table-wrapper">

                        <table class="appointment-table">

                            <thead>

                                <tr>
                                    <th>Appointment No.</th>
                                    <th>Patient Name</th>
                                    <th>Patient ID</th>
                                    <th>Phone</th>
                                    <th>Time</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                </tr>

                            </thead>


                            <tbody>

                                <?php foreach ($todaysAppointments as $appointment): ?>

                                    <?php
                                    $statusClass = strtolower(
                                        str_replace(
                                            ' ',
                                            '-',
                                            $appointment['status']
                                        )
                                    );
                                    ?>

                                    <tr>

                                        <td>
                                            <?= doctorEscape($appointment['appointment_number']) ?>
                                        </td>

                                        <td class="patient-name">
                                            <?= doctorEscape(
                                                $appointment['patient_first_name'] . ' ' .
                                                $appointment['patient_last_name']
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= doctorEscape($appointment['patient_number']) ?>
                                        </td>

                                        <td>
                                            <?= doctorEscape($appointment['patient_phone']) ?>
                                        </td>

                                        <td>
                                            <?= doctorEscape(
                                                date(
                                                    'h:i A',
                                                    strtotime($appointment['appointment_time'])
                                                )
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= doctorEscape($appointment['reason'] ?: 'Not specified') ?>
                                        </td>

                                        <td>

                                            <span class="status status-<?= doctorEscape($statusClass) ?>">
                                                <?= doctorEscape($appointment['status']) ?>
                                            </span>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="empty-message">

                        <h3>No Appointments Today</h3>

                        <p>
                            You currently have no appointments scheduled for today.
                        </p>

                    </div>

                <?php endif; ?>

            </section>


            <!-- Upcoming Appointments -->

            <section class="appointments-section">

                <div class="appointment-heading">

                    <h2>Upcoming Appointments</h2>

                    <a href="/doctor/schedule">
                        View Schedule
                    </a>

                </div>


                <?php if (!empty($upcomingAppointments)): ?>

                    <div class="table-wrapper">

                        <table class="appointment-table">

                            <thead>

                                <tr>
                                    <th>Appointment No.</th>
                                    <th>Patient Name</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>

                            </thead>


                            <tbody>

                                <?php foreach ($upcomingAppointments as $appointment): ?>

                                    <?php
                                    $statusClass = strtolower(
                                        str_replace(
                                            ' ',
                                            '-',
                                            $appointment['status']
                                        )
                                    );
                                    ?>

                                    <tr>

                                        <td>
                                            <?= doctorEscape($appointment['appointment_number']) ?>
                                        </td>

                                        <td class="patient-name">
                                            <?= doctorEscape(
                                                $appointment['patient_first_name'] . ' ' .
                                                $appointment['patient_last_name']
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= doctorEscape(
                                                date(
                                                    'd M Y',
                                                    strtotime($appointment['appointment_date'])
                                                )
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= doctorEscape(
                                                date(
                                                    'h:i A',
                                                    strtotime($appointment['appointment_time'])
                                                )
                                            ) ?>
                                        </td>

                                        <td>

                                            <span class="status status-<?= doctorEscape($statusClass) ?>">
                                                <?= doctorEscape($appointment['status']) ?>
                                            </span>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="empty-message">

                        <h3>No Upcoming Appointments</h3>

                        <p>
                            You do not have any upcoming scheduled appointments.
                        </p>

                    </div>

                <?php endif; ?>

            </section>

        <?php endif; ?>

    </main>

</div>

</body>

</html>
