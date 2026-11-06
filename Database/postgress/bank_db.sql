#create database bank_db;

use bank_db;
 
/*create table customer(customer_id int primary key auto_increment,
name varchar(20)not null,
age int not null,
city varchar(20)not null,
phone varchar(15)not null);*/

/*insert into customer(name,age,city,phone)values
('Nayana',22,'Kannur','8089556511'),
('Anu',22,'Iritty','8055654705'),
('Gopika',24,'Vadakara','7589640123'),
('Nandana',23,'Payyoli','6547890214');*/
 
#select *from bank_db.customer; 

/*create table accounts(account_id int primary key auto_increment,
customer_id int,
account_type varchar(20)not null,
balance decimal(12,2)default 0.00,
status varchar(20)default 'active',
foreign key(customer_id) references customer(customer_id));*/

/*insert into accounts(customer_id,account_type,balance,status)values
(1,'Savings',2500.00,'Active'),
(2,'Savings',3500.00,'Active'),
(3,'Current',1500.00,'Active'),
(4,'Savings',2500.00,'Inactive');*/

#select *from bank_db.accounts;

/*create table transaction(transaction_id int  primary key not null,
account_id int,
transaction_type varchar(20),
amount decimal(12.2)not null,
transation_date date,
foreign key(account_id)references accounts(account_id));*/

/*insert into transaction(account_id,transaction_type,amount,transation_date)values
(1,'Deposit',50000.00,'2026-10-23'),
(1,'Withdrawal',25000.00,'2026-10-23'),
(2,'Deposit',100000.00,'2026-10-23'),
(3,'Withdrawal',2500.00,'2026-10-23');*/

#alter table transaction modify transaction_id int auto_increment;

#select *from bank_db.transaction;

#alter table customer rename column city to location;

#select *from customer where age between 22 and 23;   #customer whose age between 22 and 23 

#select distinct balance from accounts order by balance desc; 	#arrange the balance in descending order and remove duplicates

#display the total accounts belongs to each cutomer
/*select c.customer_id,c.name,			
count(a.account_id)as total_accounts
from customer c left join accounts a
on c.customer_id=a.customer_id 
group by c.customer_id,c.name;*/

#find the accounts whose balance is greater than 2500 and stsus is active
#select *from accounts where balance >2500 and status ='active';

/*insert into accounts(customer_id,account_type,balance,status)values
(2,'current',4500.00,'Active'),
(4,'current',5555.00,'Inactive');*/

#display customer who have more than one account
/*select c.customer_id,c.name,
count(a.account_id)as total_accounts
from customer c
inner join accounts a
on c.customer_id=a.customer_id
group by c.customer_id,c.name
having count(a.account_id)>1;*/

#total amount of deposite in all transaction
#select sum(amount) as total_deposite from transaction where transaction_type='Deposit';

#adding 1000 to alla the accounts
/*select account_id,balance,
balance + 1000 as new_balance
from accounts;*/

#calculate a 5% intrest amount
/*select account_id,balance,
balance * 0.05 as intrest
from accounts;*/

#calculate the balance after adding a 5% of intrest
/*select account_id,balance,
balance * 0.05 as intrest,
balance *1.05 as balance_with_intrest
from accounts;*/



