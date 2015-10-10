<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_coupon_code"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_coupon_code" (
				"siteid", "couponid", "code", "count", "start", "end", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_coupon_code"
			SET "siteid" = ?, "couponid" = ?, "code" = ?, "count" = ?,
				"start" = ?, "end" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mcouco."id", mcouco."couponid", mcouco."siteid",
				mcouco."code", mcouco."count", mcouco."start", mcouco."end",
				mcouco."mtime", mcouco."editor", mcouco."ctime"
			FROM "mshop_coupon_code" AS mcouco
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcouco."id"
				FROM "mshop_coupon_code" AS mcouco
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'counter' => array(
		'ansi' => '
			UPDATE "mshop_coupon_code"
			SET	"count" = "count" + ?, "mtime" = ?, "editor" = ?
			WHERE :cond AND "code" = ?
		'
	),
	'newid' => array(
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);
