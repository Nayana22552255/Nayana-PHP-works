# MySQL vs PostgreSQL
### Introduction

MySQL and PostgreSQL are popular open-source relational database management system(RDBMS).They are used to store ,organize,manage,and retrieve data using SQL.

### What is MySQL?

MySQL is an open-sourece relational database management system used to store and manage structural data. It is widely used in web development,particularly with php applications.

### Main Features:

- Easy to learn and use
- Supports primary keys and foreign keys
- Support JOIN operations
- Support transaction using InnoDB
- Support JSON data
- Widely used in web applications 

### What is PostgreSQL

PostgreSQL is an open-source object-relational database management system. It provides advanced SQL features,flexible data types and extensibility.

### Main Features:

- Support advanced SQL queries.
- Support Array and JSONB data types.
- Support UUID and ENUM.
- Support custom data types
- Support foreign keys and transactions
- Support FULL OUTER JOIN
- Provides advanced indexing options



### Main difference between MySQL and PostgreSQL

|Features|MySQL|PostgreSQL|
|---|---|---|
|Database type|RDBMS|Object-relational DBMS|
|SQL Support|Standard SQL Features|Extensive SQL features|
|JSON|JSON|JSON and JSONNB|
|ARRAY|No native SQL array type|Array Supported|
|UUID|Supported|Native UUID type|
|ENUM|Supported|Supported|
|Foreign Keys|Supported|Supported|
|FULL OUTER JOIN|Not directly Supported|Supported|
|Custom types|More limited|Extensive Support|
|Extensibility|More Limited|Highly extensible|

### Advanced PostgreSQL Data Types

1. Array

        Array stores multiple values of the same data type in single column

        Example

        CREATE TABLE students(id int skills TEXT [] );

2. JSONB

        JSONB stores JSON data in a binary representation and supports efficient querying and indexing.

        Example

        CREATE TABLE users(id INT,details JSONB);

3. UUID

        UUID stands for University Unique identifier.It is used to generate unique identifiers.

        Example:

        CREATE TABLE users(id UUID PRIMARY KEY, name VARCHAR(50));

4. ENUM

        ENUM allows a column to accept only predefined values.

        Example:

        CREATE TYPE status_type AS ENUM('Active','Inactive','Resigned');

5. Custom Data Types

        Custom data types allow developers to define their own data structures.

        Example:

        CREATE TYPE address_type AS(city VARCHAR(50),pin_code VARCHAR(10));

6. Range Types

        Range type store a range of values between two boundaries

        Example:

        CEATE TABLE bookings(id INT, booking_period DATARANGE);


### JOIN Operations

JOIN operations are used to retrieve related information from multiple table using common columns

|JOINS|Descriptions|
|---|---|
|INNER JOIN|Returns matching records from both tables|
|LEFT JOIN|Return all records from the left table and matching records from the right table|
|RIGHT JOIN|Returns all records from the right table and matching records from the left table|
|FULL OUTER JOIN|Returns all matching and unmatched records from both tables|
|CROSS JOIN|Returns every possible combination of records|
|SELF JOIN|Joins a table with itself|



### SQL Syntax Differences

|Feature|MySQL|PostgreSQL|
|---|---|---|
|Auto increment|id INT AUTO_INCREMENT PRIMARY KEY|id INT GENERATED ALWAYS AS IDENTITY PRIMARY KEY /  id SERIAL PRIMARY KEY|
|Current Date|SELECT CURDATE();|SELECT CURRENT_DATE;|
|Database Switching|USE college_db;|\c college_db;|


### Advantages and Disadvantages using MySQL

|Advantages|Disadvantages|
|---|---|
|Easy to learn|Limited advanced data types|
|Open source|No direct FULL OUTER JOIN|
|Good performance for many web workloads|Some complex queries are harder to write|
|Excellent PHP intergration|Less extensive custom type support|
|Transaction support|Fewer advanced extensibility features|
|Large community support|JSON capabilities differ from PostgreSQL|


### Advantages and Disadvantages using PostgreSQL

|Advantages|Disadvantages|
|---|---|
|Free and open source|Can be difficult for beginners|
|Advanced SQL features|Requires proper configurations|
|Support many advanced data types|Administrations can be complex|
|Strong data integrity|Some workloads need careful tuning|
|Support FULL OUTER JOIN|Migration may requires code changes|
|JSONB Support|Some tools have limited compatibility|


### Applications

#### MySQL

- E-commerce websites
- Content mangement systems
- PHP web applications
- CRUD applications


#### PostgreSQL

- Complex database applications
- Data analytics
- Applications using JSONB
- Systems requiring advanced SQL features




