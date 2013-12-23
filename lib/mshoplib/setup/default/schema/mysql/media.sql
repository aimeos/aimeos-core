--
-- Media database definitions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://www.arcavias.com/en/license
--


SET SESSION sql_mode='ANSI';



--
-- Media types
--

CREATE TABLE "mshop_media_type" (
	-- Unique id of the media type
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id
	"siteid" INTEGER NOT NULL,
	-- domain
	"domain" VARCHAR(32) NOT NULL,
	-- Code of the media type
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
CONSTRAINT "pk_msmedty_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msmedty_sid_dom_code"
	UNIQUE ("siteid", "domain", "code"),
CONSTRAINT "fk_msmedty_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_msmedty_sid_status" ON "mshop_media_type" ("siteid", "status");

CREATE INDEX "idx_msmedty_sid_code" ON "mshop_media_type" ("siteid", "code");

CREATE INDEX "idx_msmedty_sid_label" ON "mshop_media_type" ("siteid", "label");


--
-- Table structure for table `mshop_media_list_type`
--

CREATE TABLE "mshop_media_list_type" (
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
CONSTRAINT "pk_msmedlity_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msmedlity_sid_dom_code"
	UNIQUE ("siteid", "domain", "code"),
CONSTRAINT "fk_msmedlity_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msmedlity_sid_status" ON "mshop_media_list_type" ("siteid", "status");

CREATE INDEX "idx_msmedlity_sid_label" ON "mshop_media_list_type" ("siteid", "label");

CREATE INDEX "idx_msmedlity_sid_code" ON "mshop_media_list_type" ("siteid", "code");


--
-- Media storage
--

CREATE TABLE "mshop_media" (
	-- Unique id of the media item
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id
	"siteid" INTEGER NOT NULL,
	-- language id
	"langid" VARCHAR(5) DEFAULT NULL,
	-- Media type
	"typeid" INTEGER NOT NULL,
	-- domain (product, catalog, service, etc.)
	"domain" VARCHAR(32) NOT NULL,
	-- Name of the media item
	"label" VARCHAR(255) NOT NULL,
	-- Link to the media file (relative/absolute)
	"link" VARCHAR(255) NOT NULL,
	-- Link to the preview file (relative/absolute)
	"preview" VARCHAR(255) NOT NULL,
	-- mime type of the file
	"mimetype" VARCHAR(64) NOT NULL,
	-- Status of the media type (0=disabled, 1=enabled, >1 for anything special)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msmed_id"
	PRIMARY KEY ("id"),
CONSTRAINT "fk_msmed_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msmed_typeid"
	FOREIGN KEY ("typeid")
	REFERENCES "mshop_media_type" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msmed_langid"
	FOREIGN KEY ("langid")
	REFERENCES "mshop_locale_language" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_msmed_sid_status_langid" ON "mshop_media" ("siteid", "status", "langid");

CREATE INDEX "idx_msmed_sid_dom_langid" ON "mshop_media" ("siteid", "domain", "langid");

CREATE INDEX "idx_msmed_sid_dom_label" ON "mshop_media" ("siteid", "domain", "label");

CREATE INDEX "idx_msmed_sid_dom_mime" ON "mshop_media" ("siteid", "domain", "mimetype");

CREATE INDEX "idx_msmed_sid_dom_link" ON "mshop_media" ("siteid", "domain", "link");

CREATE INDEX "idx_msmed_sid_dom_ctime" ON "mshop_media" ("siteid", "domain", "ctime");

CREATE INDEX "idx_msmed_sid_dom_mtime" ON "mshop_media" ("siteid", "domain", "mtime");

CREATE INDEX "idx_msmed_sid_dom_editor" ON "mshop_media" ("siteid", "domain", "editor");


--
-- Table structure for table `mshop_media_list`
--

CREATE TABLE "mshop_media_list" (
	-- Unique list id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- media id (parent id)
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
CONSTRAINT "pk_msmedli_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_msmedli_sid_pid_dm_rid_tid"
	UNIQUE ("siteid", "parentid", "domain", "refid", "typeid"),
CONSTRAINT "fk_msmedli_pid"
	FOREIGN KEY ("parentid")
	REFERENCES "mshop_media" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msmedli_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msmedli_typeid"
	FOREIGN KEY ( "typeid" )
	REFERENCES "mshop_media_list_type" ("id")
	ON DELETE CASCADE
	ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msmedli_sid_stat_start_end" ON "mshop_media_list" ("siteid", "status", "start", "end");

CREATE INDEX "idx_msmedli_sid_rid_dom_tid" ON "mshop_media_list" ("siteid", "refid", "domain", "typeid");

CREATE INDEX "idx_msmedli_pid_sid_rid" ON "mshop_media_list" ("parentid", "siteid", "refid");

CREATE INDEX "idx_msmedli_pid_sid_start" ON "mshop_media_list" ("parentid", "siteid", "start");

CREATE INDEX "idx_msmedli_pid_sid_end" ON "mshop_media_list" ("parentid", "siteid", "end");

CREATE INDEX "idx_msmedli_pid_sid_pos" ON "mshop_media_list" ("parentid", "siteid", "pos");
