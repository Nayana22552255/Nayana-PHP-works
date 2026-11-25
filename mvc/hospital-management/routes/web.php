<?php

$routes = [
    'GET /'=>[
        'controller'=>'DashboardController',
        'method'=>'index'
    ],

    'GET /patients'=>[
        'controller'=>'PatientController',
        'method'=> 'index'
    ],

    'GET /patients/create'=>[
        'controller'=>'PatientController',
        'method'=> 'create'
    ],
    'GET /doctors'=>[
        'controller'=>'DoctorController',
        'method'=>'index'
    ],

    'GET /appointments' => [
        'controller'=>'AppointmentController',
        'method'=>'index'
    ],

    'GET /prescription'=>[
        'controller'=> 'PrescriptionController',
        'method'=>'index'
    ],
    'GET /billing'=>[
        'controller'=>'BillingController',
        'method'=>'index'
    ],
    'POST /patients/store'=>[
        'controller'=>'PatientController',
        'method'=>'store'
    ],



    'GET /doctors/create'=>[
        'controller'=>'DoctorController',
        'method'=>'create'
    ],
    'POST /doctors/store'=>[
        'controller'=>'DoctorController',
        'method'=>'store'
    ],
    'GET /doctors/show'=>[
        'controller'=>'DoctorController',
        'method'=>'show'
    ],
    'GET /doctors/view'=>[
        'controller'=>'DoctorController',
        'method'=>'show'
    ],
    'GET /doctors/edit'=>[
        'controller'=>'DoctorController',
        'method'=>'edit'
    ],

    'POST /doctors/update'=>[
        'controller'=>'DoctorController',
        'method'=>'update'
    ],

    'POST /doctors/delete'=>[
        'controller'=>'DoctorController',
        'method'=>'delete'
    ],
    'GET /doctors/search'=>[
        'controller'=>'DoctorController',
        'method'=>'search'
    ],


    'GET /appointments' => [
        'controller'=>'AppointmentController',
        'method'=>'index'
    ],
    'GET /appointments/create'=>[
            'controller'=>'AppointmentController',
            'method'=>'create'
    ],
    'POST /appointments/store'=>[
        'controller'=>'AppointmentController',
        'method'=>'store'
    ],
    'GET /appointments/show'=>[
        'controller'=>'AppointmentController',
        'method'=>'show'
    ],
    'GET /appointments/edit'=>[
        'controller'=>'AppointmentController',
        'method'=>'edit'
    ],
      'POST /appointments/update'=>[
        'controller'=>'AppointmentController',
        'method'=>'update'
    ],
      'POST /appointments/delete'=>[
        'controller'=>'AppointmentController',
        'method'=>'delete'
    ],
      'GET /appointments/search'=>[
        'controller'=>'AppointmentController',
        'method'=>'search'
    ],




            'GET /prescriptions' => [
            'controller' => 'PrescriptionController',
            'method' => 'index'
        ],

        'GET /prescriptions/create' => [
            'controller' => 'PrescriptionController',
            'method' => 'create'
        ],

        'POST /prescriptions/store' => [
            'controller' => 'PrescriptionController',
            'method' => 'store'
        ],

        'GET /prescriptions/show' => [
            'controller' => 'PrescriptionController',
            'method' => 'show'
        ],

        'GET /prescriptions/edit' => [
            'controller' => 'PrescriptionController',
            'method' => 'edit'
        ],

        'POST /prescriptions/update' => [
            'controller' => 'PrescriptionController',
            'method' => 'update'
        ],

        'POST /prescriptions/delete' => [
            'controller' => 'PrescriptionController',
            'method' => 'delete'
        ],

        'GET /prescriptions/search' => [
            'controller' => 'PrescriptionController',
            'method' => 'search'
        ],




        'GET /bills' => [
            'controller' => 'BillingController',
            'method' => 'index'
        ],
        'GET /bills/create' => [
            'controller' => 'BillingController',
            'method' => 'create'
        ],
        'POST /bills/store' => [
            'controller' => 'BillingController',
            'method' => 'store'
        ],
        'GET /bills/show' => [
            'controller' => 'BillingController',
            'method' => 'show'
        ],
        'GET /bills/edit' => [
            'controller' => 'BillingController',
            'method' => 'edit'
        ],
        'POST /bills/update' => [
            'controller' => 'BillingController',
            'method' => 'update'
        ],
        'POST /bills/payment' => [
            'controller' => 'BillingController',
            'method' => 'payment'
        ],
        'POST /bills/delete' => [
            'controller' => 'BillingController',
            'method' => 'delete'
        ],
        'GET /bills/search' => [
            'controller' => 'BillingController',
            'method' => 'search'
        ],




];
?>