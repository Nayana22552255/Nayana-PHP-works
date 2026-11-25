
<?php

$user = $user ?? [];
$doctors = $doctors ?? [];
$appointments = $appointments ?? [];

function patientEscape($value)
{
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title><?= patientEscape($title ?? 'Patient Dashboard') ?></title>

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
    padding-top: 75px;
}
           
    

        /* Fixed Navbar */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 75px;
    background: #063663;
    color: white;
    padding: 0 35px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1000;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.12);
}

.navbar h2 {
    font-size: 21px;
    color: white;
}

        .logout-form {
            margin: 0;
        }


        .logout {
           background: #dc2626;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
        }

        .logout:hover {
            background: #b91c1c;
        }

        /* Main Container */

        .container {
    max-width: 1200px;
    width: 92%;
    margin: 30px auto;
    padding: 15px 0;
}

        /* Welcome Section */
.welcome {
    background: linear-gradient(135deg, #14599b, #2563eb);
    color: white;
    padding: 35px;
    border-radius: 12px;
    margin-bottom: 35px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.welcome h1 {
    color: white;
    font-size: 28px;
    margin-bottom: 12px;
}

.welcome p {
    color: #e2e8f0;
    line-height: 1.7;
}
       

        

        /* Section Heading */
    .section-heading {
    margin-bottom: 22px;
}

.section-heading h2 {
    font-size: 24px;
    color: #0f172a;
    margin-bottom: 20px;
    
}

.section-heading p {
    color: #64748b;
    font-size: 14px;
}
.doctors-section {
    margin-top: 55px;
    margin-bottom: 40px;
}

.appointment-table-wrapper {
    width: 100%;
    display: block;
    overflow-x: auto;
    background: white;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
}
.appointment-table {
    width: 100% !important;
    min-width: 100% !important;
    table-layout: fixed;
    border-collapse: collapse;
}

.appointment-table th {
    background: #14599b;
    color: white;
    padding: 17px;
    margin-top:20px;
}

.appointment-table td {
    padding: 16px 12px;
}

.appointment-table tbody tr:nth-child(even) {
    background: #f8fafc;
}

.appointment-table tbody tr:hover {
    background: #eff6ff;
}
       
        .appointment-table small {
            color: #64748b;
        }

        /* Appointment Status */

        .status-badge {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
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

        /* Success Message */

        .success-message {
            background: #dcfce7;
            color: #166534;
            padding: 14px 18px;
            border-radius: 7px;
            margin-bottom: 20px;
            border: 1px solid #bbf7d0;
        }

        /* Empty Appointments */

        .empty-appointments {
            background: white;
            padding: 35px;
            border-radius: 10px;
            text-align: center;
            color: #64748b;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .empty-appointments h3 {
            color: #334155;
            margin-bottom: 10px;
        }

        /* Doctor Grid */

        .doctor-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
}

       .doctor-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
    transition: 0.3s ease;
}

        .doctor-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.10);
}

        .doctor-icon {
            width: 55px;
    height: 55px;
    background: #dbeafe;
    color: #14599b;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 25px;
    margin-bottom: 18px;
        }

        .doctor-card h3 {
            color: #0f172a;
            margin-bottom: 8px;
            font-size: 19px;
        }

        .specialization {
            color: #2563eb;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .doctor-details {
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }

        .doctor-details p {
            margin-bottom: 10px;
            font-size: 14px;
            color: #475569;
            line-height: 1.5;
        }

        .active-status {
            display: inline-block;
            background: #dcfce7;
            color: #15803d;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 8px;
        }

        .empty-message {
            background: white;
            padding: 35px;
            border-radius: 10px;
            text-align: center;
            color: #64748b;
        }

        /* Book Appointment Button */

        .book-btn {
            display: block;
    margin-top: 20px;
    padding: 13px;
    background: #14599b;
    color: white;
    text-align: center;
    text-decoration: none;
    border-radius: 7px;
    font-weight: bold;
    transition: 0.3s;
        }

        .book-btn:hover {
            background: #0f3f70;
        }

        /* Responsive Design */

       @media (max-width: 600px) {

    .navbar {
        height: 65px;
        padding: 0 15px;
    }

    .navbar h2 {
        font-size: 17px;
    }

    body {
        padding-top: 65px;
    }

    .container {
        width: 95%;
        padding: 10px 0;
        margin: 20px auto;
    }

    .welcome {
        padding: 22px;
    }

    .welcome h1 {
        font-size: 22px;
    }

    .section-heading h2 {
        font-size: 20px;
    }

    .doctor-grid {
        grid-template-columns: 1fr;
    }

    .logout {
        padding: 8px 12px;
        font-size: 13px;
    }
}
    </style>

</head>

