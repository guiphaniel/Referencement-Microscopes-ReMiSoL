BEGIN TRANSACTION;
DROP TABLE IF EXISTS "theme";
CREATE TABLE IF NOT EXISTS "theme" (
	"type"	TEXT,
	PRIMARY KEY("type")
);
DROP TABLE IF EXISTS "manage";
CREATE TABLE IF NOT EXISTS "manage" (
	"microscopes_group_id"	INTEGER,
	"contact_id"	INTEGER,
	PRIMARY KEY("microscopes_group_id","contact_id"),
	FOREIGN KEY("microscopes_group_id") REFERENCES "microscopes_group"("id"),
	FOREIGN KEY("contact_id") REFERENCES "contact"("id")
);
DROP TABLE IF EXISTS "contact";
CREATE TABLE IF NOT EXISTS "contact" (
	"id"	INTEGER,
	"firstname"	TEXT,
	"lastname"	TEXT,
	"email"	TEXT UNIQUE,
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "belong_theme";
CREATE TABLE IF NOT EXISTS "belong_theme" (
	"belong_microscopes_group_id"	INTEGER,
	"belong_microscope_id"	INTEGER,
	"theme_type"	INTEGER,
	PRIMARY KEY("belong_microscopes_group_id","belong_microscope_id","theme_type"),
	FOREIGN KEY("theme_type") REFERENCES "theme"("type"),
	FOREIGN KEY("belong_microscopes_group_id","belong_microscope_id") REFERENCES "belong"("microscopes_group_id","microscope_id")
);
DROP TABLE IF EXISTS "belong";
CREATE TABLE IF NOT EXISTS "belong" (
	"microscopes_group_id"	INTEGER,
	"microscope_id"	INTEGER,
	"rate"	NUMERIC,
	"desc"	TEXT,
	FOREIGN KEY("microscope_id") REFERENCES "microscope"("id"),
	FOREIGN KEY("microscopes_group_id") REFERENCES "microscopes_group"("id"),
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
DROP TABLE IF EXISTS "microscope";
CREATE TABLE IF NOT EXISTS "microscope" (
	"id"	INTEGER,
	"brand"	TEXT,
	"ref"	TEXT,
	CONSTRAINT "u_brand_ref" UNIQUE("brand","ref"),
	CONSTRAINT "pk_microscope" PRIMARY KEY("id" AUTOINCREMENT)
);
COMMIT;
