/* Gladius Database Engine
 * @author legolas558
 * @version 0.6
 * Licensed under GNU General Public License (GPL)
 *
 * Example SQL file
*/

SHOW DATABASES;

DROP DATABASE amy;

CREATE DATABASE amy;

USE amy;

CREATE TABLE gladius_one(
	id integer auto_increment,
	name varchar(50),
	comment text,
	years int not null default 45,
	primary key(id)	
);

CREATE TABLE gladius_two(
	name varchar(100),
	phone varchar(20)
);

RENAME TABLE gladius_one TO gladius_tt, gladius_two TO phonebook;

INSERT INTO gladius_tt VALUES(5, 'Hackerbud', 'wudd''u say?');

INSERT INTO gladius_tt VALUES(90, 'Hackerbud', 'wudd''u say?', 543);

INSERT INTO gladius_tt VALUES(2, 'Hickobud', 'Nee nee nee!');

INSERT INTO gladius_tt VALUES(7, 'Longjohn baudrate', 'Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi Apres le midi ');

/*
SELECT 	id,name,comment from gladius_tt WHERE id NOT IN (SELECT id FROM gladius_tt WHERE name='Hackerbud xx') ORDER BY id;

UPDATE gladius_tt SET comment = 'Young!' WHERE id > 2;

SELECT COUNT(*) FROM gladius_tt;

SELECT 	id,name,comment from gladius_tt ORDER BY id;

SELECT * FROM gladius_tt LIMIT 1;
*/

/* SELECT COUNT(*) FROM gladius_tt LIMIT 0; */


SELECT * FROM gladius_tt WHERE ((comment LIKE '%midi%') and (years > 30)) OR (name LIKE '%say%');



SELECT * FROM gladius_tt WHERE id < 7 and id>=2 and id<>5;

SELECT * FROM gladius_tt WHERE (comment LIKE '%midi%');
