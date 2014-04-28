<?php

/**
 * @version $Id: default.php 37 2012-08-08 17:37:40Z fblasel $
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_coupon_code"
			WHERE :cond
			AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_coupon_code" ("siteid", "couponid", "code", "count",
				"start", "end", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_coupon_code"
			SET "siteid" = ?, "couponid" = ?, "code" = ?, "count" = ?, "start" = ?,
				"end" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mcouco."id", mcouco."couponid", mcouco."siteid", mcouco."code", mcouco."count",
				mcouco."start", mcouco."end", mcouco."mtime", mcouco."editor", mcouco."ctime"
			FROM "mshop_coupon_code" AS mcouco
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(DISTINCT  mcouco."id") AS "count"
			FROM "mshop_coupon_code" AS mcouco
			WHERE :cond
		',
		'decrease' => '
			UPDATE "mshop_coupon_code"
			SET	"count" = "count" - ?, "mtime" = ?, "editor" = ?
			WHERE :cond AND "code" = ?
		',
	),
);