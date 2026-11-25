/*CREATE DATABASE IF NOT EXISTS hospital_management
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;*/

USE hospital_management;


/*CREATE TABLE users (

    id INT AUTO_INCREMENT PRIMARY KEY,

    username VARCHAR(100) NOT NULL UNIQUE,

    email VARCHAR(150) NOT NULL UNIQUE,

    password VARCHAR(255) NOT NULL,

    role ENUM(
        'admin',
        'doctor',
        'receptionist',
        'nurse'
    ) NOT NULL,

    status TINYINT(1) NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;*/
  
  
  
/*CREATE TABLE departments (

    id INT AUTO_INCREMENT PRIMARY KEY,

    department_name VARCHAR(100) NOT NULL UNIQUE,

    description TEXT,

    status TINYINT(1) NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;*/
  
  
/*INSERT INTO departments
(department_name, description, status)
VALUES
('Cardiology', 'Heart and cardiovascular treatment', 1),
('Neurology', 'Brain and nervous system treatment', 1),
('Orthopedics', 'Bone and joint treatment', 1),
('Pediatrics', 'Medical care for children', 1),
('Dermatology', 'Skin and hair treatment', 1);*/



/*CREATE TABLE doctors (

    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL UNIQUE,

    department_id INT NOT NULL,

    first_name VARCHAR(100) NOT NULL,

    last_name VARCHAR(100) NOT NULL,

    gender ENUM(
        'Male',
        'Female',
        'Other'
    ) NOT NULL,

    date_of_birth DATE NOT NULL,

    phone VARCHAR(20) NOT NULL,

    address TEXT,

    qualification VARCHAR(150) NOT NULL,

    specialization VARCHAR(150) NOT NULL,

    experience INT NOT NULL DEFAULT 0,

    registration_number VARCHAR(100) NOT NULL UNIQUE,

    status TINYINT(1) NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_doctor_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_doctor_department
        FOREIGN KEY (department_id)
        REFERENCES departments(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;*/
  
 -- describe doctors;
 
 
 
/*CREATE TABLE patients (

    id INT AUTO_INCREMENT PRIMARY KEY,

    patient_number VARCHAR(30) NOT NULL UNIQUE,

    first_name VARCHAR(100) NOT NULL,

    last_name VARCHAR(100) NOT NULL,

    gender ENUM(
        'Male',
        'Female',
        'Other'
    ) NOT NULL,

    date_of_birth DATE NOT NULL,

    blood_group ENUM(
        'A+',
        'A-',
        'B+',
        'B-',
        'AB+',
        'AB-',
        'O+',
        'O-',
        'Unknown'
    ) NOT NULL DEFAULT 'Unknown',

    phone VARCHAR(20) NOT NULL,

    email VARCHAR(150) DEFAULT NULL,

    address TEXT,

    emergency_contact_name VARCHAR(150),

    emergency_contact_phone VARCHAR(20),

    status TINYINT(1) NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_patient_name (
        first_name,
        last_name
    ),

    INDEX idx_patient_phone (phone),

    INDEX idx_patient_status (status)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;*/
  
  
  
/*CREATE TABLE appointments (

    id INT AUTO_INCREMENT PRIMARY KEY,

    appointment_number VARCHAR(30) NOT NULL UNIQUE,

    patient_id INT NOT NULL,

    doctor_id INT NOT NULL,

    appointment_date DATE NOT NULL,

    appointment_time TIME NOT NULL,

    duration_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 30,

    reason TEXT,

    status ENUM(
        'Scheduled',
        'Confirmed',
        'Completed',
        'Cancelled',
        'No-show'
    ) NOT NULL DEFAULT 'Scheduled',

    notes TEXT,

    created_by INT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_appointment_patient (patient_id),

    INDEX idx_appointment_doctor_date (
        doctor_id,
        appointment_date
    ),

    INDEX idx_appointment_status (status),

    CONSTRAINT fk_appointment_patient
        FOREIGN KEY (patient_id)
        REFERENCES patients(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_appointment_doctor
        FOREIGN KEY (doctor_id)
        REFERENCES doctors(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_appointment_user
        FOREIGN KEY (created_by)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL

) ENGINE=InnoDB;*/

