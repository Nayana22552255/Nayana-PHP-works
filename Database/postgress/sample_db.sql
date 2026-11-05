/*CREATE TABLE employees(employee_id SERIAL PRIMARY KEY,
employee_name VARCHAR(50),
department_id INT);*/

/*CREATE TABLE department(department_id INT PRIMARY KEY,
department_name VARCHAR(50));*/

--SELECT *FROM department;
--SELECT *FROM employees;

/*INSERT INTO employees(employee_name,department_id)
VALUES('Lotus',101),
('Cherry',102),
('Soba',103),
('Divya',NULL);*/

--DROP TABLE employees;

/*INSERT INTO department(department_id,department_name)
VALUES(101,'Frondend'),
(102,'Backend'),
(103,'MERN'),
(104,'FullStack'),
(105,'Management');*/


----INNER JOIN(Return records that have matching values in both tables)
/*SELECT  e.employee_id,
e.employee_name,
d.department_id,
d.department_name
FROM employees e
INNER JOIN department d
ON e.department_id= d.department_id;*/

----LEFT JOIN(returns all records from the left table and matching records from the right table.)
/*SELECT 
e.employee_id,
e.employee_name,
d.department_id,
d.department_name
FROM employees e
LEFT JOIN department d
ON e.department_id= d.department_id;*/

---RIGHT JOIN(returns all records from the left table and matching records from the right table.)
/*SELECT 
	e.employee_id,
	e.employee_name,
	d.department_id,
	d.department_name
	FROM employees e
	RIGHT JOIN department d
	ON e.department_id=d.department_id;*/

---FULL OUTER JOIN(returns matching and unmatched records from both tables)
/*SELECT e.employee_id,
e.employee_name,
d.department_id,
d.department_name
FROM employees e
FULL OUTER JOIN department d
ON e.department_id = d.department_id;*/

--CROSS JOIN(returns every possible combination of records from both tables.)
/*SELECT e.employee_name,
d.department_name
FROM employees e
CROSS JOIN department d;*/

--NATURAL JOIN(automatically joins two tables using columns that have the same column names.)
/*SELECT employee_id,
employee_name,
department_id,
department_name
FROM employees
NATURAL JOIN department;*/

	

