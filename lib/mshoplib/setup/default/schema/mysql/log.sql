--
-- MShop log database definitions and descriptions
--
-- Copyright (c) Metaways Infosystems GmbH, 2011
-- License LGPLv3, http://www.arcavias.com/en/license
--


SET SESSION sql_mode='ANSI';



--
-- Log table
--

CREATE TABLE "madmin_log" (
	-- Unique id of the log entry
	"id" BIGINT NOT NULL AUTO_INCREMENT,
	-- Site id of the log entry
	"siteid" INTEGER,
	-- log facility
	"facility" VARCHAR(32) NOT NULL,
	-- time stamp of entry
	"timestamp" DATETIME NOT NULL,
	-- log priority
	"priority" SMALLINT NOT NULL,
	-- log message
	"message" MEDIUMTEXT NOT NULL,
	-- request
	"request" VARCHAR(32) NOT NULL,
CONSTRAINT "pk_mslog_id"
	PRIMARY KEY ("id")
) CHARACTER SET = utf8;

CREATE INDEX "idx_malog_sid_time_facility_prio" ON "madmin_log" ("siteid", "timestamp", "facility", "priority");
