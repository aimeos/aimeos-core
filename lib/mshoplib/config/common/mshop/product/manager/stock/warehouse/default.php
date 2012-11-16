<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_product_stock_warehouse" ( "siteid", "code", "label", "status", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_product_stock_warehouse"
			SET "siteid" = ?, "code" = ?, "label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE
			FROM "mshop_product_stock_warehouse"
			WHERE "id" = ?
		',
		'search' => '
			SELECT mprostwa."id", mprostwa."siteid", mprostwa."code", mprostwa."label", mprostwa."status",
				mprostwa."mtime", mprostwa."editor", mprostwa."ctime"
			FROM "mshop_product_stock_warehouse" AS mprostwa
			:joins
			WHERE
				:cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
				FROM( 
					SELECT DISTINCT mprostwa."id"
					FROM "mshop_product_stock_warehouse" AS mprostwa
					:joins
					WHERE :cond
					LIMIT 10000 OFFSET 0
					) AS list
		',
	),
);
