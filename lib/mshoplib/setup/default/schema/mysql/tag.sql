--
-- Tag database definitions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://opensource.org/licenses/LGPL-3.0
-- @copyright Aimeos (aimeos.org), 2015
--


SET SESSION sql_mode='ANSI';


--
-- Table structure for table `mshop_tag_type`
--

CREATE TABLE "mshop_tag_type" (
	-- Unique id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- domain
	"domain" VARCHAR(32) NOT NULL,
	-- code
	"code"  VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Name of the tag type
	"label" VARCHAR(255) NOT NULL,
	-- Status (0=disabled, 1=enabled, >1 for special)
	"status" SMALLINT NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_mstagty_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mstagty_sid_dom_code"
	UNIQUE ("siteid", "domain", "code")
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mstagty_sid_status" ON "mshop_tag_type" ("siteid", "status");

CREATE INDEX "idx_mstagty_sid_label" ON "mshop_tag_type" ("siteid", "label");

CREATE INDEX "idx_mstagty_sid_code" ON "mshop_tag_type" ("siteid", "code");


--
-- Product tags
--

CREATE TABLE "mshop_tag" (
	-- Product id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id
	"siteid" INTEGER NOT NULL,
	-- type id, to build group of tags like special, benefits e.g.
	"typeid" INTEGER NOT NULL,
	-- ISO language id
	"langid" VARCHAR(5) DEFAULT NULL,
	-- Tag name
	"label" VARCHAR(255) NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_mstag_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mstag_sid_tid_lid_label"
	UNIQUE ("siteid", "typeid", "langid", "label"),
CONSTRAINT "fk_mstag_typeid"
	FOREIGN KEY ("typeid")
	REFERENCES "mshop_tag_type" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_mstag_sid_label" ON "mshop_tag" ("siteid", "label");

CREATE INDEX "idx_mstag_sid_langid" ON "mshop_tag" ("siteid", "langid");
