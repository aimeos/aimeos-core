--
-- Order database definitions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://www.arcavias.com/en/license
--


SET SESSION sql_mode='ANSI';



--
-- Orders by customers
--

CREATE TABLE "mshop_order_base" (
	-- Unique id of the order
	"id" BIGINT NOT NULL AUTO_INCREMENT,
	-- Site ID
	"siteid" INTEGER NULL,
	-- Customer who ordered
	"customerid" VARCHAR(32) NOT NULL,
	-- site code
	"sitecode" VARCHAR(32) DEFAULT NULL,
	-- ISO language code
	"langid" VARCHAR(5) NOT NULL,
	-- ISO code of the currency
	"currencyid" CHAR(3) NOT NULL,
	-- total price
	"price" DECIMAL(12,2) NOT NULL,
	-- shipping costs
	"costs" DECIMAL(12,2) NOT NULL,
	-- amount of rebate
	"rebate" DECIMAL(12,2) NOT NULL,
	-- Comment string
	"comment" TEXT NOT NULL DEFAULT '',
	-- entry status (0=disabled, 1=enabled)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Timestamp of the last update
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msordba_id"
	PRIMARY KEY ("id")
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_msordba_scode_custid" ON "mshop_order_base" ("sitecode", "customerid");

CREATE INDEX "idx_msordba_sid_custid" ON "mshop_order_base" ("siteid", "customerid");


--
-- Order invoice and status
--

CREATE TABLE "mshop_order" (
	-- Unique billing id
	"id" BIGINT NOT NULL AUTO_INCREMENT,
	-- ID from mshop_order_base
	"baseid" BIGINT NOT NULL,
	-- Site ID
	"siteid" INTEGER NULL,
	-- Type of order (0=continuity, 1=web, 2=phone)
	"type" VARCHAR(8) NOT NULL,
	-- Purchase date
	"datepayment" DATETIME NOT NULL,
	-- Delivery date
	"datedelivery" DATETIME DEFAULT NULL,
	-- Status of payment (-1=unfinished, 0=deleted, 1=cancelled, 2=refused, 3=pending, 4=accepted, 5=refund)
	"statuspayment" SMALLINT NOT NULL DEFAULT -1,
	-- Status of delivery (-1=unfinished, 0=deleted, 1=pending, 2=in progress, 3=dispatched, 4=delivered, 5=lost, 6=refused, 7=returned)
	"statusdelivery" SMALLINT NOT NULL DEFAULT -1,
	-- Related order id
	"relatedid" BIGINT DEFAULT NULL,
	-- Timestamp of the last update
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msord_id"
	PRIMARY KEY ("id"),
CONSTRAINT "fk_msord_baseid"
	FOREIGN KEY ("baseid")
	REFERENCES "mshop_order_base" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_msord_sid_mtime_pstat" ON "mshop_order" ("siteid", "mtime", "statuspayment");

CREATE INDEX "idx_msord_sid_mtime_dstat" ON "mshop_order" ("siteid", "mtime", "statusdelivery");

CREATE INDEX "idx_msord_sid_type" ON "mshop_order" ("siteid", "type");

CREATE INDEX "idx_msord_sid_pdate" ON "mshop_order" ("siteid", "datepayment");

CREATE INDEX "idx_msord_sid_ddate" ON "mshop_order" ("siteid", "datedelivery");

CREATE INDEX "idx_msord_sid_dstatus" ON "mshop_order" ("siteid", "statusdelivery");

CREATE INDEX "idx_msord_sid_ctime" ON "mshop_order" ("siteid", "ctime");

CREATE INDEX "idx_msord_sid_editor" ON "mshop_order" ("siteid", "editor");


--
-- Addresses of customers (billing and delivery)
--

CREATE TABLE "mshop_order_base_address" (
	-- Unique order address id
	"id" BIGINT NOT NULL AUTO_INCREMENT,
	-- IDfrom mshop_order_base
	"baseid" BIGINT NOT NULL,
	-- Site ID
	"siteid" INTEGER NULL,
	-- Original address ID
	"addrid" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Type of the address
	"type" VARCHAR(8) NOT NULL,
	-- company name
	"company" VARCHAR(100) NOT NULL,
	-- customer categorization
	"salutation" VARCHAR(8) NOT NULL,
	-- title of the customer
	"title" VARCHAR(64) NOT NULL,
	-- first name of customer
	"firstname" VARCHAR(64) NOT NULL,
	-- last name of customer
	"lastname" VARCHAR(64) NOT NULL,
	-- Depending on country, e.g. house name
	"address1" VARCHAR(255) NOT NULL,
	-- Depending on country, e.g. street
	"address2" VARCHAR(255) NOT NULL,
	-- Depending on country, e.g. county/suburb
	"address3" VARCHAR(255) NOT NULL,
	-- postal code of customer
	"postal" VARCHAR(16) NOT NULL,
	-- city name of customer
	"city" VARCHAR(255) NOT NULL,
	-- state name of customer
	"state" VARCHAR(255) NOT NULL,
	-- Country id the customer is living in
	"countryid" CHAR(2) NULL,
	-- language id
	"langid" VARCHAR(5) NULL,
	-- Telephone number of the customer
	"telephone" VARCHAR(32) NOT NULL,
	-- Email of the customer
	"email" VARCHAR(255) NOT NULL,
	-- Telefax of the customer
	"telefax" VARCHAR(255) NOT NULL,
	-- Website of the customer
	"website" VARCHAR(255) NOT NULL,
	-- Generic flag
	"flag" INTEGER NOT NULL,
	-- Timestamp of the last update
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msordbaad_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msordbaad_bid_type"
	UNIQUE ("baseid", "type"),
CONSTRAINT "fk_msordbaad_baseid"
	FOREIGN KEY ("baseid")
	REFERENCES "mshop_order_base" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_msordbaad_sid_bid_typ" ON "mshop_order_base_address" ("siteid", "baseid", "type");

CREATE INDEX "idx_msordbaad_bid_sid_lname" ON "mshop_order_base_address" ("baseid", "siteid", "lastname");

CREATE INDEX "idx_msordbaad_bid_sid_addr1" ON "mshop_order_base_address" ("baseid", "siteid", "address1");

CREATE INDEX "idx_msordbaad_bid_sid_postal" ON "mshop_order_base_address" ("baseid", "siteid", "postal");

CREATE INDEX "idx_msordbaad_bid_sid_city" ON "mshop_order_base_address" ("baseid", "siteid", "city");

CREATE INDEX "idx_msordbaad_bid_sid_email" ON "mshop_order_base_address" ("baseid", "siteid", "email");


--
-- Ordered products by customers
--

CREATE TABLE "mshop_order_base_product" (
	-- Unique id of product within all orders
	"id" BIGINT NOT NULL AUTO_INCREMENT,
	-- ID from mshop_order_base
	"baseid" BIGINT NOT NULL,
	-- Site ID
	"siteid" INTEGER NULL,
	-- Original product ID
	"prodid" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Order Product ID
	"ordprodid" BIGINT DEFAULT NULL,
	-- Type of Order Product ID
	"type" VARCHAR(32) NOT NULL,
	-- Shop product code
	"prodcode" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Supplier id
	"suppliercode" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Product name
	"name" VARCHAR(255) NOT NULL,
	-- Media url (thumbnail)
	"mediaurl" VARCHAR(255) NOT NULL,
	-- Amount of products bought
	"quantity" INTEGER NOT NULL,
	-- Product price of a single product
	"price" DECIMAL(12,2) NOT NULL,
	-- Additional shipping costs
	"costs" DECIMAL(12,2) NOT NULL,
	-- Granted rebate
	"rebate" DECIMAL(12,2) NOT NULL,
	-- tax rate in percent
	"taxrate" DECIMAL(5,2) NOT NULL,
	-- Set flags for this ordered product
	"flags" INTEGER NOT NULL,
	-- Position of ordered product
	"pos" INTEGER NOT NULL,
	-- Delivery status (-1: unknown, 0: deleted, 1: pending, 2: in progress, 3: dispatched, 4: delivered, 5: lost, 6: refused, 7: returned)
	"status" SMALLINT NOT NULL DEFAULT -1,
	-- Timestamp of the last update
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msordbapr_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msordbapr_bid_pos"
	UNIQUE ("baseid", "pos"),
CONSTRAINT "fk_msordbapr_baseid"
	FOREIGN KEY ("baseid")
	REFERENCES "mshop_order_base" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_msordbapr_sid_bid_pcd" ON "mshop_order_base_product" ("siteid", "baseid", "prodcode");


--
-- attributes of ordered products
--

CREATE TABLE "mshop_order_base_product_attr" (
	-- Unique id of ordered product attribute
	"id" BIGINT NOT NULL AUTO_INCREMENT,
	-- Site ID
	"siteid" INTEGER NULL,
	-- Original attribute ID
	"attrid" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Order product id
	"ordprodid" BIGINT NOT NULL,
	-- Attribute type
	"type" VARCHAR(32) NOT NULL,
	-- Attribute code
	"code" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Attribute value
	"value" VARCHAR(255) NOT NULL,
	-- Localized attribute name
	"name" VARCHAR(255) NOT NULL,
	-- Timestamp of the last update
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msordbaprat_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msordbaprat_ordprodid_code"
	UNIQUE ("ordprodid", "code"),
CONSTRAINT "fk_msordbaprat_ordprodid"
	FOREIGN KEY ("ordprodid")
	REFERENCES "mshop_order_base_product" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_msordbaprat_si_oi_ty_cd_va" ON "mshop_order_base_product_attr" ("siteid", "ordprodid", "type", "code", "value");

--
-- Payment details entered by the customers
--

CREATE TABLE "mshop_order_base_service" (
	-- Unique id of ordered service
	"id" BIGINT NOT NULL AUTO_INCREMENT,
	-- ID from mshop_order_base
	"baseid" BIGINT NOT NULL,
	-- Site ID
	"siteid" INTEGER NULL,
	-- Original service ID
	"servid" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Service domain (delivery, payment)
	"type" VARCHAR(8) NOT NULL,
	-- Service code
	"code" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Service name
	"name" VARCHAR(255) NOT NULL,
	-- Media url (thumbnail)
	"mediaurl" VARCHAR(255) NOT NULL,
	-- price of the service
	"price" DECIMAL(12,2) NOT NULL,
	-- shipping costs
	"costs" DECIMAL(12,2) NOT NULL,
	-- amount of rebate
	"rebate" DECIMAL(12,2) NOT NULL,
	-- tax rate in percent
	"taxrate" DECIMAL(5,2) NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msordbase_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msordbase_bid_type_code"
	UNIQUE ("baseid", "type", "code"),
CONSTRAINT "fk_msordbase_baseid"
	FOREIGN KEY ("baseid")
	REFERENCES "mshop_order_base" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_msordbase_sid_bid_cd_typ" ON "mshop_order_base_service" ("siteid", "baseid", "code", "type");

CREATE INDEX "idx_msordbase_sid_code_type" ON "mshop_order_base_service" ("siteid", "code", "type");


--
-- Payment details entered by the customers
--

CREATE TABLE "mshop_order_base_service_attr" (
	-- Unique id of ordered service attribute
	"id" BIGINT NOT NULL AUTO_INCREMENT,
	-- Site ID
	"siteid" INTEGER NULL,
	-- Original attribute ID
	"attrid" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Order service id
	"ordservid" BIGINT NOT NULL,
	-- Attribute type
	"type" VARCHAR(32) NOT NULL,
	-- Attribute code
	"code" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Attribute value
	"value" VARCHAR(255) NOT NULL,
	-- Attribute name
	"name" VARCHAR(255) NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msordbaseat_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msordbaseat_ordservid_code"
	UNIQUE ("ordservid", "code"),
CONSTRAINT "fk_msordbaseat_ordservid"
	FOREIGN KEY ("ordservid")
	REFERENCES "mshop_order_base_service" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_msordbaseat_si_oi_ty_cd_va" ON "mshop_order_base_service_attr" ("siteid", "ordservid", "type", "code", "value");

CREATE INDEX "idx_msordbaseat_si_cd_va" ON "mshop_order_base_service_attr" ("siteid", "code", "value");

--
-- Status of the order
--

CREATE TABLE "mshop_order_status" (
	-- unique id of the order_status
	"id" BIGINT NOT NULL AUTO_INCREMENT,
	-- site id
	"siteid" INTEGER NULL,
	-- parent id
	"parentid" BIGINT NOT NULL,
	-- type
	"type" VARCHAR(32) NOT NULL,
	-- value of the status
	"value" VARCHAR(32) NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msordst_id"
	PRIMARY KEY ("id"),
CONSTRAINT "fk_msordst_parentid"
	FOREIGN KEY ("parentid")
	REFERENCES "mshop_order" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_msordstatus_val_sid" ON "mshop_order_status" ("siteid", "parentid", "type", "value");
