BEGIN TRANSACTION;
DROP TABLE IF EXISTS "manage";
DROP TABLE IF EXISTS "contact";
DROP TABLE IF EXISTS "microscope_keyword";
DROP TABLE IF EXISTS "keyword";
DROP TABLE IF EXISTS "microscope";
DROP TABLE IF EXISTS "microscopes_group";
DROP TABLE IF EXISTS "lab";
DROP TABLE IF EXISTS "controller";
DROP TABLE IF EXISTS "model";
DROP TABLE IF EXISTS "brand";
DROP TABLE IF EXISTS "compagny";

CREATE TABLE "compagny" (
	"id"	INTEGER,
	"com_name"	TEXT UNIQUE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "brand" (
	"id"	INTEGER,
	"bra_name"	TEXT UNIQUE,
	"compagny_id"	INTEGER,
	FOREIGN KEY("compagny_id") REFERENCES "compagny"("id") ON DELETE CASCADE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "model" (
	"id"	INTEGER,
	"mod_name"	TEXT UNIQUE,
	"brand_id"	INTEGER,
	FOREIGN KEY("brand_id") REFERENCES "brand"("id") ON DELETE CASCADE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "controller" (
	"id"	INTEGER,
	"ctr_name"	TEXT UNIQUE,
	"brand_id"	INTEGER,
	FOREIGN KEY("brand_id") REFERENCES "brand"("id") ON DELETE CASCADE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "microscope" (
	"id"	INTEGER,
	"rate"	TEXT,
	"desc"	TEXT,
	"access"	TEXT, /* TODO: MySQL: replace by ENUM(LAB, SERVICE, BOTH)*/
	"model_id"	INTEGER,
	"controller_id"	INTEGER,
	"microscopes_group_id" INTEGER,
	FOREIGN KEY("controller_id") REFERENCES "controller"("id"),
	FOREIGN KEY("model_id") REFERENCES "model"("id"),
	FOREIGN KEY("microscopes_group_id") REFERENCES "microscopes_group"("id") ON DELETE CASCADE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "keyword" (
	"id"	INTEGER,
	"cat"	TEXT,
	"tag"	TEXT,
	UNIQUE("cat","tag"),
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "microscope_keyword" (
	"microscope_id"	INTEGER,
	"keyword_id"	INTEGER,
	FOREIGN KEY("keyword_id") REFERENCES "keyword"("id"),
	FOREIGN KEY("microscope_id") REFERENCES "microscope"("id"),
	PRIMARY KEY("microscope_id","keyword_id")
);
CREATE TABLE "lab" (
	"id"	INTEGER,
	"lab_name"	TEXT UNIQUE,
	"address"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "microscopes_group" (
	"id"	INTEGER,
	"lat"	REAL,
	"lon"	REAL,
	"lab_id"	INTEGER,
	FOREIGN KEY("lab_id") REFERENCES "lab"("id") ON DELETE CASCADE,
	CONSTRAINT "pk_microscope_group" PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "contact" (
	"id"	INTEGER,
	"firstname"	TEXT,
	"lastname"	TEXT,
	"role"	TEXT,
	"email"	TEXT UNIQUE,
	"phone"	TEXT UNIQUE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
CREATE TABLE "manage" (
	"contact_id"	INTEGER,
	"microscopes_group_id"	INTEGER,
	FOREIGN KEY("contact_id") REFERENCES "contact"("id"),
	FOREIGN KEY("microscopes_group_id") REFERENCES "microscopes_group"("id"),
	PRIMARY KEY("contact_id","microscopes_group_id")
);
COMMIT;
