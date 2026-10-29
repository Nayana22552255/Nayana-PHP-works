#create database library_db;

use library_db;

#created to store authors details
/*create table authors(author_id int primary key  auto_increment,
author_name varchar(20)not null,
biography text);*/

#created to store geners and parent geners
/*create table genres(genre_id int primary key auto_increment,
genre_name varchar(20)not null,
parent_genre_id int null,
foreign key(parent_genre_id)references genres(genre_id));*/

#created to store books infornmation
/*create table books(book_id int auto_increment primary key,
title varchar(50)not null,
publication_year year,
isbn varchar(20)unique,
physical_format varchar(50),
description text,
price decimal(10.2)not null default 0);*/

#alter table books add column available tinyint(1) not null default 1;

#select *from library_db.borrowings;

#created to  connect books with one or more authors
/*create table book_authors(book_id int,
author_id int,
primary key(book_id,author_id),
foreign key(book_id)references books(book_id),
foreign key(author_id)references authors(author_id));*/

#create to connect books and genres
/*create table book_genres(book_id int,
genre_id int,
primary key(book_id,genre_id),
foreign key(book_id)references books(book_id),
foreign key(genre_id)references genres(genre_id));*/

#drop table book_genres;

#stores students and staff details
/*create table members(member_id int primary key auto_increment,
name varchar(20)not null,
email varchar(20)unique not null,
phone varchar(20),
category enum('staff','Undergraduate Student','Graduate Student','Research Student')not null,
max_books int not null default 3,
laon_days int not null default 14,
acctive tinyint(1)not null default 1);*/

#alter table members rename column laon_days  to loan_days;
#alter table  members rename column acctive to active;


#created to track borrowings return and fees
/*create table borrowings(borrowing_id int auto_increment primary key,
member_id int not null,
book_id int not null,
borrow_date date not null,
due_date date not null,
return_date date not null,
late_fee decimal(10,2)not null default 0,
lost_fee decimal(10,2)not null default 0,
foreign key(member_id)references members(member_id),
foreign key(book_id)references books(book_id));*/

#alter table borrowings modify column return_date date null default null;

/*create table fees(fee_id int primary key auto_increment,
borrowing_id int not null,
fee_type enum('Late fee','Lost Fee')not null,
amount decimal(10,2)not null,
fee_date date not null,
payment_status enum('pending','paid','waived')default 'pending',
payment_date date null,
foreign key(borrowing_id)references borrowings(borrowing_id));*/

/*insert into authors(author_name)
values('Arthur Conan'),('Jacks Hope');*/

#select *from library_db.authors;

/*insert into genres(genre_name)
values('Fiction'),('Mystery');*/

#select *from library_db.genres;

/*insert into books(title,publication_year,isbn,physical_format,description,price)
values('Sherlock Holmes',1901,'457900001','Book', 'A collection of detective stories', 350.00),
('Murder on the Orient Express',1955,'457900002','Book','A detective mystery novel', 450.00);*/

#select *from books;
/*insert into book_authors(book_id,author_id)
values(1,1),(2,2);*/

#select *from library_db.book_authors;

/*insert into book_genres(book_id,genre_id)
values(1,1),(1,2),(2,1),(2,2);*/

#select *from library_db.book_genres;

/*insert into members(name,email,phone,category,max_books)
values('Nayana','nayana@gmail.com',8089556511,'Undergraduate Student',3),
('Anu','anu@gmail.com',9687035214,'Staff',5);*/

#select *from library_db.members;

#alter table borrowings modify return_date date null;

/*insert into borrowings(member_id,book_id,borrow_date,due_date,return_date)
values(1,1,curdate(),date_add(curdate(),interval 14 day),null),
(2,2,curdate(),date_add(curdate(),interval 30 day),null);*/

#select *from library_db.borrowings;

/*insert into fees(borrowing_id,fee_type,amount,fee_date)
values(1,'Late Fee',20.00,curdate()),
(2,'lost Fee',90.00,curdate());*/

#select *from library_db.fees;

#truncate table fees;

#set foreign_key_checks = 0;
#truncate table  books;
#set foreign_key_checks = 1;


#set foreign_key_checks = 0;
#truncate table authors;
#set foreign_key_checks = 1;


#set foreign_key_checks = 0;
#truncate table genres;
#set foreign_key_checks = 1;

#truncate table book_genres;

#truncate table book_authors;

/*set foreign_key_checks = 0;
truncate table borrowings;
set foreign_key_checks = 1;*/

/*set foreign_key_checks = 0;
truncate table members;
set foreign_key_checks = 1;*/

#alter table authors drop column  title;

#SHOW COLUMNS FROM authors;

#alter table books drop column author_name;

#update  borrowings set  borrow_date = '2026-10-10' where  borrowing_id = 1;

#update  borrowings set  due_date = '2026-10-25' where  borrowing_id = 1;

update  borrowings set  borrow_date = '2026-10-10' where  borrowing_id = 9;

update  borrowings set  due_date = '2026-10-20' where  borrowing_id = 9;

/*create table  member_fee (
fee_id int auto_increment primary key,
member_category varchar(30)not null unique,
late_fee_per_day decimal(10,2)not null
);*/

#insert into  member_fee (member_category, late_fee_per_day)
#values('Staff', 7.00),('Research', 5.00),('Post Graduate', 3.00),('Undergraduate', 2.00);

#alter table  members add column fee_id int null;

/*SET SQL_SAFE_UPDATES = 0;
update  members m join member_fee f
on m.category = f.member_category set  m.fee_id = f.fee_id;
SET SQL_SAFE_UPDATES = 1;*/

#SELECT member_id, fee_id
#FROM members;

#alter table  members add constraint fk_members_member_fee
#foreign key (fee_id) references member_fee(fee_id);

/*SET SQL_SAFE_UPDATES = 0;
update members m join member_fee f
on LOWER(TRIM(m.category)) = LOWER(TRIM(f.member_category))
set m.fee_id = f.fee_id;
SET SQL_SAFE_UPDATES = 1;*/

/*SET SQL_SAFE_UPDATES = 0;
UPDATE members
SET fee_id = CASE
    WHEN LOWER(TRIM(category)) = 'staff' THEN 1
    WHEN LOWER(TRIM(category)) = 'research student' THEN 2
    WHEN LOWER(TRIM(category)) = 'graduate student' THEN 3
    WHEN LOWER(TRIM(category)) = 'undergraduate' THEN 4
    ELSE fee_id
END;
SET SQL_SAFE_UPDATES = 1;*/

/*SET SQL_SAFE_UPDATES = 0;
DELETE FROM fees;
SET SQL_SAFE_UPDATES = 1;*/


/*SET SQL_SAFE_UPDATES = 0;
DELETE FROM borrowings;
SET SQL_SAFE_UPDATES = 1;*/

/*create table book_copies(copy_id int auto_increment primary key,
book_id int not null,status enum('Available', 'Borrowed', 'Lost')not null default 'Available',
foreign key(book_id)references books(book_id));*/

#alter table borrowings add column copy_id int null;

/*alter table borrowings add constraint fk_borrowings_book_copies 
foreign key (copy_id) references book_copies(copy_id);*/

/*create table if not exists book_genres (
book_id int not null,
genre_id int not null,
primary key(book_id, genre_id),
foreign key(book_id) references books(book_id),
foreign key(genre_id) references genres(genre_id));*/









