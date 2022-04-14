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
	CONSTRAINT "fk_microscope_group_lab" FOREIGN KEY("lab_name") REFERENCES "lab"("name") ON DELETE CASCADE,
	CONSTRAINT "pk_microscope_group" PRIMARY KEY("id" AUTOINCREMENT)
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
DROP TABLE IF EXISTS "microscope";
CREATE TABLE IF NOT EXISTS "microscope" (
	"id"	INTEGER,
	"brand"	TEXT,
	"ref"	TEXT,
	"rate"	NUMERIC,
	"desc"	TEXT,
	"group_id"	INTEGER,
	FOREIGN KEY("group_id") REFERENCES "microscopes_group"("id") ON DELETE CASCADE,
	CONSTRAINT "pk_microscope" PRIMARY KEY("id" AUTOINCREMENT)
);
DROP TABLE IF EXISTS "manage";
CREATE TABLE IF NOT EXISTS "manage" (
	"contact_id"	INTEGER,
	"microscopes_group_id"	INTEGER,
	FOREIGN KEY("microscopes_group_id") REFERENCES "microscopes_group"("id"),
	FOREIGN KEY("contact_id") REFERENCES "contact"("id"),
	PRIMARY KEY("contact_id","microscopes_group_id")
);
DROP TABLE IF EXISTS "belong";
CREATE TABLE IF NOT EXISTS "belong" (
	"microscopes_group_id"	INTEGER,
	"microscope_id"	INTEGER,
	FOREIGN KEY("microscope_id") REFERENCES "microscope"("id"),
	FOREIGN KEY("microscopes_group_id") REFERENCES "microscopes_group"("id"),
	PRIMARY KEY("microscopes_group_id","microscope_id")
);
DROP TABLE IF EXISTS "belong_theme";
CREATE TABLE IF NOT EXISTS "belong_theme" (
	"belong_pk"	INTEGER,
	"theme_type"	INTEGER,
	FOREIGN KEY("belong_pk") REFERENCES "belong"("microscopes_group_id","microscope_id"),
	FOREIGN KEY("theme_type") REFERENCES "theme"("type"),
	PRIMARY KEY("belong_pk","theme_type")
);
COMMIT;
