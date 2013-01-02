--
-- Locale database definitions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://www.arcavias.com/en/license
-- $Id: locale.sql 14679 2012-01-03 18:16:41Z spopp $
--


SET SESSION sql_mode='ANSI';


--
-- List of shop instances
--

CREATE TABLE "mshop_locale_site" (
	-- unique site id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- parent id
	"parentid" INTEGER NOT NULL,
	-- site code
	"code" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- name of the site
	"label" VARCHAR(255) NOT NULL,
	-- site config values stored in JSON format
	"config" TEXT NOT NULL,
	-- entry status (0=hidden, 1=displayed, >1 for anything special)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- level in nested set
	"level" INTEGER NOT NULL,
	-- left number in nested set
	"nleft" INTEGER NOT NULL,
	-- right number in nested set
	"nright" INTEGER NOT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_mslocsi_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mslocsi_code"
	UNIQUE ("code")
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_mslocsi_nleft_nright" ON "mshop_locale_site" ("nleft", "nright");

CREATE INDEX "idx_mslocsi_level_status" ON "mshop_locale_site" ("level", "status");


--
-- List of available languages
--
CREATE TABLE "mshop_locale_language" (
	-- Unique id of the language
	"id" VARCHAR(5) NOT NULL,
	-- Site ID
	"siteid" INTEGER NULL,
	-- name of the language
	"label" VARCHAR(255) NOT NULL,
	-- entry status (0=hidden, 1=displayed, >1 for special)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_mslocla_id_siteid"
	PRIMARY KEY ("id"),
CONSTRAINT "fk_mslocla_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE SET NULL
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_mslocla_status" ON "mshop_locale_language" ("status");

CREATE INDEX "idx_mslocla_label" ON "mshop_locale_language" ("label");


--
-- List of available currencies
--

CREATE TABLE "mshop_locale_currency" (
	-- ISO code of the currency
	"id" CHAR(3) NOT NULL,
	-- Site ID
	"siteid" INTEGER NULL,
	-- Short name of the currency
	"label" VARCHAR(255) NOT NULL,
	-- Entry status (0=hidden, 1=displayed, >1 for anything special)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msloccu_id_siteid"
	PRIMARY KEY ("id"),
CONSTRAINT "fk_msloccu_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE SET NULL
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_msloccu_sid_status" ON "mshop_locale_currency" ("siteid", "status");


--
-- Table structure for table `mshop_locale`
--

CREATE TABLE "mshop_locale" (
	-- ID
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- Site ID
	"siteid" INTEGER NOT NULL,
	-- ISO language code
	"langid" VARCHAR(5) NOT NULL,
	-- ISO code of the currency
	"currencyid" CHAR(3) NOT NULL,
	-- pos
	"pos" INTEGER NOT NULL,
	-- Status
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msloc_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msloc_sid_lang_curr"
	UNIQUE ("siteid", "langid", "currencyid"),
CONSTRAINT "fk_msloc_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msloc_langid"
	FOREIGN KEY ("langid")
	REFERENCES "mshop_locale_language" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msloc_currid"
	FOREIGN KEY ("currencyid")
	REFERENCES "mshop_locale_currency" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_msloc_sid_curid" ON "mshop_locale" ("siteid", "currencyid");

CREATE INDEX "idx_msloc_sid_status" ON "mshop_locale" ("siteid", "status");

CREATE INDEX "idx_msloc_sid_pos" ON "mshop_locale" ("siteid", "pos");

CREATE INDEX "idx_msloc_sid_ctime" ON "mshop_locale" ("siteid", "ctime");

CREATE INDEX "idx_msloc_sid_mtime" ON "mshop_locale" ("siteid", "mtime");

CREATE INDEX "idx_msloc_sid_editor" ON "mshop_locale" ("siteid", "editor");
