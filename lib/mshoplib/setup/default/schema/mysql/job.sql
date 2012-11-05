--
-- MShop job database definitions and descriptions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://www.arcavias.com/en/license
-- $Id: job.sql 14246 2011-12-09 12:25:12Z nsendetzky $
--


SET SESSION sql_mode='ANSI';



--
-- Job table
--

CREATE TABLE "madmin_job" (
	-- Unique id of the job entry
	"id" BIGINT NOT NULL AUTO_INCREMENT,
	-- Site id of the job entry
	"siteid" INTEGER NOT NULL,
	-- Label/name of the job
	"label" VARCHAR(255) NOT NULL,
	-- Job controller/action name
	"method" VARCHAR(255) NOT NULL,
	-- Parameter for the job (JSON encoded)
	"parameter" TEXT NOT NULL,
	-- Result (JSON encoded)
	"result" TEXT NOT NULL,
	-- Status (-1: running/failure, 0: done, 1: scheduled)
	"status" SMALLINT NOT NULL,
	-- Creation time stamp
	"ctime" DATETIME NOT NULL,
	-- Modification time stamp
	"mtime" DATETIME NOT NULL,
	-- Modification time stamp
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_majob_id"
	PRIMARY KEY ("id"),
CONSTRAINT "fk_majob_siteid"
	FOREIGN KEY ("siteid")
	REFERENCES "mshop_locale_site" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_majob_ctime" ON "madmin_job" ("ctime");
CREATE INDEX "idx_majob_sid_status" ON "madmin_job" ("siteid", "status");
