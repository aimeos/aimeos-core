--
-- MShop coupon database definition and description
--


SET SESSION sql_mode='ANSI';



--
-- coupon items
--

CREATE TABLE "mshop_coupon" (
	-- Unique coupon id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- Name of the coupon
	"label" VARCHAR(255) NOT NULL,
	-- Coupon provider class name
	"provider" VARCHAR(255) NOT NULL,
	-- Coupon provider configuration
	"config" TEXT NOT NULL,
	-- Valid from
	"start" DATETIME DEFAULT NULL,
	-- Valid until
	"end" DATETIME DEFAULT NULL,
	-- Status code (0=disabled, 1=enabled, >1 for anything special)
	"status" SMALLINT NOT NULL DEFAULT 0,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_mscou_id"
	PRIMARY KEY ("id")
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX "idx_mscou_sid_stat_start_end" ON "mshop_coupon" ("siteid", "status", "start", "end");

CREATE INDEX "idx_mscou_sid_label" ON "mshop_coupon" ("siteid", "label");

CREATE INDEX "idx_mscou_sid_provider" ON "mshop_coupon" ("siteid", "provider");

CREATE INDEX "idx_mscou_sid_start" ON "mshop_coupon" ("siteid", "start");

CREATE INDEX "idx_mscou_sid_end" ON "mshop_coupon" ("siteid", "end");

CREATE INDEX "idx_mscou_sid_mtime" ON "mshop_coupon" ("siteid", "mtime");

CREATE INDEX "idx_mscou_sid_ctime" ON "mshop_coupon" ("siteid", "ctime");

CREATE INDEX "idx_mscou_sid_editor" ON "mshop_coupon" ("siteid", "editor");


--
-- coupon codes
--

CREATE TABLE "mshop_coupon_code" (
	-- Unique coupon code id
	"id" INTEGER NOT NULL AUTO_INCREMENT,
	-- Unique coupon id, references mshop_coupon.id
	"couponid" INTEGER NOT NULL,
	-- site id, references mshop_locale_site.id
	"siteid" INTEGER NOT NULL,
	-- coupon code
	"code" VARCHAR(32) NOT NULL COLLATE utf8_bin,
	-- Number of times the coupon is usable (decreases after each usage)
	"count" INTEGER NOT NULL,
	-- Valid from
	"start" DATETIME DEFAULT NULL,
	-- Valid until
	"end" DATETIME DEFAULT NULL,
	-- Date of last modification of this database entry
	"mtime" DATETIME NOT NULL,
	-- Date of creation of this database entry
	"ctime" DATETIME NOT NULL,
	-- Editor who modified this entry at last
	"editor" VARCHAR(255) NOT NULL,
CONSTRAINT "pk_mscouco_id"
	PRIMARY KEY ("id"),
CONSTRAINT "unq_mscouco_sid_code"
	UNIQUE ("siteid", "code"),
CONSTRAINT "fk_mscouco_couponid"
	FOREIGN KEY ("couponid")
	REFERENCES "mshop_coupon" ("id")
	ON UPDATE CASCADE
	ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET = utf8;

CREATE INDEX "idx_mscouco_sid_ct_start_end" ON "mshop_coupon_code" ("siteid", "count", "start", "end");

CREATE INDEX "idx_mscouco_sid_ct_start_end" ON "mshop_coupon_code" ("siteid", "count", "start", "end");

CREATE INDEX "idx_mscouco_sid_start" ON "mshop_coupon_code" ("siteid", "start");

CREATE INDEX "idx_mscouco_sid_end" ON "mshop_coupon_code" ("siteid", "end");

CREATE INDEX "idx_mscouco_sid_mtime" ON "mshop_coupon_code" ("siteid", "mtime");

CREATE INDEX "idx_mscouco_sid_ctime" ON "mshop_coupon_code" ("siteid", "ctime");

CREATE INDEX "idx_mscouco_sid_editor" ON "mshop_coupon_code" ("siteid", "editor");
