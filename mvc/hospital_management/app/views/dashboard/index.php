
<?php
$role = $user['role'];
$username = $user['username'];

$h = function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $h($title) ?> | Hospital Management System</title>

    <style>
            html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100%;
            overscroll-behavior: none;
        }

        
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            overflow-x: hidden;

        }

       

        /* Main layout */
        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 300px;
            background: #172b4d;
            color: white;
            padding: 25px 20px;
            flex-shrink: 0;
        }

        .brand {
            font-size: 30px;
            font-weight: 700;
            padding: 10px 20px 40px;
            line-height: 1.5;
        }

        .brand span {
            color: #38bdf8;
        }

        .menu-title {
            color: #94a3b8;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1.2px;
            margin: 22px 10px 10px;
            text-transform: uppercase;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #dbeafe;
            text-decoration: none;
            padding: 12px 14px;
            margin-bottom: 5px;
            border-radius: 8px;
            font-size: 14px;
            transition: 0.2s;
        }

        .nav-link:hover {
            background: #29466e;
            color: white;
            text-decoration: none;
            transform: translateX(3px);
        }

        .nav-icon {
            font-size: 18px;
            width: 22px;
            text-align: center;
        }

        /* Main content */
        .main-content {
            flex: 1;
            min-width: 0;
        }

        /* Topbar */
        .topbar {
            background: white;
            min-height: 75px;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            gap: 15px;
        }

        .topbar h2 {
            font-size: 19px;
            color: #172b4d;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #dbeafe;
            color: #1d4ed8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 17px;
        }

        .user-name {
            font-weight: 600;
            font-size: 14px;
        }

        .user-role {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
        }

        .logout-button {
            background: #29466e;
            color: white;
            border: none;
            border-radius: 7px;
            padding: 10px 16px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.2s;
        }

        .logout-button:hover {
            background: #134ea0;
        }

        /* Dashboard content */
        .content {
            padding: 30px;
        }

        .welcome {
            background: linear-gradient(120deg, #1e40af, #0284c7);
            color: white;
            padding: 30px;
            border-radius: 14px;
            margin-bottom: 30px;
            box-shadow: 0 5px 18px rgba(30,64,175,0.15);
        }

        .welcome h1 {
            font-size: 27px;
            margin-bottom: 9px;
        }

        .welcome p {
            color: #e0f2fe;
            font-size: 14px;
        }

        .section-heading {
            font-size: 19px;
            margin-bottom: 18px;
            color: #172b4d;
        }

        /* Module cards */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .module-card {
            background: white;
            padding: 23px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 3px 10px rgba(0,0,0,0.03);
            transition: 0.25s;
            display: flex;
            flex-direction: column;
            min-height: 190px;
        }

        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 9px 22px rgba(0,0,0,0.08);
        }

        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 11px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 23px;
            margin-bottom: 17px;
        }

        .blue {
            background: #dbeafe;
        }

        .green {
            background: #dcfce7;
        }

        .purple {
            background: #ede9fe;
        }

        .orange {
            background: #ffedd5;
        }

        .pink {
            background: #fce7f3;
        }

        .cyan {
            background: #cffafe;
        }

        .module-card h3 {
            font-size: 16px;
            margin-bottom: 8px;
            color: #1e293b;
        }

        .module-card p {
            font-size: 13px;
            line-height: 1.6;
            color: #64748b;
            margin-bottom: 18px;
            flex: 1;
        }

        .card-link {
            display: inline-block;
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
        }

        .card-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        /* Doctor appointments */
        .appointment-section {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            margin-bottom: 30px;
            overflow-x: auto;
        }

        .appointment-table {
            width: 100%;
            min-width: 850px;
            border-collapse: collapse;
        }

        .appointment-table th,
        .appointment-table td {
            padding: 13px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
        }

        .appointment-table th {
            background: #f1f5f9;
            color: #334155;
        }

        .appointment-table tr:hover {
            background: #f8fafc;
        }

        .appointment-status {
            font-weight: 600;
            color: #2563eb;
        }

        .no-appointments {
            text-align: center;
            padding: 25px;
            color: #64748b;
        }


        /* Footer */
        .footer {
            padding: 25px 30px;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
        }

        /* Responsive */
        @media (max-width: 1050px) {
            .card-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .layout {
                display: block;
            }

            .sidebar {
                width: 100%;
                padding: 15px;
            }

            .brand {
                padding-bottom: 10px;
            }

            .menu-title {
                margin-top: 12px;
            }

            .topbar {
                padding: 15px;
                flex-wrap: wrap;
            }

            .content {
                padding: 18px;
            }

            .welcome {
                padding: 23px;
            }

            .welcome h1 {
                font-size: 22px;
            }

            .card-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="brand">
               Younseii
            <span>Hospital</span>
        </div>

        <div class="menu-title">Main Menu</div>

        <a class="nav-link" href="/">
            <span class="nav-icon">🏠</span>
            Home
        </a>

        <?php if ($role === 'admin'): ?>

            <div class="menu-title">Administration</div>

            <a class="nav-link" href="/departments">
                <span class="nav-icon">🏢</span>
                Departments
            </a>

            <a class="nav-link" href="/doctors">
                <span class="nav-icon">👨‍⚕️</span>
                Doctors
            </a>

            <a class="nav-link" href="/nurses">
                <span class="nav-icon">👩‍⚕️</span>
                Nurses
            </a>

            <a class="nav-link" href="/wards">
                <span class="nav-icon">🛏️</span>
                Ward Management
            </a>

        <?php endif; ?>

        <?php if (in_array($role, ['admin', 'receptionist'], true)): ?>

            <div class="menu-title">Patient Services</div>

            <a class="nav-link" href="/patients">
                <span class="nav-icon">👥</span>
                Patients
            </a>

            <a class="nav-link" href="/patients/create">
                <span class="nav-icon">➕</span>
                Register Patient
            </a>

            <a class="nav-link" href="/billing">
                <span class="nav-icon">💳</span>
                Billing
            </a>

        <?php endif; ?>

        <?php if (in_array($role, ['admin', 'doctor'], true)): ?>

            <div class="menu-title">Clinical Services</div>

            <a class="nav-link" href="/prescriptions">
                <span class="nav-icon">💊</span>
                Prescriptions
            </a>

        <?php endif; ?>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">

            <h2><?= $h($title) ?></h2>

            <div class="user-info">

                <div class="avatar">
                    <?= $h(strtoupper(substr($username, 0, 1))) ?>
                </div>

                <div>
                    <div class="user-name"><?= $h($username) ?></div>
                    <div class="user-role"><?= $h(ucfirst($role)) ?></div>
                </div>

                <form action="/logout" method="POST">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= $h($csrfToken) ?>"
                    >

                    <button class="logout-button" type="submit">
                        Logout
                    </button>

                </form>

            </div>

        </header>

        <!-- DASHBOARD -->
        <section class="content">

            <div class="welcome">

                <h1>Welcome back, <?= $h($username) ?>! 👋</h1>

                <p>
                    Welcome to your hospital management dashboard.
                    Manage your hospital activities from one place.
                </p>

            </div>

            <?php if ($role === 'doctor'): ?>

                <h2 class="section-heading">
                    My Patient Appointments
                </h2>

                <div class="appointment-section">

                    <?php if (empty($doctorProfileFound)): ?>

                        <p class="no-appointments">
                            Your doctor profile is not linked to your login account.
                            Please contact the administrator.
                        </p>

                    <?php elseif (empty($appointments)): ?>

                        <p class="no-appointments">
                            No appointments have been assigned to you yet.
                        </p>

                    <?php else: ?>

                        <table class="appointment-table">

                            <thead>
                                <tr>
                                    <th>Appointment No.</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Phone</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($appointments as $appointment): ?>

                                    <tr>

                                        <td>
                                            <?= $h($appointment['appointment_number']) ?>
                                        </td>

                                        <td>
                                            <?= $h($appointment['patient_number'] ?? 'N/A') ?>
                                        </td>

                                        <td>
                                            <?= $h(
                                                $appointment['patient_first_name'] . ' ' .
                                                $appointment['patient_last_name']
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= $h($appointment['patient_phone'] ?? 'N/A') ?>
                                        </td>

                                        <td>
                                            <?= $h(
                                                date(
                                                    'd-m-Y',
                                                    strtotime($appointment['appointment_date'])
                                                )
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= $h(
                                                date(
                                                    'h:i A',
                                                    strtotime($appointment['appointment_time'])
                                                )
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= $h($appointment['reason'] ?? 'N/A') ?>
                                        </td>

                                        <td class="appointment-status">
                                            <?= $h($appointment['status']) ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    <?php endif; ?>

                </div>

            <?php endif; ?>


            <h2 class="section-heading">Management Modules</h2>

            <div class="card-grid">

                <?php if ($role === 'admin'): ?>

                    <div class="module-card">
                        <div class="card-icon blue">🏢</div>
                        <h3>Department Management</h3>
                        <p>Create, update and manage hospital departments.</p>
                        <a class="card-link" href="/departments">Manage Departments →</a>
                    </div>

                    <div class="module-card">
                        <div class="card-icon green">👨‍⚕️</div>
                        <h3>Doctor Management</h3>
                        <p>Register and manage hospital doctors.</p>
                        <a class="card-link" href="/doctors">Manage Doctors →</a>
                    </div>

                    <div class="module-card">
                        <div class="card-icon purple">👩‍⚕️</div>
                        <h3>Nurse Management</h3>
                        <p>Manage nurse profiles and staff information.</p>
                        <a class="card-link" href="/nurses">Manage Nurses →</a>
                    </div>

                    <div class="module-card">
                        <div class="card-icon cyan">🛏️</div>
                        <h3>Ward Management</h3>
                        <p>Manage hospital wards and related facilities.</p>
                        <a class="card-link" href="/wards">Manage Wards →</a>
                    </div>

                <?php endif; ?>

                <?php if (in_array($role, ['admin', 'receptionist'], true)): ?>

                    <div class="module-card">
                        <div class="card-icon blue">👥</div>
                        <h3>Patient Management</h3>
                        <p>View and manage registered patient information.</p>
                        <a class="card-link" href="/patients">Manage Patients →</a>
                    </div>

                    <div class="module-card">
                        <div class="card-icon green">➕</div>
                        <h3>Patient Registration</h3>
                        <p>Register new patients in the hospital system.</p>
                        <a class="card-link" href="/patients/create">Register Patient →</a>
                    </div>

                    <div class="module-card">
                        <div class="card-icon orange">💳</div>
                        <h3>Billing Management</h3>
                        <p>Manage patient bills and payment records.</p>
                        <a class="card-link" href="/billing">Manage Billing →</a>
                    </div>

                <?php endif; ?>

                <?php if (in_array($role, ['admin', 'doctor'], true)): ?>

                    <div class="module-card">
                        <div class="card-icon pink">💊</div>
                        <h3>Prescription Management</h3>
                        <p>Create and view patient prescriptions.</p>
                        <a class="card-link" href="/prescriptions">Manage Prescriptions →</a>
                    </div>

                <?php endif; ?>

            </div>

        </section>

        <footer class="footer">
            © <?= date('Y') ?> Hospital Management System.
            All rights reserved.
        </footer>

    </main>

</div>

</body>
</html>
