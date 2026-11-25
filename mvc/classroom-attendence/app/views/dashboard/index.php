
<?php
$user = $user ?? [];
$totalStudents = $totalStudents ?? 0;
$totalTeachers = $totalTeachers ?? 0;
$todayPresent = $todayPresent ?? 0;
$todayAbsent = $todayAbsent ?? 0;
$recentAttendance = $recentAttendance ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Classroom Attendance</title>

    <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body>

<div class="dashboard-layout">

    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">CA</div>
            <h2>Classroom Attendance</h2>
        </div>

        <nav class="sidebar-nav">
            <a href="/dashboard" class="active">Dashboard</a>
            <a href="/students">Students</a>
            <a href="/teachers">Teachers</a>
            <a href="/attendance">Attendance</a>
            <a href="/attendance/reports">Reports</a>
        </nav>

        <div class="sidebar-bottom">
            <a href="/logout" class="logout-link">Logout</a>
        </div>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div>
                <h1>Dashboard</h1>
                <p>Classroom Attendance Management System</p>
            </div>

            <div class="user-info">
                <span>
                    Welcome, 
                    <strong>
                        <?= htmlspecialchars($user['username'] ?? 'User') ?>
                    </strong>
                </span>
            </div>
        </header>

        <section class="welcome-section">
            <h2>Welcome Back!</h2>
            <p>Here is your classroom attendance overview.</p>
        </section>

        <section class="summary-cards">

            <div class="summary-card students-card">
                <h3>Total Students</h3>
                <p><?= htmlspecialchars((string)$totalStudents) ?></p>
            </div>

            <div class="summary-card teachers-card">
                <h3>Total Teachers</h3>
                <p><?= htmlspecialchars((string)$totalTeachers) ?></p>
            </div>

            <div class="summary-card present-card">
                <h3>Present Today</h3>
                <p><?= htmlspecialchars((string)$todayPresent) ?></p>
            </div>

            <div class="summary-card absent-card">
                <h3>Absent Today</h3>
                <p><?= htmlspecialchars((string)$todayAbsent) ?></p>
            </div>

        </section>

        <section class="recent-section">

            <div class="section-heading">
                <h2>Recent Attendance</h2>
                <a href="/attendance">View All</a>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Admission Number</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($recentAttendance)): ?>

                            <?php foreach ($recentAttendance as $record): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($record['name']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($record['admission_no']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($record['attendance_date']) ?>
                                    </td>

                                    <td>
                                        <span class="status <?= strtolower($record['status']) ?>">
                                            <?= htmlspecialchars($record['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="4" class="empty-message">
                                    No attendance records found.
                                </td>
                            </tr>

                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </section>

    </main>

</div>

<script src="/js/dashboard.js"></script>

</body>
</html>
