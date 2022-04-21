BEGIN TRANSACTION;
DROP TABLE IF EXISTS "contact";
CREATE TABLE IF NOT EXISTS "contact" (
	"id"	INTEGER,
	"firstname"	TEXT,
	"lastname"	TEXT,
	"email"	TEXT UNIQUE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "microscopes_group";
CREATE TABLE IF NOT EXISTS "microscopes_group" (
	"id"	INTEGER,
	"lat"	REAL,
	"lon"	REAL,
	"lab_id"	INTEGER,
	"contact_id"	INTEGER,
	FOREIGN KEY("lab_id") REFERENCES "lab"("id") ON DELETE CASCADE,
	FOREIGN KEY("contact_id") REFERENCES "contact"("id"),
	CONSTRAINT "pk_microscope_group" PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "keyword";
CREATE TABLE IF NOT EXISTS "keyword" (
	"id"	INTEGER,
	"cat"	TEXT,
	"tag"	TEXT,
	UNIQUE("cat","tag"),
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "microscope";
CREATE TABLE IF NOT EXISTS "microscope" (
	"id"	INTEGER,
	"rate"	TEXT,
	"desc"	TEXT,
	"model_id"	INTEGER,
	"controller_id"	INTEGER,
	"microscopes_group_id" INTEGER,
	FOREIGN KEY("controller_id") REFERENCES "controller"("id"),
	FOREIGN KEY("model_id") REFERENCES "model"("id"),
	FOREIGN KEY("microscopes_group_id") REFERENCES "microscopes_group"("id") ON DELETE CASCADE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "microscope_keyword";
CREATE TABLE IF NOT EXISTS "microscope_keyword" (
	"microscope_id"	INTEGER,
	"keyword_id"	INTEGER,
	FOREIGN KEY("keyword_id") REFERENCES "keyword"("id"),
	FOREIGN KEY("microscope_id") REFERENCES "microscope"("id"),
	PRIMARY KEY("microscope_id","keyword_id")
);
DROP TABLE IF EXISTS "lab";
CREATE TABLE IF NOT EXISTS "lab" (
	"id"	INTEGER,
	"lab_name"	TEXT UNIQUE,
	"address"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "model";
CREATE TABLE IF NOT EXISTS "model" (
	"id"	INTEGER,
	"mod_name"	TEXT UNIQUE,
	"brand_id"	INTEGER,
	FOREIGN KEY("brand_id") REFERENCES "brand"("id") ON DELETE CASCADE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "controller";
CREATE TABLE IF NOT EXISTS "controller" (
	"id"	INTEGER,
	"ctr_name"	TEXT UNIQUE,
	"brand_id"	INTEGER,
	FOREIGN KEY("brand_id") REFERENCES "brand"("id") ON DELETE CASCADE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "brand";
CREATE TABLE IF NOT EXISTS "brand" (
	"id"	INTEGER,
	"bra_name"	TEXT UNIQUE,
	"compagny_id"	INTEGER,
	FOREIGN KEY("compagny_id") REFERENCES "compagny"("id") ON DELETE CASCADE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "compagny";
CREATE TABLE IF NOT EXISTS "compagny" (
	"id"	INTEGER,
	"com_name"	TEXT UNIQUE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
COMMIT;
