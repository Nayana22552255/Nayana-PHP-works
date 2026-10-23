#create database employee_db;

use employee_db;

/*create table employees(
id int primary key auto_increment,
name varchar(20)not null,
email varchar(20)unique not null,
department varchar(20)not null,
salary decimal(10,2)not null);*/

/*create table department(
department_id int primary key  auto_increment,
department_name varchar(50)not null);*/

#alter table employees drop department;

#SELECT * FROM employee_db.employees;
#SELECT * FROM employee_db.department;

#describe employees;
#describe department;

/*alter table employees add department_id int null;*/

#alter table employees add column department varchar(20)not null;

#show table status;

/*SET SQL_SAFE_UPDATES = 0;
update employees AS e
join department AS d
on trim(e.department) = trim(d.department_name)
set e.department_id = d.department_id;
set SQL_SAFE_UPDATES = 1;*/

#SHOW CREATE TABLE employees;

/*alter table employees
add constraint fk_employee_department
foreign key (department_id)
references department(department_id);*/

/*create table attendance(
attendance_id int primary key auto_increment,
employee_id int not null,
attendance_date date not null,
status enum('present','absent','leave')not null,
check_in time null,
check_out time null);*/

#drop table attendance;

/*create table attendance(
attendance_id int primary key auto_increment,
employee_id int not null,
attendance_date date not null,
status enum('present','absent','leave')not null,
check_in time null,
check_out time null,
foreign key(employee_id) references employees(id),
unique(employee_id,attendance_date));*/

SELECT * FROM employee_db.attendance;


