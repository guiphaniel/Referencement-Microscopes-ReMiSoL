BEGIN TRANSACTION;
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
	CONSTRAINT "pk_microscope_group" PRIMARY KEY("id" AUTOINCREMENT),
	CONSTRAINT "fk_microscope_group_lab" FOREIGN KEY("lab_name") REFERENCES "lab"("name") ON DELETE CASCADE
);
DROP TABLE IF EXISTS "theme";
CREATE TABLE IF NOT EXISTS "theme" (
	"type"	TEXT,
	PRIMARY KEY("type")
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
	PRIMARY KEY("contact_id","microscopes_group_id"),
	FOREIGN KEY("contact_id") REFERENCES "contact"("id"),
	FOREIGN KEY("microscopes_group_id") REFERENCES "microscopes_group"("id")
);
DROP TABLE IF EXISTS "belong_theme";
CREATE TABLE IF NOT EXISTS "belong_theme" (
	"belong_microscopes_group_id"	INTEGER,
	"belong_microscope_id"	INTEGER,
	"theme_type"	INTEGER,
	PRIMARY KEY("belong_microscopes_group_id","belong_microscope_id","theme_type"),
	FOREIGN KEY("belong_microscopes_group_id","belong_microscope_id") REFERENCES "belong"("microscopes_group_id","microscope_id"),
	FOREIGN KEY("theme_type") REFERENCES "theme"("type")
);
DROP TABLE IF EXISTS "belong";
CREATE TABLE IF NOT EXISTS "belong" (
	"microscopes_group_id"	INTEGER,
	"microscope_id"	INTEGER,
	"rate"	NUMERIC,
	"desc"	TEXT,
	PRIMARY KEY("microscopes_group_id","microscope_id"),
	FOREIGN KEY("microscope_id") REFERENCES "microscope"("id"),
	FOREIGN KEY("microscopes_group_id") REFERENCES "microscopes_group"("id")
);
DROP TABLE IF EXISTS "microscope";
CREATE TABLE IF NOT EXISTS "microscope" (
	"id"	INTEGER,
	"brand"	TEXT,
	"ref"	TEXT,
	"group_id"	INTEGER,
	CONSTRAINT "pk_microscope" PRIMARY KEY("id" AUTOINCREMENT),
	FOREIGN KEY("group_id") REFERENCES "microscopes_group"("id")
);
COMMIT;
