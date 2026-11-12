#create database university_db;

use university_db;

/*create table department(department_id int primary key auto_increment,
department_name varchar(50) not null);*/

-- alter table department add column college_id int;

/*alter table department add constraint fk_department_college
foreign key(college_id)references colleges(college_id);*/

/*create  table students(student_id int primary key auto_increment,
student_name varchar(50)not null,
age int,
department_id int,
foreign key(department_id)references department(department_id));*/

-- select * from university_db.department;
#select *from university_db.students;

/*create table courses(course_id int primary key auto_increment,
course_name varchar(50)not null,
department_id int,
foreign key(department_id)references department(department_id));*/

-- alter table courses add column college_id int;

/*alter table courses add constraint fk_courses_college
foreign key(college_id)
references colleges(college_id);*/

-- select * from university_db.courses;

/*create table colleges(
college_id int primary key auto_increment,
college_name varchar(50)not null,
location varchar(50),
established_year varchar(10));*/

/*create table teachers(
teacher_id int auto_increment primary key,
teacher_name varchar(20)not null,
email varchar(20),
subject varchar(20),
college_id int,
foreign key(college_id)references colleges(college_id));*/
-- select * from university_db.colleges;

