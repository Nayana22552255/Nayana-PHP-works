/*CREATE TABLE students(student_id SERIAL PRIMARY KEY,
student_name VARCHAR(50),
course_id INT);*/

/*CREATE TABLE courses(course_id SERIAL PRIMARY KEY,
course_name VARCHAR(50));*/

--SELECT * FROM courses;

--ALTER TABLE students ADD COLUMN course_name VARCHAR(50);

--ALTER TABLE students ADD COLUMN address VARCHAR(50);

--SELECT * FROM students;

--ALTER TABLE courses ADD COLUMN duration VARCHAR(50);

/*ALTER TABLE students
ADD COLUMN email VARCHAR(100)UNIQUE,
ADD COLUMN phone VARCHAR(15),
ADD COLUMN date_of_birth DATE,
ADD COLUMN gender VARCHAR(10),
ADD COLUMN admission_date DATE DEFAULT CURRENT_DATE,
ADD COLUMN status VARCHAR(20)DEFAULT 'Active',
ADD COLUMN registration_number VARCHAR(20)UNIQUE;*/

/*ALTER TABLE courses
ADD COLUMN course_code VARCHAR(20)UNIQUE,
ADD COLUMN course_fee NUMERIC(10,2)DEFAULT 0,
ADD COLUMN start_date DATE,
ADD COLUMN end_date DATE,
ADD COLUMN description TEXT,
ADD COLUMN course_status VARCHAR(20)DEFAULT 'Active';*/

/*CREATE TABLE attendance(
attendance_id SERIAL PRIMARY KEY,
student_id INT  REFERENCES students(student_id),
attendance_date DATE DEFAULT CURRENT_DATE,
attendance_status VARCHAR(20)
);*/

--SELECT *FROM attendance;

/*CREATE TABLE marks(
mark_id SERIAL PRIMARY KEY,
student_id INT REFERENCES students(student_id),
subject_name VARCHAR(50),
exam_type VARCHAR(30),
total_marks INT,
obtained_marks INT
);*/

--SELECT *FROM marks;

/*CREATE TABLE fees(
fee_id SERIAL PRIMARY KEY,
students_id INT REFERENCES students(student_id),
total_fee NUMERIC(10,2),
paid_amount NUMERIC(10,2)DEFAULT 0,
payment_date DATE,
payment_status VARCHAR(20)
);*/

--SELECT *FROM fees;

/*CREATE TABLE departments(
department_id SERIAL PRIMARY KEY,
department_name VARCHAR(100),
deparment_code VARCHAR(20)UNIQUE
);*/

--SELECT *FROM departments;

--ALTER TABLE students DROP COLUMN course_name;

/*ALTER TABLE students
ADD CONSTRAINT fk_student_course
FOREIGN KEY (course_id)
REFERENCES courses(course_id);*/


--one student can have only one attendance record per day 
/*ALTER TABLE attendance
ADD CONSTRAINT unique_student_attendance_date
UNIQUE (student_id, attendance_date);*/

--ALTER TABLE fees RENAME COLUMN students_id to student_id;

--ALTER TABLE departments ADD COLUMN department_code VARCHAR(10) UNIQUE;

--ALTER TABLE  departments DROP COLUMN deparment_code;
