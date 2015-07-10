--
-- Category database definitions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://opensource.org/licenses/LGPL-3.0
--


SET SESSION sql_mode='ANSI';



--
-- Catalog
--

CREATE TABLE "mshop_catalog" (
	-- Unique id of the tree node
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- parent id
	"parentid" INTEGER NOT NULL,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- Level of depth of the node
	"level" SMALLINT NOT NULL,
	-- Code of catalog node
	"code" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Displayed name in backend
	"label" VARCHAR(255) NOT NULL,
	-- catalog config values stored in JSON format
	"config" TEXT NOT NULL,
	-- Left value of the node
	"nleft" INTEGER NOT NULL,
	-- Right value of the node
	"nright" INTEGER NOT NULL,
	-- status code (0=hidden, 1=display, >1 for anything special)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_mscat_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mscat_sid_code"
	UNIQUE ( "siteid", "code" )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mscat_sid_nlt_nrt_lvl_pid" ON "mshop_catalog" ("siteid", "nleft", "nright", "level", "parentid");

CREATE INDEX "idx_mscat_sid_status" ON "mshop_catalog" ("siteid", "status");


--
-- Table structure for table `mshop_catalog_list_type`
--

CREATE TABLE "mshop_catalog_list_type" (
	-- Unique id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- domain
	"domain" VARCHAR(32) NOT NULL,
	-- code
	"code" VARCHAR(32) NOT NULL COLLATE utf8_bin,
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
CONSTRAINT "pk_mscatlity_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mscatlity_sid_dom_code"
	UNIQUE ("siteid", "domain", "code")
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mscatlity_sid_status" ON "mshop_catalog_list_type" ("siteid", "status");

CREATE INDEX "idx_mscatlity_sid_label" ON "mshop_catalog_list_type" ("siteid", "label");

CREATE INDEX "idx_mscatlity_sid_code" ON "mshop_catalog_list_type" ("siteid", "code");


--
-- Catalog list
--

CREATE TABLE "mshop_catalog_list" (
	-- Unique list id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- Catalog id
	"parentid" INTEGER NOT NULL,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- typeid
	"typeid" INTEGER NOT NULL,
	-- Referenced domain
	"domain" VARCHAR(32) NOT NULL,
	-- Featured reference
	"refid" VARCHAR(32) NOT NULL,
	-- Valid from
	"start" DATETIME DEFAULT NULL,
	-- Valid until
	"end" DATETIME DEFAULT NULL,
	-- Configuration
	"config" TEXT NOT NULL,
	-- Position of the list entry
	"pos" INTEGER NOT NULL,
	-- status code (0=hidden, 1=display, >1 for anything special)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_mscatli_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mscatli_sid_dm_rid_tid_pid"
	UNIQUE ("siteid", "domain", "refid", "typeid", "parentid"),
CONSTRAINT "fk_mscatli_parentid"
	FOREIGN KEY ("parentid")
	REFERENCES "mshop_catalog" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mscatli_typeid"
	FOREIGN KEY ( "typeid" )
	REFERENCES "mshop_catalog_list_type" ("id")
	ON DELETE CASCADE
	ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_mscatli_sid_stat_start_end" ON "mshop_catalog_list" ("siteid", "status", "start", "end");

CREATE INDEX "idx_mscatli_pid_sid_rid_dm_tid" ON "mshop_catalog_list" ("parentid", "siteid", "refid", "domain", "typeid");

CREATE INDEX "idx_mscatli_pid_sid_start" ON "mshop_catalog_list" ("parentid", "siteid", "start");

CREATE INDEX "idx_mscatli_pid_sid_end" ON "mshop_catalog_list" ("parentid", "siteid", "end");

CREATE INDEX "idx_mscatli_pid_sid_pos" ON "mshop_catalog_list" ("parentid", "siteid", "pos");
