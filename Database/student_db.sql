/*CREATE DATABASE student_db;*/

USE student_db;

/*CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);*/

#alter table students add column phone varchar(15)not null;
#truncate students;

#alter table students add column age int;
alter table students add column course varchar(20)not null;
