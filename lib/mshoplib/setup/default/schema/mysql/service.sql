--
-- Service database definitions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://www.arcavias.com/en/license
--


SET SESSION sql_mode='ANSI';



--
-- Table structure for table `mshop_service_type`
--

CREATE TABLE "mshop_service_type" (
	-- Unique id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- Domain
	"domain" VARCHAR(32) NOT NULL,
	-- code
	"code"  VARCHAR(32) NOT NULL COLLATE utf8_bin,
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
CONSTRAINT "pk_msserty_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msserty_sid_dom_code"
	UNIQUE ("siteid", "domain", "code"),
CONSTRAINT "fk_msserty_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msserty_sid_status" ON "mshop_service_type" ("siteid", "status");

CREATE INDEX "idx_msserty_sid_code" ON "mshop_service_type" ("siteid", "code");

CREATE INDEX "idx_msserty_sid_label" ON "mshop_service_type" ("siteid", "label");


--
-- Service items
--

CREATE TABLE "mshop_service" (
	-- Unique id of the service item
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- Position for sorting
	"pos" INTEGER NOT NULL,
	-- Service type id
	"typeid" INTEGER NOT NULL,
	-- External service code
	"code" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Name of the service
	"label" VARCHAR(255) NOT NULL,
	-- Provider class name
	"provider" VARCHAR(255) NOT NULL,
	-- Configuration
	"config" TEXT NOT NULL,
	-- Status (0=disabled, 1=enabled, >1 for special)
	"status" SMALLINT NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msser_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msser_siteid_typeid_code"
	UNIQUE ("siteid", "typeid", "code"),
CONSTRAINT "fk_msser_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mstyp_typeid"
	FOREIGN KEY ("typeid")
	REFERENCES "mshop_service_type" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_msser_sid_status" ON "mshop_service" ("siteid", "status");

CREATE INDEX "idx_msser_sid_prov" ON "mshop_service" ("siteid", "provider");

CREATE INDEX "idx_msser_sid_code" ON "mshop_service" ("siteid", "code");

CREATE INDEX "idx_msser_sid_label" ON "mshop_service" ("siteid", "label");

CREATE INDEX "idx_msser_sid_pos" ON "mshop_service" ("siteid", "pos");

CREATE INDEX "idx_msser_sid_ctime" ON "mshop_service" ("siteid", "ctime");

CREATE INDEX "idx_msser_sid_mtime" ON "mshop_service" ("siteid", "mtime");

CREATE INDEX "idx_msser_sid_editor" ON "mshop_service" ("siteid", "editor");


--
-- Table structure for table `mshop_service_list_type`
--

CREATE TABLE "mshop_service_list_type" (
	-- Unique id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- Domain
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
CONSTRAINT "pk_msserlity_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msserlity_sid_dom_code"
	UNIQUE ("siteid", "domain", "code"),
CONSTRAINT "fk_msserlity_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msserlity_sid_status" ON "mshop_service_list_type" ("siteid", "status");

CREATE INDEX "idx_msserlity_sid_label" ON "mshop_service_list_type" ("siteid", "label");

CREATE INDEX "idx_msserlity_sid_code" ON "mshop_service_list_type" ("siteid", "code");


--
-- Table structure for table `mshop_service_list`
--

CREATE TABLE "mshop_service_list" (
	-- Unique list id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- service tree id (parent id)
	"parentid" INTEGER NOT NULL,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- typeid, reference to mshop_service_lis_type.id
	"typeid" INTEGER NOT NULL,
	-- domain (e.g.: text, media)
	"domain" VARCHAR(32) NOT NULL,
	-- Reference of the object in given domain
	"refid" VARCHAR(32) NOT NULL,
	-- Valid from
	"start" DATETIME DEFAULT NULL,
	-- Valid until
	"end" DATETIME DEFAULT NULL,
	-- Configuration
	"config" TEXT NOT NULL,
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
CONSTRAINT "pk_msserviceli_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msserli_sid_pid_dm_rid_tid"
	UNIQUE ("siteid", "parentid", "domain", "refid", "typeid"),
CONSTRAINT "fk_msserli_pid"
	FOREIGN KEY ("parentid")
	REFERENCES "mshop_service" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msserli_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msserli_typeid"
	FOREIGN KEY ( "typeid" )
	REFERENCES "mshop_service_list_type" ("id")
	ON DELETE CASCADE
	ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msserli_sid_stat_start_end" ON "mshop_service_list" ("siteid", "status", "start", "end");

CREATE INDEX "idx_msserli_sid_rid_dom_tid" ON "mshop_service_list" ("siteid", "refid", "domain", "typeid");

CREATE INDEX "idx_msserli_pid_sid_rid" ON "mshop_service_list" ("parentid", "siteid", "refid");

CREATE INDEX "idx_msserli_pid_sid_start" ON "mshop_service_list" ("parentid", "siteid", "start");

CREATE INDEX "idx_msserli_pid_sid_end" ON "mshop_service_list" ("parentid", "siteid", "end");

CREATE INDEX "idx_msserli_pid_sid_pos" ON "mshop_service_list" ("parentid", "siteid", "pos");
