<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
	        DELETE FROM "mshop_order_base_coupon"
	        WHERE :cond AND siteid = ?
	    ',
		'insert' => '
	        INSERT INTO "mshop_order_base_coupon" (
	        	"baseid", "siteid", "ordprodid", "code", "mtime", "editor",
	        	"ctime"
	        ) VALUES (
	        	?, ?, ?, ?, ?, ?, ?
	        )
	    ',
		'update' => '
	        UPDATE "mshop_order_base_coupon"
	        SET "baseid" = ?, "siteid" = ?, "ordprodid" = ?, "code" = ?,
	        	"mtime" = ?, "editor" = ?
	        WHERE "id" = ?
	    ',
		'search' => '
			SELECT DISTINCT mordbaco."id", mordbaco."baseid",
				mordbaco."siteid", mordbaco."ordprodid", mordbaco."code",
				mordbaco."mtime", mordbaco."editor", mordbaco."ctime"
			FROM "mshop_order_base_coupon" AS mordbaco
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM(
				SELECT DISTINCT mordbaco."id"
				FROM "mshop_order_base_coupon" AS mordbaco
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
