
DROP TABLE IF EXISTS manage;
DROP TABLE IF EXISTS contact;
DROP TABLE IF EXISTS microscope_keyword;
DROP TABLE IF EXISTS keyword;
DROP TABLE IF EXISTS category;
DROP TABLE IF EXISTS microscope;
DROP TABLE IF EXISTS locked_microscopes_group;
DROP TABLE IF EXISTS microscopes_group;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS locked_user;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS coordinates;
DROP TABLE IF EXISTS lab;
DROP TABLE IF EXISTS address;
DROP TABLE IF EXISTS controller;
DROP TABLE IF EXISTS model;
DROP TABLE IF EXISTS brand;
DROP TABLE IF EXISTS compagny;

CREATE TABLE compagny (
	id	INTEGER AUTO_INCREMENT,
	name	VARCHAR(50) UNIQUE,
	PRIMARY KEY(id)
);
CREATE TABLE brand (
	id	INTEGER AUTO_INCREMENT,
	name	VARCHAR(50) UNIQUE,
	compagny_id	INTEGER,
	FOREIGN KEY(compagny_id) REFERENCES compagny(id) ON DELETE CASCADE,
	PRIMARY KEY(id)
);
CREATE TABLE model (
	id	INTEGER AUTO_INCREMENT,
	name	VARCHAR(50) UNIQUE,
	brand_id	INTEGER,
	FOREIGN KEY(brand_id) REFERENCES brand(id) ON DELETE CASCADE,
	PRIMARY KEY(id)
);
CREATE TABLE controller (
	id	INTEGER AUTO_INCREMENT,
	name	VARCHAR(50) UNIQUE,
	brand_id	INTEGER,
	FOREIGN KEY(brand_id) REFERENCES brand(id) ON DELETE CASCADE,
	PRIMARY KEY(id)
);
CREATE TABLE address (
	id	INTEGER AUTO_INCREMENT,
	school	VARCHAR(50),
	street	VARCHAR(50),
	zipCode	VARCHAR(50),
	city	VARCHAR(50),
	country	VARCHAR(50),
	PRIMARY KEY(id)
);
CREATE TABLE lab (
	id	INTEGER AUTO_INCREMENT,
	name	VARCHAR(50),
	type	VARCHAR(4),
	code	VARCHAR(10),
	website	VARCHAR(200),
	address_id	INTEGER,
	UNIQUE(type,code),
	PRIMARY KEY(id),
	FOREIGN KEY(address_id) REFERENCES address(id)
);
CREATE TABLE coordinates (
	id	INTEGER AUTO_INCREMENT,
	lat	REAL,
	lon	REAL,
	UNIQUE(lat,lon),
	CONSTRAINT pk_coordinates PRIMARY KEY(id)
);
CREATE TABLE `user` (
	id	INTEGER AUTO_INCREMENT,
	firstname	VARCHAR(50),
	lastname	VARCHAR(50),
	norm_lastname	VARCHAR(50),
	email	VARCHAR(50) UNIQUE,
	phone_code	VARCHAR(3),
	phone_num	VARCHAR(9),
	password	VARCHAR(255),
	PRIMARY KEY(id)
);
CREATE TABLE locked_user (
	user_id	INTEGER,
	token	VARCHAR(128),
	FOREIGN KEY(user_id) REFERENCES user(id) ON DELETE CASCADE,
	PRIMARY KEY(user_id)
);
CREATE TABLE admin (
	user_id	INTEGER,
	FOREIGN KEY(user_id) REFERENCES user(id) ON DELETE CASCADE,
	PRIMARY KEY(user_id)
);
CREATE TABLE microscopes_group (
	id	INTEGER AUTO_INCREMENT,
	coordinates_id	INTEGER,
	lab_id	INTEGER,
	user_id	INTEGER,
	FOREIGN KEY(coordinates_id) REFERENCES coordinates(id) ON DELETE CASCADE,
	FOREIGN KEY(lab_id) REFERENCES lab(id) ON DELETE CASCADE,
	FOREIGN KEY(user_id) REFERENCES user(id) ON DELETE SET NULL,
	CONSTRAINT pk_microscope_group PRIMARY KEY(id)
);
CREATE TABLE locked_microscopes_group (
	microscopes_group_id	INTEGER,
	FOREIGN KEY(microscopes_group_id) REFERENCES microscopes_group(id) ON DELETE CASCADE,
	PRIMARY KEY(microscopes_group_id)
);
CREATE TABLE microscope (
	id	INTEGER AUTO_INCREMENT,
	rate	VARCHAR(200),
	`descr`	VARCHAR(2000),
	norm_descr	VARCHAR(2000),
	type	ENUM('LABO', 'SERV'),
	access	ENUM('ACAD', 'INDU', 'BOTH'),
	model_id	INTEGER,
	controller_id	INTEGER,
	microscopes_group_id INTEGER,
	FOREIGN KEY(controller_id) REFERENCES controller(id),
	FOREIGN KEY(model_id) REFERENCES model(id),
	FOREIGN KEY(microscopes_group_id) REFERENCES microscopes_group(id) ON DELETE CASCADE,
	PRIMARY KEY(id)
);
CREATE TABLE category (
	id	INTEGER AUTO_INCREMENT,
	name	VARCHAR(50) UNIQUE,
	norm_name	VARCHAR(50) UNIQUE,
	PRIMARY KEY(id)
);
CREATE TABLE keyword (
	id	INTEGER AUTO_INCREMENT,
	category_id	INTEGER,
	tag	VARCHAR(50),
	norm_tag	VARCHAR(50),
	UNIQUE(category_id,tag),
	UNIQUE(category_id,norm_tag),
	FOREIGN KEY(category_id) REFERENCES category(id) ON DELETE CASCADE,
	PRIMARY KEY(id)
);
CREATE TABLE microscope_keyword (
	microscope_id	INTEGER,
	keyword_id	INTEGER,
	FOREIGN KEY(keyword_id) REFERENCES keyword(id) ON DELETE CASCADE,
	FOREIGN KEY(microscope_id) REFERENCES microscope(id) ON DELETE CASCADE,
	PRIMARY KEY(microscope_id,keyword_id)
);
CREATE TABLE contact (
	id	INTEGER AUTO_INCREMENT,
	firstname	VARCHAR(50),
	lastname	VARCHAR(50),
	norm_lastname	VARCHAR(50),
	email	VARCHAR(50),
	phone_code	VARCHAR(3),
	phone_num	VARCHAR(9),
	role	VARCHAR(50),
	PRIMARY KEY(id)
);
CREATE TABLE manage (
	contact_id	INTEGER,
	microscopes_group_id	INTEGER,
	FOREIGN KEY(contact_id) REFERENCES contact(id) ON DELETE CASCADE,
	FOREIGN KEY(microscopes_group_id) REFERENCES microscopes_group(id) ON DELETE CASCADE,
	PRIMARY KEY(contact_id,microscopes_group_id)
);
