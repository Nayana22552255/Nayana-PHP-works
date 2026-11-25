-- create database  hospital_db;
use hospital_db;
/*create table departments(department_id int primary key,
department_name varchar(100) not null,
description text);*/

-- alter table departments modify column department_id int auto_increment;

/*create table patients(patient_d int auto_increment primary key,
patient_name varchar(100) not null,
gender varchar(20),
date_of_birth date,
phone varchar(15),
email varchar(100),
address text,
created_at timestamp default current_timestamp);*/

-- alter table patients rename column patient_d to patient_id;

/*create table doctors(doctor_id int auto_increment primary key,
doctor_name varchar(100)not null,
specialization varchar(20),
phone varchar(20),
email varchar(20),
department_id int,

foreign key(department_id)references departments(department_id));*/

/*create table appointments(appointment_id int auto_increment primary key,
patient_id int not null,
doctor_id int not null,
appointment_date datetime not null,
reason text,
status varchar(30)default 'Scheduled',

foreign key(patient_id)references patients(patient_d),
foreign key(doctor_id)references doctors(doctor_id));*/

/*create table prescriptions(prescription_id int auto_increment primary key,
appointment_id int not null,
notes text,
created_at timestamp default current_timestamp,

foreign key (appointment_id) references appointments(appointment_id));*/

/*create table prescription_items(item_id int auto_increment primary key,
prescription_id int not null,
medicine_name varchar(100) not null,
dosage varchar(100),
frequency varchar(100),
duration varchar(100),
instructions text,

foreign key(prescription_id) references prescriptions(prescription_id));*/


/*create table bills(bill_id int auto_increment primary key,
appointment_id int not null,
total_amount decimal(10,2)not null default 0,
payment_status varchar(30) default 'Unpaid',
bill_date timestamp default current_timestamp,

foreign key(appointment_id) references appointments(appointment_id));*/

/*create table bill_items(bill_item int auto_increment primary key,
bill_id int not null,
description varchar(200),
quantity int default 1,
unit_price decimal(10,2) not null,

foreign key (bill_id) references bills(bill_id));*/

/*create table payments(payment_id int auto_increment primary key,
bill_id int not null,
amount decimal(10,2)not null,
payment_method varchar(50),
payment_date timestamp default current_timestamp,

foreign key(bill_id)references bills(bill_id))*/
