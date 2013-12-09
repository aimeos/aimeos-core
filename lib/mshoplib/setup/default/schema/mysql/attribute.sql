--
-- Attribute database definitions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://www.arcavias.com/en/license
-- @version $Id: attribute.sql 14277 2011-12-12 11:28:56Z spopp $
--


SET SESSION sql_mode='ANSI';


--
-- Attribute types
--

CREATE TABLE "mshop_attribute_type" (
	-- Unique id of the attribute type
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id
	"siteid" INTEGER NOT NULL,
	-- domain
	"domain" VARCHAR(32) NOT NULL,
	-- Code of the attribute type
	"code" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Name of the type
	"label" VARCHAR(255) NOT NULL,
	-- Status (0=disabled, 1=enabled, >1 for special)
	"status" SMALLINT NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msattty_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msattty_sid_dom_code"
	UNIQUE ("siteid", "domain", "code"),
CONSTRAINT "fk_msattty_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_msattty_sid_status" ON "mshop_attribute_type" ("siteid", "status");

CREATE INDEX "idx_msattty_sid_label" ON "mshop_attribute_type" ("siteid", "label");

CREATE INDEX "idx_msattty_sid_code" ON "mshop_attribute_type" ("siteid", "code");


--
-- Table structure for table `mshop_attribute`
--

CREATE TABLE "mshop_attribute" (
	-- Unique id of the tree node
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- attribute type
	"typeid" INTEGER NOT NULL,
	-- domain the attributes belongs to
	"domain" VARCHAR(32) NOT NULL,
	-- code
	"code" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- label
	"label" VARCHAR(255) NOT NULL,
	-- position
	"pos" INTEGER NOT NULL,
	-- status code (0=hidden, 1=display, >1 for anything special)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msattr_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msattr_sid_dom_cod_tid"
	UNIQUE ("siteid", "domain", "code", "typeid"),
CONSTRAINT "fk_msattr_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msattr_typeid"
	FOREIGN KEY ("typeid")
	REFERENCES "mshop_attribute_type" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msatt_sid_status" ON "mshop_attribute" ("siteid", "status");

CREATE INDEX "idx_msatt_sid_dom_label" ON "mshop_attribute" ("siteid", "domain", "label");

CREATE INDEX "idx_msatt_sid_dom_pos" ON "mshop_attribute" ("siteid", "domain", "pos");

CREATE INDEX "idx_msatt_sid_dom_ctime" ON "mshop_attribute" ("siteid", "domain", "ctime");

CREATE INDEX "idx_msatt_sid_dom_mtime" ON "mshop_attribute" ("siteid", "domain", "mtime");

CREATE INDEX "idx_msatt_sid_dom_editor" ON "mshop_attribute" ("siteid", "domain", "editor");


--
-- Table structure for table `mshop_attribute_list_type`
--

CREATE TABLE "mshop_attribute_list_type" (
	-- Unique id
	"id" INTEGER AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- domain
	"domain" VARCHAR(32) NOT NULL,
	-- code
	"code"  VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Name of the list type
	"label" VARCHAR(255) NOT NULL,
	-- Status (0=disabled, 1=enabled, >1 for special)
	"status" SMALLINT NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msattlity_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msattlity_sid_dom_code"
	UNIQUE ("siteid", "domain", "code"),
CONSTRAINT "fk_msattlity_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msattlity_sid_status" ON "mshop_attribute_list_type" ("siteid", "status");

CREATE INDEX "idx_msattlity_sid_label" ON "mshop_attribute_list_type" ("siteid", "label");

CREATE INDEX "idx_msattlity_sid_code" ON "mshop_attribute_list_type" ("siteid", "code");


--
-- Table structure for table `mshop_attribute_list`
--

CREATE TABLE "mshop_attribute_list" (
	-- Unique list id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- attribute tree id (parent id)
	"parentid" INTEGER NOT NULL,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- typeid
	"typeid" INTEGER NOT NULL,
	-- domain (e.g.: text, media)
	"domain" VARCHAR(32) NOT NULL,
	-- Reference of the object in given domain
	"refid" VARCHAR(32) NOT NULL,
	-- Valid from
	"start" DATETIME DEFAULT NULL,
	-- Valid until
	"end" DATETIME DEFAULT NULL,
	-- Precedence of the promotion
	"pos" INTEGER NOT NULL,
	-- status code (0=hidden, 1=display, >1 for anything special)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msattli_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msattli_sid_pid_dm_rid_tid"
	UNIQUE ("siteid", "parentid", "domain", "refid", "typeid"),
CONSTRAINT "fk_msattrli_pid"
	FOREIGN KEY ("parentid")
	REFERENCES "mshop_attribute" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msattli_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msattli_typeid"
	FOREIGN KEY ( "typeid" )
	REFERENCES "mshop_attribute_list_type" ("id")
	ON DELETE CASCADE
	ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msattli_sid_start_end" ON "mshop_attribute_list" ("siteid", "start", "end");

CREATE INDEX "idx_msattli_sid_rid_dom_tid" ON "mshop_attribute_list" ( "siteid", "refid", "domain", "typeid" );

CREATE INDEX "idx_msattli_pid_sid_rid" ON "mshop_attribute_list" ("parentid", "siteid", "refid");

CREATE INDEX "idx_msattli_pid_sid_start" ON "mshop_attribute_list" ("parentid", "siteid", "start");

CREATE INDEX "idx_msattli_pid_sid_end" ON "mshop_attribute_list" ("parentid", "siteid", "end");

CREATE INDEX "idx_msattli_pid_sid_pos" ON "mshop_attribute_list" ("parentid", "siteid", "pos");
