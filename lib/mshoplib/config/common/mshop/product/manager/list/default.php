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
			FROM "mshop_product_list"
			WHERE "siteid" = ?
				AND "parentid" = ?
				AND "typeid" = ?
				AND "domain" = ?
		',
		'insert' => '
			INSERT INTO "mshop_product_list"( "parentid", "siteid", "typeid", "domain", "refid", "start", "end", "pos",
				"mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_product_list"
			SET "parentid" = ?, "siteid" = ?, "typeid" = ?, "domain" = ?, "refid" = ?, "start" = ?, "end" = ?,
				"pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'updatepos' => '
			UPDATE "mshop_product_list"
			SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_product_list"
			WHERE "id" = ?
		',
		'move' => '
			UPDATE "mshop_product_list"
			SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ?
				AND "parentid" = ?
				AND "typeid" = ?
				AND "domain" = ?
				AND "pos" >= ?
		',
		'search' => '
			SELECT mproli."id", mproli."parentid", mproli."siteid", mproli."typeid", mproli."domain",
				mproli."refid", mproli."start", mproli."end", mproli."pos", mproli."mtime", mproli."editor", mproli."ctime"
			FROM "mshop_product_list" AS mproli
			:joins
			WHERE :cond
			 /*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mproli."id" ) AS "count"
			FROM "mshop_product_list" AS mproli
			:joins
			WHERE :cond
			LIMIT 10000 OFFSET 0
		',
	),
);