<body>

    <!-- Navbar -->

    <div class="navbar">

        <h2>Younseii Hospital</h2>
        <form method="POST" action="/logout" class="logout-form">

    <input
        type="hidden"
        name="csrf_token"
        value="<?= patientEscape($csrfToken ?? '') ?>"
    >

    <button type="submit" class="logout">
        Logout
    </button>

</form>
    
    </div>


    <div class="container">

        <!-- Welcome Section -->

        <div class="welcome">

            <h1>
                Welcome,
                <?= patientEscape($user['username'] ?? 'Patient') ?>!
            </h1>

            <p>
                Welcome to your patient dashboard.
                You can explore our active doctors, book appointments
                and view your appointment history.
            </p>

        </div>


        <!-- My Appointments Section -->

        <section class="appointments-section">

            <div class="section-heading">

                <h2>My Appointments</h2>

                <p>
                    View all your booked appointments and their current status.
                </p>

            </div>


            <?php if (isset($_GET['booking']) && $_GET['booking'] === 'success'): ?>

                <div class="success-message">
                    Your appointment has been booked successfully!
                </div>

            <?php endif; ?>


            <?php if (!empty($appointments)): ?>

                <div class="appointment-table-wrapper">

                    <table class="appointment-table">

                        <thead>

                            <tr>
                                <th>Appointment No.</th>
                                <th>Doctor</th>
                                <th>Department</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Reason</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($appointments as $appointment): ?>

                                <tr>

                                    <td>
                                        <?= patientEscape($appointment['appointment_number']) ?>
                                    </td>

                                    <td>

                                        Dr.
                                        <?= patientEscape(
                                            $appointment['doctor_first_name'] . ' ' .
                                            $appointment['doctor_last_name']
                                        ) ?>

                                        <br>

                                        <small>
                                            <?= patientEscape($appointment['specialization']) ?>
                                        </small>

                                    </td>

                                    <td>
                                        <?= patientEscape($appointment['department_name']) ?>
                                    </td>

                                    <td>
                                        <?= patientEscape(
                                            date(
                                                'd M Y',
                                                strtotime($appointment['appointment_date'])
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= patientEscape(
                                            date(
                                                'h:i A',
                                                strtotime($appointment['appointment_time'])
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= patientEscape(
                                            $appointment['reason'] ?: 'Not specified'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= (int)$appointment['duration_minutes'] ?> minutes
                                    </td>

                                    <td>

                                        <?php
                                        $statusClass = strtolower(
                                            str_replace(
                                                ' ',
                                                '-',
                                                $appointment['status']
                                            )
                                        );
                                        ?>

                                        <span class="status-badge status-<?= patientEscape($statusClass) ?>">

                                            <?= patientEscape($appointment['status']) ?>

                                        </span>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


            <?php else: ?>

                <div class="empty-appointments">

                    <h3>No Appointments Yet</h3>

                    <p>
                        You have not booked any appointments.
                    </p>

                    <p>
                        Choose a doctor below to book your first appointment.
                    </p>

                </div>

            <?php endif; ?>

        </section>


        <!-- Active Doctors Section -->

        <section class="doctors-section">

            <div class="section-heading">

                <h2>Available Doctors</h2>

                <p>
                    Explore our currently active doctors and their specializations.
                </p>

            </div>


            <?php if (!empty($doctors)): ?>

                <div class="doctor-grid">

                    <?php foreach ($doctors as $doctor): ?>

                        <div class="doctor-card">

                            <div class="doctor-icon">
                                +
                            </div>

                            <h3>
                                Dr.
                                <?= patientEscape($doctor['first_name']) ?>
                                <?= patientEscape($doctor['last_name']) ?>
                            </h3>

                            <div class="specialization">
                                <?= patientEscape($doctor['specialization']) ?>
                            </div>

                            <div class="doctor-details">

                                <p>
                                    <strong>Department:</strong>
                                    <?= patientEscape($doctor['department_name']) ?>
                                </p>

                                <p>
                                    <strong>Qualification:</strong>
                                    <?= patientEscape($doctor['qualification']) ?>
                                </p>

                                <p>
                                    <strong>Experience:</strong>
                                    <?= patientEscape($doctor['experience']) ?>
                                    years
                                </p>

                                <span class="active-status">
                                    Active Doctor
                                </span>

                            </div>


                            <a
                                href="/patient/appointments/create?doctor_id=<?= (int)$doctor['id'] ?>"
                                class="book-btn"
                            >
                                Book Appointment
                            </a>

                        </div>

                    <?php endforeach; ?>

                </div>


            <?php else: ?>

                <div class="empty-message">

                    <h3>No Active Doctors Available</h3>

                    <p>
                        Currently, there are no active doctors available.
                        Please check again later.
                    </p>

                </div>

            <?php endif; ?>

        </section>

    </div>

</body>

</html>
