--
-- Product database definitions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://www.arcavias.com/en/license
-- $Id: product.sql 14456 2011-12-19 16:18:24Z fblasel $
--


SET SESSION sql_mode='ANSI';


--
-- Table structure for table `mshop_product_type`
--

CREATE TABLE "mshop_product_type" (
	-- Unique id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- domain
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
CONSTRAINT "pk_msproty_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msproty_sid_dom_code"
	UNIQUE ("siteid", "domain", "code"),
CONSTRAINT "fk_msproty_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msproty_sid_status" ON "mshop_product_type" ("siteid", "status");

CREATE INDEX "idx_msproty_sid_code" ON "mshop_product_type" ("siteid", "code");

CREATE INDEX "idx_msproty_sid_label" ON "mshop_product_type" ("siteid", "label");


--
-- Central product repository
--

CREATE TABLE "mshop_product" (
	-- Unique id of the product
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- Product type ID
	"typeid" INTEGER NOT NULL,
	-- User product code
	"code" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Supplier code
	"suppliercode" VARCHAR(32) NOT NULL,
	-- Default name in admin backend
	"label" VARCHAR(255) NOT NULL,
	-- Date and time the product should be activated
	"start" DATETIME DEFAULT NULL,
	-- Date and time the product should be deactivated
	"end" DATETIME DEFAULT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
	-- entry status (0=hidden, 1=displayed, >1 for special)
	"status" SMALLINT NOT NULL DEFAULT 0,
CONSTRAINT "pk_mspro_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mspro_siteid_code"
	UNIQUE ("siteid", "code"),
CONSTRAINT "fk_mspro_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mspro_typeid"
	FOREIGN KEY ("typeid")
	REFERENCES "mshop_product_type" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_mspro_sid_stat_st_end" ON "mshop_product" ("siteid", "status", "start", "end");

CREATE INDEX "idx_mspro_id_sid_stat_st_end" ON "mshop_product" ("id", "siteid", "status", "start", "end");

CREATE INDEX "idx_mspro_sid_label" ON "mshop_product" ("siteid", "label");

CREATE INDEX "idx_mspro_sid_supplier" ON "mshop_product" ("siteid", "suppliercode");

CREATE INDEX "idx_mspro_sid_start" ON "mshop_product" ("siteid", "start");

CREATE INDEX "idx_mspro_sid_end" ON "mshop_product" ("siteid", "end");

CREATE INDEX "idx_mspro_sid_ctime" ON "mshop_product" ("siteid", "ctime");

CREATE INDEX "idx_mspro_sid_mtime" ON "mshop_product" ("siteid", "mtime");

CREATE INDEX "idx_mspro_sid_editor" ON "mshop_product" ("siteid", "editor");


--
-- Table structure for table `mshop_product_list_type`
--

CREATE TABLE "mshop_product_list_type" (
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
CONSTRAINT "pk_msprolity_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msprolity_sid_dom_code"
	UNIQUE ("siteid", "domain", "code"),
CONSTRAINT "fk_msprolity_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msprolity_sid_status" ON "mshop_product_list_type" ("siteid", "status");

CREATE INDEX "idx_msprolity_sid_label" ON "mshop_product_list_type" ("siteid", "label");

CREATE INDEX "idx_msprolity_sid_code" ON "mshop_product_list_type" ("siteid", "code");


--
-- Product list
--

CREATE TABLE "mshop_product_list" (
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
	-- Position of the list element reagrding to the domain and the prodid
	"pos" INTEGER NOT NULL DEFAULT 0,
	-- status code (0=hidden, 1=display, >1 for anything special)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msproli_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msproli_sid_pid_dm_rid_tid"
	UNIQUE ("siteid", "parentid", "domain", "refid", "typeid"),
CONSTRAINT "fk_msproli_pid"
	FOREIGN KEY ("parentid")
	REFERENCES "mshop_product" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msproli_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msproli_typeid"
	FOREIGN KEY ( "typeid" )
	REFERENCES "mshop_product_list_type" ("id")
	ON DELETE CASCADE
	ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msproli_sid_start_end" ON "mshop_product_list" ("siteid", "start", "end");

CREATE INDEX "idx_msproli_sid_rid_dom_tid" ON "mshop_product_list" ("siteid", "refid", "domain", "typeid");

CREATE INDEX "idx_msproli_pid_sid_rid" ON "mshop_product_list" ("parentid", "siteid", "refid");

CREATE INDEX "idx_msproli_pid_sid_start" ON "mshop_product_list" ("parentid", "siteid", "start");

CREATE INDEX "idx_msproli_pid_sid_end" ON "mshop_product_list" ("parentid", "siteid", "end");

CREATE INDEX "idx_msproli_pid_sid_pos" ON "mshop_product_list" ("parentid", "siteid", "pos");


--
-- Table structure for table `mshop_product_tag_type`
--

CREATE TABLE "mshop_product_tag_type" (
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
CONSTRAINT "pk_msprotaty_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msprotaty_sid_dom_code"
	UNIQUE ("siteid", "domain", "code"),
CONSTRAINT "fk_msprotaty_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msprotaty_sid_status" ON "mshop_product_tag_type" ("siteid", "status");

CREATE INDEX "idx_msprotaty_sid_label" ON "mshop_product_tag_type" ("siteid", "label");

CREATE INDEX "idx_msprotaty_sid_code" ON "mshop_product_tag_type" ("siteid", "code");


--
-- Product tags
--

CREATE TABLE "mshop_product_tag" (
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
CONSTRAINT "pk_msprota_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msprota_sid_tid_lid_label"
	UNIQUE ("siteid", "typeid", "langid", "label"),
CONSTRAINT "fk_msprota_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msprota_typeid"
	FOREIGN KEY ("typeid")
	REFERENCES "mshop_product_tag_type" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_msprota_sid_label" ON "mshop_product_tag" ("siteid", "label");

CREATE INDEX "idx_msprota_sid_langid" ON "mshop_product_tag" ("siteid", "langid");


--
-- Product warehouse
--

CREATE TABLE "mshop_product_stock_warehouse" (
	-- unique id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id
	"siteid" INTEGER NOT NULL,
	-- default name in admin backend
	"code" VARCHAR(255)  NOT NULL COLLATE utf8_bin,
	-- Label of the warehouse
	"label" VARCHAR(255) NOT NULL,
	-- Status (0=disabled, 1=enabled, >1 for special)
	"status" SMALLINT NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msprostwa_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msprostwa_sid_code"
	UNIQUE ("siteid", "code"),
CONSTRAINT "fk_msprostwa_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msprostwa_sid_label" ON "mshop_product_stock_warehouse" ("siteid", "label");

CREATE INDEX "idx_msprostwa_sid_ctime" ON "mshop_product_stock_warehouse" ("siteid", "ctime");

CREATE INDEX "idx_msprostwa_sid_mtime" ON "mshop_product_stock_warehouse" ("siteid", "mtime");

CREATE INDEX "idx_msprostwa_sid_editor" ON "mshop_product_stock_warehouse" ("siteid", "editor");



--
-- Stock level for products
--

CREATE TABLE "mshop_product_stock" (
	-- unique id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- product id
	"prodid" INTEGER NOT NULL,
	-- site id
	"siteid" INTEGER NOT NULL,
	-- warehouse id
	"warehouseid" INTEGER DEFAULT NULL,
	-- available amount in stock
	"stocklevel" INTEGER DEFAULT NULL,
	-- product back in stock
	"backdate" DATETIME DEFAULT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msprost_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msprost_sid_pid_wid"
	UNIQUE ("siteid", "prodid", "warehouseid"),
CONSTRAINT "fk_msprost_prodid"
	FOREIGN KEY ("prodid")
	REFERENCES "mshop_product" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msprost_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msprost_stock_warehouseid"
	FOREIGN KEY ("warehouseid")
	REFERENCES "mshop_product_stock_warehouse" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msprost_sid_stocklevel" ON "mshop_product_stock" ("siteid", "stocklevel");

CREATE INDEX "idx_msprost_sid_backdate" ON "mshop_product_stock" ("siteid", "backdate");

CREATE INDEX "idx_msprost_sid_ctime" ON "mshop_product_stock" ("siteid", "ctime");

CREATE INDEX "idx_msprost_sid_mtime" ON "mshop_product_stock" ("siteid", "mtime");

CREATE INDEX "idx_msprost_sid_editor" ON "mshop_product_stock" ("siteid", "editor");