-- describe appointments;


/*CREATE TABLE bills (
    id INT AUTO_INCREMENT PRIMARY KEY,

    bill_number VARCHAR(30) NOT NULL UNIQUE,

    patient_id INT NOT NULL,
    appointment_id INT NULL,

    bill_date DATE NOT NULL,

    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    tax DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    paid_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    balance_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    payment_status ENUM(
        'Pending',
        'Partially Paid',
        'Paid'
    ) NOT NULL DEFAULT 'Pending',

    notes TEXT,

    created_by INT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_bill_patient (patient_id),
    INDEX idx_bill_date (bill_date),
    INDEX idx_bill_status (payment_status),

    CONSTRAINT fk_bill_patient
        FOREIGN KEY (patient_id)
        REFERENCES patients(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_bill_appointment
        FOREIGN KEY (appointment_id)
        REFERENCES appointments(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_bill_user
        FOREIGN KEY (created_by)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL

) ENGINE=InnoDB;*/


/*CREATE TABLE bill_items (
    id INT AUTO_INCREMENT PRIMARY KEY,

    bill_id INT NOT NULL,

    item_name VARCHAR(150) NOT NULL,
    description TEXT,

    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_bill_item (bill_id),

    CONSTRAINT fk_bill_item_bill
        FOREIGN KEY (bill_id)
        REFERENCES bills(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE

) ENGINE=InnoDB;*/


/*CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,

    bill_id INT NOT NULL,

    payment_number VARCHAR(30) NOT NULL UNIQUE,

    amount DECIMAL(10,2) NOT NULL,

    payment_method ENUM(
        'Cash',
        'Card',
        'UPI',
        'Bank Transfer'
    ) NOT NULL,

    payment_date DATETIME NOT NULL,

    transaction_reference VARCHAR(100) NULL,

    notes TEXT,

    received_by INT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_payment_bill (bill_id),
    INDEX idx_payment_date (payment_date),

    CONSTRAINT fk_payment_bill
        FOREIGN KEY (bill_id)
        REFERENCES bills(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_payment_user
        FOREIGN KEY (received_by)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL

) ENGINE=InnoDB;*/


/*CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,

    prescription_number VARCHAR(30) NOT NULL UNIQUE,

    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_id INT NULL,

    prescription_date DATE NOT NULL,

    diagnosis TEXT,
    notes TEXT,

    status ENUM(
        'Active',
        'Completed',
        'Cancelled'
    ) NOT NULL DEFAULT 'Active',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_prescription_patient (patient_id),
    INDEX idx_prescription_doctor (doctor_id),
    INDEX idx_prescription_date (prescription_date),

    CONSTRAINT fk_prescription_patient
        FOREIGN KEY (patient_id)
        REFERENCES patients(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_prescription_doctor
        FOREIGN KEY (doctor_id)
        REFERENCES doctors(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_prescription_appointment
        FOREIGN KEY (appointment_id)
        REFERENCES appointments(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL

) ENGINE=InnoDB;*/


/*CREATE TABLE prescription_items (
    id INT AUTO_INCREMENT PRIMARY KEY,

    prescription_id INT NOT NULL,

    medicine_name VARCHAR(150) NOT NULL,

    dosage VARCHAR(100) NOT NULL,

    frequency VARCHAR(100) NOT NULL,

    duration VARCHAR(100) NOT NULL,

    quantity INT UNSIGNED NOT NULL DEFAULT 1,

    instructions TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_prescription_item (prescription_id),

    CONSTRAINT fk_prescription_item
        FOREIGN KEY (prescription_id)
        REFERENCES prescriptions(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE

) ENGINE=InnoDB;*/

/*SELECT id, username, email, role, status
FROM users;*/


