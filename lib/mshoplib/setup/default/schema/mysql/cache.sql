--
-- MShop cache database definitions and descriptions
--
-- Copyright (c) Metaways Infosystems GmbH, 2014
-- License LGPLv3, http://www.arcavias.com/en/license
--


SET SESSION sql_mode='ANSI';



--
-- Cache table
--

CREATE TABLE "madmin_cache" (
	-- Unique id of the cache entry
	"id" VARCHAR(255) NOT NULL,
	-- Expiration time stamp
	"expire" DATETIME,
	-- Cached value
	"value" MEDIUMTEXT NOT NULL,
CONSTRAINT "pk_macac_id"
	PRIMARY KEY ("id")
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_majob_expire" ON "madmin_cache" ("expire");


--
-- Cache tag table
--

CREATE TABLE "madmin_cache_tag" (
	-- Unique id of the cache entry
	"id" VARCHAR(255) NOT NULL,
	-- Tag name
	"name" VARCHAR(255) NOT NULL,
CONSTRAINT "unq_macacta_id_name"
	UNIQUE KEY ("id", "name"),
CONSTRAINT "fk_macac_id"
	FOREIGN KEY ("id")
	REFERENCES "madmin_cache" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;
