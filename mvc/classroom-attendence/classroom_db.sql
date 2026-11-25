/*CREATE TABLE users(
id SERIAL PRIMARY KEY,
username VARCHAR(100)UNIQUE NOT NULL,
password VARCHAR(255)NOT NULL,
role VARCHAR(20)NOT NULL CHECK(role IN ('admin','teacher','students')),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);*/

SELECT * FROM users;

/*CREATE TABLE studets(
id SERIAL PRIMARY KEY,
admission_no VARCHAR(50)UNIQUE NOT NULL,
name VARCHAR(150)NOT NULL,
gender VARCHAR(10) CHECK (gender IN('Male','female','other')),
phone VARCHAR(20),
address TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);*/

-- alter table studets rename to students;

-- SELECT *FROM students;

/*CREATE TABLE teachers(
id SERIAL PRIMARY KEY,
user_id INT UNIQUE REFERENCES users(id) ON DELETE SET NULL,
name VARCHAR(150)NOT NULL,
phone VARCHAR(20),
email VARCHAR(150),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);*/

-- SELECT * FROM teachers;

/*CREATE TABLE attendance(
id SERIAL PRIMARY KEY,
student_id INT NOT NULL REFERENCES students(id)ON DELETE CASCADE,
attendance_date DATE NOT NULL,
status VARCHAR(20)NOT NULL CHECK(status IN ('Present','Absent','Late')),
marked_by INT REFERENCES users(id)ON DELETE SET NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
UNIQUE(student_id,attendance_date)
);*/


-- SELECT * FROM attendance;
