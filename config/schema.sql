BEGIN TRANSACTION;
DROP TABLE IF EXISTS "admin";
DROP TABLE IF EXISTS "locked_user";
DROP TABLE IF EXISTS "user";
DROP TABLE IF EXISTS "manage";
DROP TABLE IF EXISTS "contact";
DROP TABLE IF EXISTS "microscope_keyword";
DROP TABLE IF EXISTS "keyword";
DROP TABLE IF EXISTS "category";
DROP TABLE IF EXISTS "microscope";
DROP TABLE IF EXISTS "locked_microscopes_group";
DROP TABLE IF EXISTS "microscopes_group";
DROP TABLE IF EXISTS "coordinates";
DROP TABLE IF EXISTS "lab";
DROP TABLE IF EXISTS "address";
DROP TABLE IF EXISTS "controller";
DROP TABLE IF EXISTS "model";
DROP TABLE IF EXISTS "brand";
DROP TABLE IF EXISTS "compagny";

CREATE TABLE "compagny" (
	"id"	INTEGER,
	"name"	TEXT UNIQUE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "brand" (
	"id"	INTEGER,
	"name"	TEXT UNIQUE,
	"compagny_id"	INTEGER,
	FOREIGN KEY("compagny_id") REFERENCES "compagny"("id") ON DELETE CASCADE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "model" (
	"id"	INTEGER,
	"name"	TEXT UNIQUE,
	"brand_id"	INTEGER,
	FOREIGN KEY("brand_id") REFERENCES "brand"("id") ON DELETE CASCADE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "controller" (
	"id"	INTEGER,
	"name"	TEXT UNIQUE,
	"brand_id"	INTEGER,
	FOREIGN KEY("brand_id") REFERENCES "brand"("id") ON DELETE CASCADE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "microscope" (
	"id"	INTEGER,
	"rate"	TEXT,
	"descr"	TEXT,
	"norm_descr"	TEXT,
	"type"	TEXT, /* TODO: MySQL: replace by ENUM(LABO, SERV)*/
	"access"	TEXT, /* TODO: MySQL: replace by ENUM(ACAD, INDU, BOTH)*/
	"model_id"	INTEGER,
	"controller_id"	INTEGER,
	"microscopes_group_id" INTEGER,
	FOREIGN KEY("controller_id") REFERENCES "controller"("id"),
	FOREIGN KEY("model_id") REFERENCES "model"("id"),
	FOREIGN KEY("microscopes_group_id") REFERENCES "microscopes_group"("id") ON DELETE CASCADE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "category" (
	"id"	INTEGER,
	"name"	TEXT UNIQUE,
	"norm_name"	TEXT UNIQUE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "keyword" (
	"id"	INTEGER,
	"category_id"	INTEGER,
	"tag"	TEXT,
	"norm_tag"	TEXT,
	UNIQUE("category_id","tag"),
	UNIQUE("category_id","norm_tag"),
	FOREIGN KEY("category_id") REFERENCES "category"("id") ON DELETE CASCADE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "microscope_keyword" (
	"microscope_id"	INTEGER,
	"keyword_id"	INTEGER,
	FOREIGN KEY("keyword_id") REFERENCES "keyword"("id") ON DELETE CASCADE,
	FOREIGN KEY("microscope_id") REFERENCES "microscope"("id") ON DELETE CASCADE,
	PRIMARY KEY("microscope_id","keyword_id")
);
CREATE TABLE "address" (
	"id"	INTEGER,
	"school"	TEXT,
	"street"	TEXT,
	"zipCode"	TEXT,
	"city"	TEXT,
	"country"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "lab" (
	"id"	INTEGER,
	"name"	TEXT,
	"type"	TEXT,
	"code"	TEXT,
	"website"	TEXT,
	"address_id"	INTEGER,
	UNIQUE("type","code"),
	PRIMARY KEY("id" AUTOINCREMENT),
	FOREIGN KEY("address_id") REFERENCES "address"("id")
);
CREATE TABLE "coordinates" (
	"id"	INTEGER,
	"lat"	REAL,
	"lon"	REAL,
	UNIQUE("lat","lon"),
	CONSTRAINT "pk_coordinates" PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "microscopes_group" (
	"id"	INTEGER,
	"coordinates_id"	INTEGER,
	"lab_id"	INTEGER,
	"user_id"	INTEGER,
	FOREIGN KEY("coordinates_id") REFERENCES "coordinates"("id") ON DELETE CASCADE,
	FOREIGN KEY("lab_id") REFERENCES "lab"("id") ON DELETE CASCADE,
	FOREIGN KEY("user_id") REFERENCES "user"("id") ON DELETE SET NULL,
	CONSTRAINT "pk_microscope_group" PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "locked_microscopes_group" (
	"microscopes_group_id"	INTEGER,
	FOREIGN KEY("microscopes_group_id") REFERENCES "microscopes_group"("id") ON DELETE CASCADE,
	PRIMARY KEY("microscopes_group_id")
);
CREATE TABLE "contact" (
	"id"	INTEGER,
	"firstname"	TEXT,
	"lastname"	TEXT,
	"norm_lastname"	TEXT,
	"email"	TEXT,
	"phone_code"	TEXT,
	"phone_num"	TEXT,
	"role"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "manage" (
	"contact_id"	INTEGER,
	"microscopes_group_id"	INTEGER,
	FOREIGN KEY("contact_id") REFERENCES "contact"("id") ON DELETE CASCADE,
	FOREIGN KEY("microscopes_group_id") REFERENCES "microscopes_group"("id") ON DELETE CASCADE,
	PRIMARY KEY("contact_id","microscopes_group_id")
);
CREATE TABLE "user" (
	"id"	INTEGER,
	"firstname"	TEXT,
	"lastname"	TEXT,
	"norm_lastname"	TEXT,
	"email"	TEXT UNIQUE,
	"phone_code"	TEXT,
	"phone_num"	TEXT,
	"password"	TEXT, /* TODO: MySQL: replace by VARCHAR(255)*/
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "locked_user" (
	"user_id"	INTEGER,
	"token"	TEXT,
	FOREIGN KEY("user_id") REFERENCES "user"("id") ON DELETE CASCADE,
	PRIMARY KEY("user_id")
);
CREATE TABLE "admin" (
	"user_id"	INTEGER,
	FOREIGN KEY("user_id") REFERENCES "user"("id") ON DELETE CASCADE,
	PRIMARY KEY("user_id" AUTOINCREMENT)
);
COMMIT;
