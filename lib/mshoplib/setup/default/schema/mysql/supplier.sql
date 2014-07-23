--
-- Supplier database definitions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://www.arcavias.com/en/license
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
-- Table structure for table `mshop_supplier_address`
--
CREATE TABLE "mshop_supplier_address" (
	-- Unique address id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- reference id for supplier
	"refid" INTEGER NOT NULL,
	-- company name
	"company" VARCHAR(100) NOT NULL,
	-- vatno
	"vatno" VARCHAR(64) NOT NULL,
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
CONSTRAINT "fk_mssupad_refid"
	FOREIGN KEY ("refid")
	REFERENCES "mshop_supplier" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEY "idx_mssupad_langid" ON "mshop_supplier_address" ("langid");

CREATE INDEX "idx_mssupad_sid_rid" ON "mshop_supplier_address" ("siteid", "refid");
