
--
-- Plugin database definitions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://www.arcavias.com/en/license
--


SET SESSION sql_mode='ANSI';



--
-- Plugins for extending shop functionality
--

CREATE TABLE "mshop_plugin_type" (
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
CONSTRAINT "pk_mspluty_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mspluty_sid_dom_code"
	UNIQUE ("siteid", "domain", "code"),
CONSTRAINT "fk_mspluty_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mspluty_sid_status" ON "mshop_plugin_type" ("siteid", "status");

CREATE INDEX "idx_mspluty_sid_code" ON "mshop_plugin_type" ("siteid", "code");

CREATE INDEX "idx_mspluty_sid_label" ON "mshop_plugin_type" ("siteid", "label");


CREATE TABLE "mshop_plugin" (
	-- Unique plugin id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- Type of plugin (1=Order plugin)
	"typeid" INTEGER NOT NULL,
	-- Name of the plugin
	"label" VARCHAR(255) NOT NULL,
	-- Name of the plugin class
	"provider" VARCHAR(255) NOT NULL,
	-- Plugin configuration
	"config" TEXT NOT NULL DEFAULT '',
	-- Position for sorting
	"pos" INTEGER NOT NULL,
	-- status code (0=hidden, 1=display)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_msplu_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mspul_sid_tid_provider"
	UNIQUE ("siteid", "typeid", "provider"),
CONSTRAINT "fk_msplu_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE,
CONSTRAINT "fk_msplu_typeid"
	FOREIGN KEY ("typeid")
	REFERENCES "mshop_plugin_type" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE INDEX "idx_msplu_sid_status" ON "mshop_plugin" ("siteid", "status");

CREATE INDEX "idx_msplu_sid_label" ON "mshop_plugin" ("siteid", "label");

CREATE INDEX "idx_msplu_sid_provider" ON "mshop_plugin" ("siteid", "provider");

CREATE INDEX "idx_msplu_sid_provider" ON "mshop_plugin" ("siteid", "pos");

CREATE INDEX "idx_msplu_sid_mtime" ON "mshop_plugin" ("siteid", "mtime");

CREATE INDEX "idx_msplu_sid_ctime" ON "mshop_plugin" ("siteid", "ctime");

CREATE INDEX "idx_msplu_sid_editor" ON "mshop_plugin" ("siteid", "editor");
