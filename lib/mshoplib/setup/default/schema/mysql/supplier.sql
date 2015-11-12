--
-- Supplier database definitions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://opensource.org/licenses/LGPL-3.0
--


SET SESSION sql_mode='ANSI';



--
-- Table structure for table `mshop_supplier`
--
CREATE TABLE "mshop_supplier" (
	-- Unique id of the supplier item
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- Code of the supplier item
	"code" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Label of the supplier item
	"label" VARCHAR(255) NOT NULL,
	-- Status of the supplier item
	"status" SMALLINT NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_mssup_id"
	PRIMARY KEY ("id")
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_mssup_sid_status" ON "mshop_supplier" ("siteid", "status");


--
-- Table structure for table `mshop_supplier_list_type`
--

CREATE TABLE "mshop_supplier_list_type" (
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
CONSTRAINT "pk_msuplity_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msuplity_sid_dom_code"
	UNIQUE ("siteid", "domain", "code")
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msuplity_sid_status" ON "mshop_supplier_list_type" ("siteid", "status");

CREATE INDEX "idx_msuplity_sid_label" ON "mshop_supplier_list_type" ("siteid", "label");

CREATE INDEX "idxmsuplity_sid_code" ON "mshop_supplier_list_type" ("siteid", "code");


--
-- Product list
--

CREATE TABLE "mshop_supplier_list" (
	-- Unique list id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- Product id
	"parentid" INTEGER NOT NULL,
	-- Site id
	"siteid" INTEGER NOT NULL,
	-- typeid
	"typeid" INTEGER NOT NULL,
	-- list type
	"domain" VARCHAR(32) NOT NULL,
	-- Featured reference
	"refid" VARCHAR(32) NOT NULL,
	-- Valid from
	"start" DATETIME DEFAULT NULL,
	-- Valid until
	"end" DATETIME DEFAULT NULL,
	-- Configuration
	"config" TEXT NOT NULL,
	-- Position of the list element regarding to the domain and the supid
	"pos" INTEGER NOT NULL DEFAULT 0,
	-- status code (0=hidden, 1=display, >1 for anything special)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msupli_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msupli_sid_dm_rid_tid_pid"
	UNIQUE ("siteid", "domain", "refid", "typeid", "parentid"),
CONSTRAINT "fk_msupli_pid"
	FOREIGN KEY ("parentid")
	REFERENCES "mshop_supplier" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msupli_typeid"
	FOREIGN KEY ( "typeid" )
	REFERENCES "mshop_supplier_list_type" ("id")
	ON DELETE CASCADE
	ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msupli_sid_stat_start_end" ON "mshop_supplier_list" ("siteid", "status", "start", "end");

CREATE INDEX "idx_msupli_pid_sid_rid_dom_tid" ON "mshop_supplier_list" ("parentid", "siteid", "refid", "domain", "typeid");

CREATE INDEX "idx_msupli_pid_sid_start" ON "mshop_supplier_list" ("parentid", "siteid", "start");

CREATE INDEX "idx_msupli_pid_sid_end" ON "mshop_supplier_list" ("parentid", "siteid", "end");

CREATE INDEX "idx_msupli_pid_sid_pos" ON "mshop_supplier_list" ("parentid", "siteid", "pos");


--
-- Table structure for table `mshop_supplier_address`
--
CREATE TABLE "mshop_supplier_address" (
	-- Unique address id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- reference id for supplier
	"parentid" INTEGER NOT NULL,
	-- company name
	"company" VARCHAR(100) NOT NULL,
	-- vatid
	"vatid" VARCHAR(32) NOT NULL,
	-- customer/supplier categorization
	"salutation" VARCHAR(8) NOT NULL,
	-- title of the customer/supplier
	"title" VARCHAR(64) NOT NULL,
	-- first name of customer/supplier
	"firstname" VARCHAR(64) NOT NULL,
	-- last name of customer/supplier
	"lastname" VARCHAR(64) NOT NULL,
	-- Depending on country, e.g. house name
	"address1" VARCHAR(255) NOT NULL,
	-- Depending on country, e.g. street
	"address2" VARCHAR(255) NOT NULL,
	-- Depending on country, e.g. county/suburb
	"address3" VARCHAR(255) NOT NULL,
	-- postal code of customer/supplier
	"postal" VARCHAR(16) NOT NULL,
	-- city name of customer/supplier
	"city" VARCHAR(255) NOT NULL,
	-- state name of customer/supplier
	"state" VARCHAR(255) NOT NULL,
	-- language id
	"langid" VARCHAR(5) NULL,
	-- Country id the customer/supplier is living in
	"countryid" CHAR(2) NULL,
	-- Telephone number of the customer/supplier
	"telephone" VARCHAR(32) NOT NULL,
	-- Email of the customer/supplier
	"email" VARCHAR(255) NOT NULL,
	-- Telefax of the customer/supplier
	"telefax" VARCHAR(255) NOT NULL,
	-- Website of the customer/supplier
	"website" VARCHAR(255) NOT NULL,
	-- Generic flag
	"flag" INTEGER NOT NULL,
	-- Position 
	"pos" SMALLINT NOT NULL default 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_mssupad_id"
	PRIMARY KEY ("id"),
CONSTRAINT "fk_mssupad_parentid"
	FOREIGN KEY ("parentid")
	REFERENCES "mshop_supplier" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEY "idx_mssupad_langid" ON "mshop_supplier_address" ("langid");

CREATE INDEX "idx_mssupad_sid_rid" ON "mshop_supplier_address" ("siteid", "parentid");
