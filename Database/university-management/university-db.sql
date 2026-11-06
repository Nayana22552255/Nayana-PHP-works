#create database university_db;

use university_db;

/*create table department(department_id int primary key auto_increment,
department_name varchar(50) not null);*/

/*create  table students(student_id int primary key auto_increment,
student_name varchar(50)not null,
age int,
department_id int,
foreign key(department_id)references department(department_id));*/

#select * from university_db.department;
#select *from university_db.students;

/*create table courses(course_id int primary key auto_increment,
course_name varchar(50)not null,
department_id int,
foreign key(department_id)references department(department_id));*/

#select * from university_db.courses;

