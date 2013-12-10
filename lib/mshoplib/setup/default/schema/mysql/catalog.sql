--
-- Category database definitions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://www.arcavias.com/en/license
-- $Id: catalog.sql 14518 2011-12-21 09:30:48Z sneubert $
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
CONSTRAINT "fk_mscat_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
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
	UNIQUE ("siteid", "domain", "code"),
CONSTRAINT "fk_mscatlity_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
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
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_mscatli_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mscatli_sid_pid_dm_rid_tid"
	UNIQUE ("siteid", "parentid", "domain", "refid", "typeid"),
CONSTRAINT "fk_mscatli_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
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

CREATE INDEX "idx_mscatli_sid_start_end" ON "mshop_catalog_list" ("siteid", "start", "end");

CREATE INDEX "idx_mscatli_sid_rid_dom_tid" ON "mshop_catalog_list" ("siteid", "refid", "domain", "typeid");

CREATE INDEX "idx_mscatli_pid_sid_rid" ON "mshop_catalog_list" ("parentid", "siteid", "refid");

CREATE INDEX "idx_mscatli_pid_sid_start" ON "mshop_catalog_list" ("parentid", "siteid", "start");

CREATE INDEX "idx_mscatli_pid_sid_end" ON "mshop_catalog_list" ("parentid", "siteid", "end");

CREATE INDEX "idx_mscatli_pid_sid_pos" ON "mshop_catalog_list" ("parentid", "siteid", "pos");


--
-- Table structures for default indexer
--

CREATE TABLE "mshop_catalog_index_catalog" (
	-- Product id
	"prodid" INTEGER NOT NULL,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- catalog node
	"catid" INTEGER NOT NULL,
	-- catalog list type
	"listtype" VARCHAR(32) NOT NULL,
	-- product position
	"pos" INTEGER NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "fk_mscatinca_prodid"
	FOREIGN KEY ("prodid")
	REFERENCES "mshop_product" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mscatinca_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mscatinca_catid"
	FOREIGN KEY ("catid")
	REFERENCES "mshop_catalog" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mscatinca_s_ca_lt_po" ON "mshop_catalog_index_catalog" ("siteid", "catid", "listtype", "pos");

CREATE INDEX "idx_mscatinca_p_s_ca_lt_po" ON "mshop_catalog_index_catalog" ("prodid", "siteid", "catid", "listtype", "pos");


CREATE TABLE "mshop_catalog_index_price" (
	-- Product id
	"prodid" INTEGER NOT NULL,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- price id
	"priceid" INTEGER NULL,
	-- product list type
	"listtype" VARCHAR(32) NOT NULL,
	-- price type
	"type" VARCHAR(32) NOT NULL,
	-- Currency id
	"currencyid" CHAR(3) DEFAULT NULL,
	-- price value
	"value" DECIMAL(12,2) NOT NULL,
	-- price value
	"shipping" DECIMAL(12,2) NOT NULL,
	-- price value
	"rebate" DECIMAL(12,2) NOT NULL,
	-- price value
	"taxrate" DECIMAL(12,2) NOT NULL,
	-- Amount of products
	"quantity" INTEGER NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "fk_mscatinpr_prodid"
	FOREIGN KEY ("prodid")
	REFERENCES "mshop_product" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mscatinpr_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mscatinpr_priceid"
	FOREIGN KEY ("priceid")
	REFERENCES "mshop_price" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mscatinpr_curid"
	FOREIGN KEY ("currencyid")
	REFERENCES "mshop_locale_currency" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mscatinpr_s_lt_cu_ty_va" ON "mshop_catalog_index_price" ("siteid", "listtype", "currencyid", "type", "value");

CREATE INDEX "idx_mscatinpr_p_s_lt_cu_ty_va" ON "mshop_catalog_index_price" ("prodid", "siteid", "listtype", "currencyid", "type", "value");


CREATE TABLE "mshop_catalog_index_text" (
	-- Product id
	"prodid" INTEGER NOT NULL,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- text id
	"textid" INTEGER NULL,
	-- Language id
	"langid" VARCHAR(5) DEFAULT NULL,
	-- product list type
	"listtype" VARCHAR(32) NOT NULL,
	-- text type
	"type" VARCHAR(32) NOT NULL,
	-- domain of text
	"domain" VARCHAR(32) NOT NULL,
	-- text value
	"value" TEXT NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "fk_mscatinte_prodid"
	FOREIGN KEY ("prodid")
	REFERENCES "mshop_product" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mscatinte_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mscatinte_textid"
	FOREIGN KEY ("textid")
	REFERENCES "mshop_text" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mscatinte_langid"
	FOREIGN KEY ("langid")
	REFERENCES "mshop_locale_language" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE FULLTEXT INDEX "idx_mscatinte_value" ON "mshop_catalog_index_text" ("value");

CREATE INDEX "idx_mscatinte_p_s_lt_la_ty_va" ON "mshop_catalog_index_text" ("prodid", "siteid", "listtype", "langid", "type", "value"(16));


CREATE TABLE "mshop_catalog_index_attribute" (
	-- Product id
	"prodid" INTEGER NOT NULL,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- attribute ID
	"attrid" INTEGER NULL,
	-- product list type
	"listtype" VARCHAR(32) NOT NULL,
	-- attribute type
	"type" VARCHAR(32) NOT NULL,
	-- attribute code
	"code" VARCHAR(32) NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "fk_mscatinat_prodid"
	FOREIGN KEY ("prodid")
	REFERENCES "mshop_product" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mscatinat_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mscatinat_attrid"
	FOREIGN KEY ("attrid")
	REFERENCES "mshop_attribute" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mscatinat_s_at_lt" ON "mshop_catalog_index_attribute" ("siteid", "attrid", "listtype");

CREATE INDEX "idx_mscatinat_p_s_at_lt" ON "mshop_catalog_index_attribute" ("prodid", "siteid", "attrid", "listtype");

CREATE INDEX "idx_mscatinat_p_s_lt_t_c" ON "mshop_catalog_index_attribute" ("prodid", "siteid", "listtype", "type", "code");
