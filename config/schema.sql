BEGIN TRANSACTION;
DROP TABLE IF EXISTS "manage";
CREATE TABLE IF NOT EXISTS "manage" (
	"microscopes_group_id"	INTEGER,
	"contact_id"	INTEGER,
	FOREIGN KEY("contact_id") REFERENCES "contact"("id"),
	FOREIGN KEY("microscopes_group_id") REFERENCES "microscopes_group"("id"),
	PRIMARY KEY("microscopes_group_id","contact_id")
);
DROP TABLE IF EXISTS "contact";
CREATE TABLE IF NOT EXISTS "contact" (
	"id"	INTEGER,
	"firstname"	TEXT,
	"lastname"	TEXT,
	"email"	TEXT UNIQUE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "belong";
CREATE TABLE IF NOT EXISTS "belong" (
	"microscopes_group_id"	INTEGER,
	"microscope_id"	INTEGER,
	"rate"	NUMERIC,
	"desc"	TEXT,
	FOREIGN KEY("microscopes_group_id") REFERENCES "microscopes_group"("id"),
	FOREIGN KEY("microscope_id") REFERENCES "microscope"("id"),
	PRIMARY KEY("microscopes_group_id","microscope_id")
);
DROP TABLE IF EXISTS "lab";
CREATE TABLE IF NOT EXISTS "lab" (
	"id"	INTEGER,
	"name"	TEXT UNIQUE,
	"address"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "microscopes_group";
CREATE TABLE IF NOT EXISTS "microscopes_group" (
	"id"	INTEGER,
	"lat"	REAL,
	"lon"	REAL,
	"lab_id"	INTEGER,
	FOREIGN KEY("lab_id") REFERENCES "lab"("id") ON DELETE CASCADE,
	CONSTRAINT "pk_microscope_group" PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "belong_keyword";
CREATE TABLE IF NOT EXISTS "belong_keyword" (
	"belong_microscopes_group_id"	INTEGER,
	"belong_microscope_id"	INTEGER,
	"keyword_id"	INTEGER,
	FOREIGN KEY("keyword_id") REFERENCES "keyword"("cat"),
	FOREIGN KEY("belong_microscopes_group_id","belong_microscope_id") REFERENCES "belong"("microscopes_group_id","microscope_id"),
	PRIMARY KEY("belong_microscopes_group_id","belong_microscope_id","keyword_id")
);
DROP TABLE IF EXISTS "keyword";
CREATE TABLE IF NOT EXISTS "keyword" (
	"id"	INTEGER,
	"cat"	TEXT,
	"tag"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT),
	UNIQUE("cat","tag")
);
DROP TABLE IF EXISTS "controller";
CREATE TABLE IF NOT EXISTS "controller" (
	"name"	TEXT,
	"brand_name"	TEXT,
	FOREIGN KEY("brand_name") REFERENCES "brand"("name") ON DELETE CASCADE,
	PRIMARY KEY("name")
);
DROP TABLE IF EXISTS "brand";
CREATE TABLE IF NOT EXISTS "brand" (
	"name"	TEXT,
	"compagny"	TEXT,
	PRIMARY KEY("name")
);
DROP TABLE IF EXISTS "microscope";
CREATE TABLE IF NOT EXISTS "microscope" (
	"id"	INTEGER,
	"model"	TEXT,
	"brand_name"	TEXT,
	"controller_name"	TEXT NOT NULL,
	FOREIGN KEY("controller_name") REFERENCES "controller"("name"),
	FOREIGN KEY("brand_name") REFERENCES "brand"("name") ON DELETE CASCADE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
COMMIT;