/*CREATE TABLE nurses (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nurse_number VARCHAR(30) NOT NULL UNIQUE,

    user_id INT NOT NULL UNIQUE,

    department_id INT NOT NULL,

    first_name VARCHAR(100) NOT NULL,

    last_name VARCHAR(100) NOT NULL,

    gender VARCHAR(20),

    qualification VARCHAR(150),

    phone VARCHAR(20),

    email VARCHAR(150),

    experience_years DECIMAL(4,1) DEFAULT 0,

    shift VARCHAR(50),

    address TEXT,

    status TINYINT(1) DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_nurse_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_nurse_department
        FOREIGN KEY (department_id)
        REFERENCES departments(id)
        ON DELETE RESTRICT

);*/

-- describe nurses;


/*CREATE TABLE wards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ward_name VARCHAR(100) NOT NULL,
    ward_code VARCHAR(30) NOT NULL UNIQUE,
    ward_type VARCHAR(50) NOT NULL,
    description TEXT,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;*/

/*CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ward_id INT NOT NULL,
    room_number VARCHAR(30) NOT NULL,
    room_type VARCHAR(50),
    daily_charge DECIMAL(10,2) DEFAULT 0.00,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_room (ward_id, room_number),

    CONSTRAINT fk_room_ward
        FOREIGN KEY (ward_id)
        REFERENCES wards(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;*/

/*CREATE TABLE beds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    bed_number VARCHAR(30) NOT NULL,
    bed_status ENUM(
        'Available',
        'Occupied',
        'Maintenance'
    ) DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_bed (room_id, bed_number),

    CONSTRAINT fk_bed_room
        FOREIGN KEY (room_id)
        REFERENCES rooms(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;*/

/*CREATE TABLE admissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admission_number VARCHAR(30) NOT NULL UNIQUE,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    bed_id INT NOT NULL,
    admission_date DATETIME NOT NULL,
    discharge_date DATETIME DEFAULT NULL,
    admission_reason TEXT,
    diagnosis TEXT,
    status ENUM(
        'Admitted',
        'Discharged',
        'Cancelled'
    ) DEFAULT 'Admitted',
    admitted_by INT NOT NULL,
    discharged_by INT DEFAULT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_admission_patient (patient_id),
    INDEX idx_admission_doctor (doctor_id),
    INDEX idx_admission_bed (bed_id),
    INDEX idx_admission_date (admission_date),

    CONSTRAINT fk_admission_patient
        FOREIGN KEY (patient_id)
        REFERENCES patients(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_admission_doctor
        FOREIGN KEY (doctor_id)
        REFERENCES doctors(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_admission_bed
        FOREIGN KEY (bed_id)
        REFERENCES beds(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_admission_user
        FOREIGN KEY (admitted_by)
        REFERENCES users(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_discharge_user
        FOREIGN KEY (discharged_by)
        REFERENCES users(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;*/

/*CREATE TABLE bed_transfers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admission_id INT NOT NULL,
    from_bed_id INT NOT NULL,
    to_bed_id INT NOT NULL,
    transfer_date DATETIME NOT NULL,
    reason TEXT,
    transferred_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_transfer_admission (admission_id),

    CONSTRAINT fk_transfer_admission
        FOREIGN KEY (admission_id)
        REFERENCES admissions(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_transfer_from_bed
        FOREIGN KEY (from_bed_id)
        REFERENCES beds(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_transfer_to_bed
        FOREIGN KEY (to_bed_id)
        REFERENCES beds(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_transfer_user
        FOREIGN KEY (transferred_by)
        REFERENCES users(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;*/

-- SHOW COLUMNS FROM patients;
-- SHOW COLUMNS FROM users;



/*ALTER TABLE patients
ADD COLUMN user_id INT NULL UNIQUE AFTER id,
ADD CONSTRAINT fk_patients_user
FOREIGN KEY (user_id)
REFERENCES users(id)
ON DELETE SET NULL;*/

/*ALTER TABLE users
MODIFY COLUMN role
ENUM('admin', 'doctor', 'receptionist', 'nurse', 'patient')
NOT NULL;*/


-- SHOW COLUMNS FROM users LIKE 'role';






