/*CREATE TABLE categories(category_id SERIAL PRIMARY KEY,
category_name VARCHAR(50)UNIQUE NOT NULL);*/

--SELECT *FROM categories;

/*CREATE TABLE suppliers(supplier_id SERIAL PRIMARY KEY,
supplier_name VARCHAR(50)NOT NULL,
phone VARCHAR(15),
email VARCHAR(50),
address TEXT);*/

--SELECT *FROM suppliers;

/*CREATE TABLE products(product_id SERIAL PRIMARY KEY,
product_name VARCHAR(100)NOT NULL,
category_id INT REFERENCES categories(category_id),
supplier_id INT REFERENCES suppliers(supplier_id),
price DECIMAL(10,2)NOT NULL,
quantity INT DEFAULT 0,
minimum_stock INT DEFAULT 5);*/

--SELECT *FROM products;

/*CREATE TABLE stock_transactions(transaction_id SERIAL PRIMARY KEY,
product_id INT REFERENCES products(product_id),
transaction_type VARCHAR(10)
CHECK(transaction_type IN ('IN','OUT')),
quantity INT NOT NULL CHECK(quantity > 0),
transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP);*/

--SELECT *FROM stock_transactions;


