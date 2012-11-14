<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14874 2012-01-15 17:19:41Z nsendetzky $
 */

return array(
	'item' => array(
		'getposmax' => '
			SELECT MAX( "pos" ) AS pos
			FROM "mshop_customer_list"
			WHERE "siteid" = ?
				AND "parentid" = ?
				AND "typeid" = ?
				AND "domain" = ?
		',
		'insert' => '
			INSERT INTO "mshop_customer_list"( "parentid", "siteid", "typeid", "domain", "refid", "start", "end",
			"pos", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_customer_list"
			SET "parentid"=?, "siteid" = ?, "typeid" = ?, "domain" = ?, "refid" = ?, "start" = ?, "end" = ?,
				"pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'updatepos' => '
			UPDATE "mshop_customer_list"
				SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_customer_list"
			WHERE "id" = ?
		',
		'move' => '
			UPDATE "mshop_customer_list"
				SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ?
				AND "parentid" = ?
				AND "typeid" = ?
				AND "domain" = ?
				AND "pos" >= ?
		',
		'search' => '
			SELECT mcusli."id", mcusli."parentid", mcusli."siteid", mcusli."typeid",
				mcusli."domain", mcusli."refid", mcusli."start", mcusli."end", mcusli."pos",
				mcusli."mtime", mcusli."editor", mcusli."ctime"
			FROM "mshop_customer_list" AS mcusli
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mcusli."id" ) AS "count"
			FROM "mshop_customer_list" AS mcusli
			:joins
			WHERE :cond
			LIMIT 10000 OFFSET 0
		',
	),
);
