BEGIN TRANSACTION;
DROP TABLE IF EXISTS "microscope";
CREATE TABLE IF NOT EXISTS "microscope" (
	"id"	INTEGER,
	"brand"	TEXT,
	"reference"	TEXT,
	"rate"	NUMERIC,
	"group_id"	INTEGER,
	FOREIGN KEY("group_id") REFERENCES "microscopes_group"("id") ON DELETE CASCADE,
	CONSTRAINT "pk_microscope" PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "lab";
CREATE TABLE IF NOT EXISTS "lab" (
	"name"	TEXT,
	"address"	TEXT,
	PRIMARY KEY("name")
);
DROP TABLE IF EXISTS "microscopes_group";
CREATE TABLE IF NOT EXISTS "microscopes_group" (
	"id"	INTEGER,
	"lat"	REAL,
	"lon"	REAL,
	"lab_name"	TEXT,
	CONSTRAINT "fk_microscope_group_lab" FOREIGN KEY("lab_name") REFERENCES "lab"("name") ON DELETE CASCADE,
	CONSTRAINT "pk_microscope_group" PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "theme";
CREATE TABLE IF NOT EXISTS "theme" (
	"type"	TEXT,
	PRIMARY KEY("type")
);
DROP TABLE IF EXISTS "microscope_theme";
CREATE TABLE IF NOT EXISTS "microscope_theme" (
	"microscope_id"	INTEGER,
	"theme_type"	INTEGER,
	FOREIGN KEY("microscope_id") REFERENCES "microscope"("id"),
	FOREIGN KEY("theme_type") REFERENCES "theme"("type"),
	PRIMARY KEY("microscope_id","theme_type")
);
DROP TABLE IF EXISTS "contact";
CREATE TABLE IF NOT EXISTS "contact" (
	"id"	INTEGER,
	"name"	TEXT,
	"email"	TEXT,
	PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "manage";
CREATE TABLE IF NOT EXISTS "manage" (
	"contact_id"	INTEGER,
	"microscopes_group_id"	INTEGER,
	FOREIGN KEY("contact_id") REFERENCES "contact"("id"),
	FOREIGN KEY("microscopes_group_id") REFERENCES "microscopes_group"("id"),
	PRIMARY KEY("contact_id","microscopes_group_id")
);
COMMIT;
