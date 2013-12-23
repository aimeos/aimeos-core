--
-- Text database definitions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://www.arcavias.com/en/license
--


SET SESSION sql_mode='ANSI';



--
-- Types of text items
--

CREATE TABLE "mshop_text_type" (
	-- Unique ID of the text type
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- Site id
	"siteid" INTEGER NOT NULL,
	-- domain
	"domain" VARCHAR(32) NOT NULL,
	-- Unique code of the text
	"code" VARCHAR(32) NOT NULL COLLATE utf8_bin,
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
CONSTRAINT "pk_mstexty_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mstexty_sid_dom_code"
	UNIQUE ("siteid", "domain", "code"),
CONSTRAINT "fk_mstexty_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mstexty_sid_status" ON "mshop_text_type" ("siteid", "status");

CREATE INDEX "idx_mstexty_sid_code" ON "mshop_text_type" ("siteid", "code");

CREATE INDEX "idx_mstexty_sid_label" ON "mshop_text_type" ("siteid", "label");


--
-- Text storage
--

CREATE TABLE "mshop_text" (
	-- Unique ID of the text
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- Site id
	"siteid" INTEGER NOT NULL,
	-- Language id
	"langid" VARCHAR(5) DEFAULT NULL,
	-- Type ID of the text (headline, shorttext, etc.)
	"typeid" INTEGER NOT NULL,
	-- Domain the text belongs to (product, catalog, service, etc.)
	"domain" VARCHAR(32) NOT NULL,
	-- Label of the text
	"label" VARCHAR(255) NOT NULL,
	-- Text string
	"content" TEXT NOT NULL DEFAULT '',
	-- Status of the text (0=disabled, 1=enabled)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_mstex_id"
	PRIMARY KEY ("id"),
CONSTRAINT "fk_mstex_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mstex_typeid"
	FOREIGN KEY ("typeid")
	REFERENCES "mshop_text_type" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mstex_langid"
	FOREIGN KEY ("langid")
	REFERENCES "mshop_locale_language" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mstex_sid_status_langid" ON "mshop_text" ("siteid", "status", "langid");

CREATE INDEX "idx_mstex_sid_dom_lid" ON "mshop_text" ("siteid", "domain", "langid");

CREATE INDEX "idx_mstex_sid_dom_label" ON "mshop_text" ("siteid", "domain", "label");

CREATE INDEX "idx_mstex_sid_dom_cont" ON "mshop_text" ("siteid", "domain", "content"(255));

CREATE INDEX "idx_mstex_sid_dom_ctime" ON "mshop_text" ("siteid", "domain", "ctime");

CREATE INDEX "idx_mstex_sid_dom_mtime" ON "mshop_text" ("siteid", "domain", "mtime");

CREATE INDEX "idx_mstex_sid_dom_editor" ON "mshop_text" ("siteid", "domain", "editor");


--
-- Table structure for table `mshop_text_list_type`
--

CREATE TABLE "mshop_text_list_type" (
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
CONSTRAINT "pk_mstexlity_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mstexlity_sid_dom_code"
	UNIQUE ("siteid", "domain", "code"),
CONSTRAINT "fk_mstexlity_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mstexlity_sid_status" ON "mshop_text_list_type" ("siteid", "status");

CREATE INDEX "idx_mstexlity_sid_label" ON "mshop_text_list_type" ("siteid", "label");

CREATE INDEX "idx_mstexlity_sid_code" ON "mshop_text_list_type" ("siteid", "code");


--
-- Table structure for table `mshop_text_list`
--

CREATE TABLE "mshop_text_list" (
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
CONSTRAINT "pk_mstexli_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mstexli_sid_pid_dm_rid_tid"
	UNIQUE ("siteid", "parentid", "domain", "refid", "typeid"),
CONSTRAINT "fk_mstexli_pid"
	FOREIGN KEY ("parentid")
	REFERENCES "mshop_text" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mstexli_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_mstexli_typeid"
	FOREIGN KEY ( "typeid" )
	REFERENCES "mshop_text_list_type" ("id")
	ON DELETE CASCADE
	ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mstexli_sid_stat_start_end" ON "mshop_text_list" ("siteid", "status", "start", "end");

CREATE INDEX "idx_mstexli_sid_rid_dom_tid" ON "mshop_text_list" ("siteid", "refid", "domain", "typeid");

CREATE INDEX "idx_mstexli_pid_sid_rid" ON "mshop_text_list" ("parentid", "siteid", "refid");

CREATE INDEX "idx_mstexli_pid_sid_start" ON "mshop_text_list" ("parentid", "siteid", "start");

CREATE INDEX "idx_mstexli_pid_sid_end" ON "mshop_text_list" ("parentid", "siteid", "end");

CREATE INDEX "idx_mstexli_pid_sid_pos" ON "mshop_text_list" ("parentid", "siteid", "pos");
