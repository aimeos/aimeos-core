<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_order_base_product_attr"
			WHERE :cond
			AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_order_base_product_attr" ( "siteid", "attrid", "ordprodid", "type", "code", "value",
				"name", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_order_base_product_attr"
			SET "siteid" = ?, "attrid" = ?, "ordprodid" = ?, "type" = ?, "code" = ?, "value" = ?,
				"name" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mordbaprat."id", mordbaprat."siteid", mordbaprat."attrid", mordbaprat."ordprodid",
				mordbaprat."type", mordbaprat."code", mordbaprat."value", mordbaprat."name", mordbaprat."mtime",
				mordbaprat."editor", mordbaprat."ctime"
			FROM "mshop_order_base_product_attr" AS mordbaprat
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM(
				SELECT DISTINCT mordbaprat."id"
				FROM "mshop_order_base_product_attr" AS mordbaprat
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
