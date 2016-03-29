--
-- Message queue database definitions and descriptions
--
-- Copyright (c) Aimeos (aimeos.org), 2016
-- License LGPLv3, http://opensource.org/licenses/LGPL-3.0
--


SET SESSION sql_mode='ANSI';



--
-- Message queue table
--

CREATE TABLE "madmin_queue" (
	-- Unique id of the queue entry
	"id" BIGINT NOT NULL AUTO_INCREMENT,
	-- Queue name
	"queue" VARCHAR(255) NOT NULL,
	-- Client name which reserved the entry
	"cname" VARCHAR(32) NOT NULL,
	-- Release timestamp
	"rtime" DATETIME NOT NULL,
	-- Message text
	"message" TEXT NOT NULL,
CONSTRAINT "pk_maque_id"
	PRIMARY KEY ("id")
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_maque_queue_cname_rtime" ON "madmin_queue" ("queue", "cname", "rtime");
