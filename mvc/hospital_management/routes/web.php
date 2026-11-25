
<?php

// Home page
$homeController = new HomeController();
$router->get('/', [$homeController, 'index']);

// Department Management

$departmentController = new DepartmentController();



//Doctor Management
$doctorController = new DoctorController();

//patient management
$patientController = new PatientController();

//appointment management
$appointmentController = new AppointmentController();

//billing mangemnt
$billingController = new BillingController();

// Prescription Management
$prescriptionController = new PrescriptionController();


// Nurse Management
$nurseController = new NurseController();

// Ward Management Routes

$wardController = new WardController();


// Authentication
$authController = new AuthController();

// Public Patient Registration
$router->get('/register', [$authController, 'register']);

$router->post('/register', [$authController, 'storeRegistration']);

$router->get('/login', [$authController, 'login']);

$router->post('/login', [$authController, 'authenticate']);

$router->post('/logout', [$authController, 'logout']);


// Dashboard
$dashboardController = new DashboardController();


$router->get(
    '/admin/dashboard',
    [$dashboardController, 'admin']
);

$router->get(
    '/doctor/dashboard',
    [$dashboardController, 'doctor']
);

$router->get(
    '/receptionist/dashboard',
    [$dashboardController, 'receptionist']
);

$router->get(
    '/nurse/dashboard',
    [$dashboardController, 'nurse']
);



// Display all departments
$router->get(
    '/departments',
    [$departmentController, 'index']
);


// Display add department form
$router->get(
    '/departments/create',
    [$departmentController, 'create']
);


// Save department
$router->post(
    '/departments/store',
    [$departmentController, 'store']
);


// Display edit department form
$router->get(
    '/departments/edit',
    [$departmentController, 'edit']
);


// Update department
$router->post(
    '/departments/update',
    [$departmentController, 'update']
);


// Delete department
$router->post(
    '/departments/delete',
    [$departmentController, 'delete']
);
$router->get('/patient/dashboard', [
    $dashboardController,
    'patient'
]);



// Display all doctors
$router->get(
    '/doctors',
    [$doctorController, 'index']
);


// Display registration form
$router->get(
    '/doctors/create',
    [$doctorController, 'create']
);


// Save doctor
$router->post(
    '/doctors/store',
    [$doctorController, 'store']
);


// Display edit form
$router->get(
    '/doctors/edit',
    [$doctorController, 'edit']
);

$router->get(
    '/doctors/reset-password',
    [$doctorController, 'showResetPassword']
);


// Update doctor
$router->post(
    '/doctors/update',
    [$doctorController, 'update']
);


// Activate / Deactivate doctor
$router->post(
    '/doctors/toggle-status',
    [$doctorController, 'toggleStatus']
);

$router->post(
    '/admin/doctors/{id}/reset-password',
    [$doctorController, 'resetPassword']
);



// Display patient list
$router->get(
    '/patients',
    [$patientController, 'index']
);


// Display patient registration form
$router->get(
    '/patients/create',
    [$patientController, 'create']
);


// Save patient
$router->post(
    '/patients/store',
    [$patientController, 'store']
);


// Display individual patient
$router->get(
    '/patients/show',
    [$patientController, 'show']
);


// Display edit patient form
$router->get(
    '/patients/edit',
    [$patientController, 'edit']
);


// Update patient
$router->post(
    '/patients/update',
    [$patientController, 'update']
);

// Activate / Deactivate patient
$router->post(
    '/patients/toggle-status',
    [$patientController, 'toggleStatus']
    
);

$router->get(
    '/doctor/patients',
    [$doctorController, 'myPatients']
);

$router->get(
    '/doctor/patients/show',
    [$doctorController, 'patientDetails']
);




// Appointment Management Routes
$router->get(
    '/doctor/schedule',
    [$appointmentController, 'doctorSchedule']
);

$router->post(
    '/doctor/appointments/complete',
    [$appointmentController, 'completeAppointment']
);


$router->get(
    '/appointments',
     [$appointmentController, 'index']
);


$router->get(
    '/appointments/create',
     [$appointmentController, 'create']
);


$router->post(
    '/appointments/store', 
    [$appointmentController, 'store']
);

$router->get(
    '/appointments/show', 
    [$appointmentController, 'show']
);

$router->get(
    '/appointments/edit', 
    [$appointmentController, 'edit']
);

$router->post(
    '/appointments/update',
     [$appointmentController, 'update']
);

$router->post(
    '/appointments/update-status',
     [$appointmentController, 'updateStatus']
);

// Patient Appointment Booking Routes

$router->get(
    '/patient/appointments/create',
    [$appointmentController, 'patientCreate']
);

$router->post(
    '/patient/appointments/store',
    [$appointmentController, 'patientStore']
);



// Billing Management Routes

$router->get(
    '/billing', 
    [$billingController, 'index']
);


$router->get(
    '/billing/create', 
    [$billingController, 'create']
);

$router->post(
    '/billing/store', 
    [$billingController, 'store']
);

$router->get(
    '/billing/show',
     [$billingController, 'show']
);

$router->post(
    '/billing/payment', 
    [$billingController, 'storePayment']
);



$router->get('/prescriptions', [$prescriptionController, 'index']);

$router->get('/prescriptions/create', [$prescriptionController, 'create']);

$router->post('/prescriptions/store', [$prescriptionController, 'store']);

$router->get('/prescriptions/show', [$prescriptionController, 'show']);

$router->post('/prescriptions/update-status', [$prescriptionController, 'updateStatus']);

$router->get('/doctor/prescriptions',[$prescriptionController, 'doctorPrescriptions']);

$router->get('/doctor/prescriptions/show',[$prescriptionController, 'doctorPrescriptionDetails']);




$router->get('/nurses', [$nurseController, 'index']);

$router->get('/nurses/create', [$nurseController, 'create']);

$router->post('/nurses/store', [$nurseController, 'store']);

$router->get('/nurses/show', [$nurseController, 'show']);

$router->get('/nurses/edit', [$nurseController, 'edit']);

$router->post('/nurses/update', [$nurseController, 'update']);

$router->post('/nurses/update-status', [$nurseController, 'updateStatus']);



$router->get('/wards', [$wardController, 'index']);

$router->get('/wards/create', [$wardController, 'create']);

$router->post('/wards/store', [$wardController, 'store']);

$router->get('/wards/show', [$wardController, 'show']);

$router->get('/wards/edit', [$wardController, 'edit']);

$router->post('/wards/update', [$wardController, 'update']);

$router->post('/wards/update-status', [$wardController, 'updateStatus']);