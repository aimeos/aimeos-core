--
-- Customer database definition
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://www.arcavias.com/en/license
--


SET SESSION sql_mode='ANSI';



--
-- Table structure for table `mshop_customer`
--
CREATE TABLE "mshop_customer" (
	-- Unique id of the customer item
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- Label of the customer item
	"label" VARCHAR(255) NOT NULL,
	-- code of the customer
	"code" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- salutation of the customer
	"salutation" varchar(8) NOT NULL,
	-- company of the customer
	"company" varchar(100) NOT NULL,
	-- vatno of the customer
	"vatno" varchar(64) NOT NULL,
	-- title of the customer
	"title" varchar(64) NOT NULL,
	-- firstname of the customer
	"firstname" varchar(64) NOT NULL,
	-- lastname of the customer
	"lastname" varchar(64) NOT NULL,
	-- address1 of the customer
	"address1" varchar(255) NOT NULL,
	-- address2 of the customer
	"address2" varchar(255) NOT NULL,
	-- address3 of the customer
	"address3" varchar(255) NOT NULL,
	-- postal of the customer
	"postal" varchar(16) NOT NULL,
	-- city of the customer
	"city" varchar(255) NOT NULL,
	-- state of the customer
	"state" varchar(255) NOT NULL,
	-- langid of the customer
	"langid" varchar(5) NULL,
	-- countryid of the customer
	"countryid" char(2) NULL,
	-- telephone of the customer
	"telephone" varchar(32) NOT NULL,
	-- email of the customer
	"email" varchar(255) NOT NULL,
	-- telefax of the customer
	"telefax" varchar(255) NOT NULL,
	-- website of the customer
	"website" varchar(255) NOT NULL,
	-- birthday of the customer
	"birthday" date NULL,
	-- user password
	"password" VARCHAR(255) NOT NULL,
	-- Status of the customer item
	"status" SMALLINT NOT NULL,
	-- last verification time
	"vdate" DATE NULL,
	-- creation time
	"ctime" DATETIME NOT NULL,
	-- modification time
	"mtime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_mscus_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mscus_sid_code"
	UNIQUE ("siteid", "code")
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEY "idx_mscus_langid" ON "mshop_customer" ("langid");

CREATE INDEX "idx_mscus_sid_st_ln_fn" ON "mshop_customer" ("siteid", "status", "lastname", "firstname");

CREATE INDEX "idx_mscus_sid_st_ad1_ad2" ON "mshop_customer" ("siteid", "status", "address1", "address2");

CREATE INDEX "idx_mscus_sid_st_post_ci" ON "mshop_customer" ("siteid", "status", "postal", "city");

CREATE INDEX "idx_mscus_sid_lastname" ON "mshop_customer" ("siteid", "lastname");

CREATE INDEX "idx_mscus_sid_lastname" ON "mshop_customer" ("siteid", "address1");

CREATE INDEX "idx_mscus_sid_lastname" ON "mshop_customer" ("siteid", "city");

CREATE INDEX "idx_mscus_sid_lastname" ON "mshop_customer" ("siteid", "postal");

CREATE INDEX "idx_mscus_sid_lastname" ON "mshop_customer" ("siteid", "email");


--
-- Table structure for table `mshop_customer_address`
--
CREATE TABLE "mshop_customer_address" (
	-- Unique address id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- reference id for customer
	"refid" INTEGER NOT NULL,
	-- company name
	"company" VARCHAR(100) NOT NULL,
	-- vatno
	"vatno" VARCHAR(16) NOT NULL,
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
CONSTRAINT "pk_mscusad_id"
	PRIMARY KEY ("id"),
CONSTRAINT "fk_mscusad_refid"
	FOREIGN KEY ("refid")
	REFERENCES "mshop_customer" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEY "idx_mscusad_langid" ON "mshop_customer_address" ("langid");

CREATE INDEX "idx_mscusad_sid_ln_fn" ON "mshop_customer_address" ("siteid", "lastname", "firstname");

CREATE INDEX "idx_mscusad_sid_ad1_ad2" ON "mshop_customer_address" ("siteid", "address1", "address2");

CREATE INDEX "idx_mscusad_sid_post_ci" ON "mshop_customer_address" ("siteid", "postal", "city");

CREATE INDEX "idx_mscusad_sid_rid" ON "mshop_customer_address" ("siteid", "refid");

CREATE INDEX "idx_mscusad_sid_lastname" ON "mshop_customer_address" ("siteid", "lastname");

CREATE INDEX "idx_mscusad_sid_postal" ON "mshop_customer_address" ("siteid", "postal");

CREATE INDEX "idx_mscusad_sid_city" ON "mshop_customer_address" ("siteid", "city");

CREATE INDEX "idx_mscusad_sid_addr1" ON "mshop_customer_address" ("siteid", "address1");

CREATE INDEX "idx_mscusad_sid_rid" ON "mshop_customer_address" ("siteid", "email");


--
-- Table structure for table `mshop_customer_list_type`
--

CREATE TABLE "mshop_customer_list_type" (
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
CONSTRAINT "pk_mscuslity_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mscuslity_sid_dom_code"
	UNIQUE ("siteid", "domain", "code")
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mscuslity_sid_status" ON "mshop_customer_list_type" ("siteid", "status");

CREATE INDEX "idx_mscuslity_sid_label" ON "mshop_customer_list_type" ("siteid", "label");

CREATE INDEX "idx_mscuslity_sid_code" ON "mshop_customer_list_type" ("siteid", "code");


--
-- Table structure for table `mshop_customer_list`
--

CREATE TABLE "mshop_customer_list" (
	-- Unique list id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- text id (parent id)
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
	-- Configuration
	"config" TEXT NOT NULL,
	-- Precedence rating
	"pos" INTEGER NOT NULL,
	-- status code (0=hidden, 1=display, >1 for anything special)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_mscusli_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mscusli_sid_dm_rid_tid_pid"
	UNIQUE ("siteid", "domain", "refid", "typeid", "parentid"),
CONSTRAINT "fk_mscusli_pid"
	FOREIGN KEY ("parentid")
	REFERENCES "mshop_customer" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mscusli_typeid"
	FOREIGN KEY ( "typeid" )
	REFERENCES "mshop_customer_list_type" ("id")
	ON DELETE CASCADE
	ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mscusli_sid_stat_start_end" ON "mshop_customer_list" ("siteid", "status", "start", "end");

CREATE INDEX "idx_mscusli_pid_sid_rid_dom_tid" ON "mshop_customer_list" ("parentid", "siteid", "refid", "domain", "typeid");

CREATE INDEX "idx_mscusli_pid_sid_start" ON "mshop_customer_list" ("parentid", "siteid", "start");

CREATE INDEX "idx_mscusli_pid_sid_end" ON "mshop_customer_list" ("parentid", "siteid", "end");

CREATE INDEX "idx_mscusli_pid_sid_pos" ON "mshop_customer_list" ("parentid", "siteid", "pos");

CREATE INDEX "idx_mscusli_pid_sid_tid" ON "mshop_customer_list" ("parentid", "siteid", "typeid");
