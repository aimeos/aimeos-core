--
-- Price database definitions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://www.arcavias.com/en/license
-- $Id: price.sql 14277 2011-12-12 11:28:56Z spopp $
--


SET SESSION sql_mode='ANSI';


--
-- Table structure for table "mshop_price_type"
--

CREATE TABLE "mshop_price_type" (
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
CONSTRAINT "pk_msprity_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msprity_sid_dom_code"
	UNIQUE ("siteid", "domain", "code"),
CONSTRAINT "fk_msprity_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msprity_sid_status" ON "mshop_price_type" ("siteid", "status");

CREATE INDEX "idx_msprity_sid_label" ON "mshop_price_type" ("siteid", "label");

CREATE INDEX "idx_msprity_sid_code" ON "mshop_price_type" ("siteid", "code");


--
-- Table structure for table `mshop_price_list_type`
--

CREATE TABLE "mshop_price_list_type" (
	-- Unique id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
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
CONSTRAINT "pk_msprility_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msprility_sid_dom_code"
	UNIQUE ("siteid", "domain", "code"),
CONSTRAINT "fk_msprility_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msprility_sid_status" ON "mshop_price_list_type" ("siteid", "status");

CREATE INDEX "idx_msprility_sid_label" ON "mshop_price_list_type" ("siteid", "label");

CREATE INDEX "idx_msprility_sid_code" ON "mshop_price_list_type" ("siteid", "code");


--
-- Table structure for table "mshop_price"
--

CREATE TABLE "mshop_price" (
 	-- Price id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id
	"siteid" INTEGER NOT NULL,
	-- type id
	"typeid" INTEGER NOT NULL,
	-- Domain the price is associated to
	"domain" VARCHAR(32) NOT NULL,
	-- Name of the price
	"label" VARCHAR(255) NOT NULL,
	-- currency this product price is available for
	"currencyid" CHAR(3) NOT NULL,
	-- Amount of products
	"quantity" INTEGER NOT NULL,
	-- Price of the product
	"value" DECIMAL(12,2) NOT NULL,
	-- Additional shipping costs
	"costs" DECIMAL(12,2) NOT NULL,
	-- Granted rebate
	"rebate" DECIMAL(12,2) NOT NULL,
	-- tax rate in percent
	"taxrate" DECIMAL(5,2) NOT NULL,
	-- entry status (0=disabled, 1=enabled, >1 for special)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_mspri_id"
	PRIMARY KEY ("id"),
CONSTRAINT "fk_mspri_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mspri_typeid"
	FOREIGN KEY ("typeid")
	REFERENCES "mshop_price_type" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mspri_curid"
	FOREIGN KEY ("currencyid")
	REFERENCES "mshop_locale_currency" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_mspri_sid_status_currencyid" ON "mshop_price" ("siteid", "status", "currencyid");

CREATE INDEX "idx_mspri_sid_dom_currid" ON "mshop_price" ("siteid", "domain", "currencyid");

CREATE INDEX "idx_mspri_sid_dom_quantity" ON "mshop_price" ("siteid", "domain", "quantity");

CREATE INDEX "idx_mspri_sid_dom_value" ON "mshop_price" ("siteid", "domain", "value");

CREATE INDEX "idx_mspri_sid_dom_costs" ON "mshop_price" ("siteid", "domain", "costs");

CREATE INDEX "idx_mspri_sid_dom_rebate" ON "mshop_price" ("siteid", "domain", "rebate");

CREATE INDEX "idx_mspri_sid_dom_taxrate" ON "mshop_price" ("siteid", "domain", "taxrate");

CREATE INDEX "idx_mspri_sid_dom_mtime" ON "mshop_price" ("siteid", "domain", "mtime");

CREATE INDEX "idx_mspri_sid_dom_ctime" ON "mshop_price" ("siteid", "domain", "ctime");

CREATE INDEX "idx_mspri_sid_dom_editor" ON "mshop_price" ("siteid", "domain", "editor");

CREATE INDEX "idx_mspri_sid_domain" ON "mshop_price" ("siteid", "domain");

CREATE INDEX "idx_mspri_sid_label" ON "mshop_price" ("siteid", "label");


--
-- Table structure for table `mshop_price_list`
--

CREATE TABLE "mshop_price_list" (
	-- Unique list id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- price id (parent id)
	"parentid" INTEGER NOT NULL,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- typeid
	"typeid" INTEGER NOT NULL,
	-- domain (e.g.: text, attribute)
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
CONSTRAINT "pk_msprili_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msprili_sid_pid_dm_rid_tid"
	UNIQUE ("siteid", "parentid", "domain", "refid", "typeid"),
CONSTRAINT "fk_msprili_pid"
	FOREIGN KEY ("parentid")
	REFERENCES "mshop_price" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msprili_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msprili_typeid"
	FOREIGN KEY ( "typeid" )
	REFERENCES "mshop_price_list_type" ("id")
	ON DELETE CASCADE
	ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msprili_sid_stat_start_end" ON "mshop_price_list" ("siteid", "status", "start", "end");

CREATE INDEX "idx_msprili_sid_rid_dom_tid" ON "mshop_price_list" ("siteid", "refid", "domain", "typeid");

CREATE INDEX "idx_msprili_pid_sid_rid" ON "mshop_price_list" ("parentid", "siteid", "refid");

CREATE INDEX "idx_msprili_pid_sid_start" ON "mshop_price_list" ("parentid", "siteid", "start");

CREATE INDEX "idx_msprili_pid_sid_end" ON "mshop_price_list" ("parentid", "siteid", "end");

CREATE INDEX "idx_msprili_pid_sid_pos" ON "mshop_price_list" ("parentid", "siteid", "pos");
