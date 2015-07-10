--
-- Category database definitions
--
-- Copyright (c) Metaways Infosystems GmbH, 2014
-- License LGPLv3, http://opensource.org/licenses/LGPL-3.0
--


SET SESSION sql_mode='ANSI';



--
-- Table structures for default indexer
--


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
CONSTRAINT "unq_mscatinat_p_s_aid_lt"
	UNIQUE ("prodid", "siteid", "attrid", "listtype")
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mscatinat_s_at_lt" ON "mshop_catalog_index_attribute" ("siteid", "attrid", "listtype");

CREATE INDEX "idx_mscatinat_p_s_lt_t_c" ON "mshop_catalog_index_attribute" ("prodid", "siteid", "listtype", "type", "code");


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
CONSTRAINT "unq_mscatinca_p_s_cid_lt_po"
	UNIQUE ("prodid", "siteid", "catid", "listtype", "pos")
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mscatinca_s_ca_lt_po" ON "mshop_catalog_index_catalog" ("siteid", "catid", "listtype", "pos");


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
	"costs" DECIMAL(12,2) NOT NULL,
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
CONSTRAINT "unq_mscatinpr_p_s_prid_lt"
	UNIQUE ("prodid", "siteid", "priceid", "listtype")
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
CONSTRAINT "unq_mscatinte_p_s_tid_lt"
	UNIQUE ("prodid", "siteid", "textid", "listtype")
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE FULLTEXT INDEX "idx_mscatinte_value" ON "mshop_catalog_index_text" ("value");

CREATE INDEX "idx_mscatinte_sid" ON "mshop_catalog_index_text" ("siteid");

CREATE INDEX "idx_mscatinte_p_s_lt_la_ty_va" ON "mshop_catalog_index_text" ("prodid", "siteid", "listtype", "langid", "type", "value"(16));
