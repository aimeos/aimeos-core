<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_order_base_product_attr"
			WHERE "id" = ?
		',
		'insert' => '
			INSERT INTO "mshop_order_base_product_attr" ( "siteid", "ordprodid", "code", "value", "name",
				"mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_order_base_product_attr"
			SET "siteid" = ?, "ordprodid" = ?, "code" = ?, "value" = ?, "name" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT mordbaprat."id", mordbaprat."siteid", mordbaprat."ordprodid", mordbaprat."code",
				mordbaprat."value", mordbaprat."name", mordbaprat."mtime", mordbaprat."editor", mordbaprat."ctime"
			FROM "mshop_order_base_product_attr" AS mordbaprat
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mordbaprat."id" ) AS "count"
			FROM "mshop_order_base_product_attr" AS mordbaprat
			:joins
			WHERE :cond
			LIMIT 10000 OFFSET 0
		',
	),
);
