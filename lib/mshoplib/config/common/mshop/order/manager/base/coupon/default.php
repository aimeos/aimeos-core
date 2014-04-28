<?php

return array(
	'item' => array(
		'insert' => '
	        INSERT INTO "mshop_order_base_coupon" ("baseid", "siteid", "ordprodid", "code", "mtime", "editor", "ctime")
	        VALUES ( ?, ?, ?, ?, ?, ?, ? )
	    ',
		'update' => '
	        UPDATE "mshop_order_base_coupon"
	        SET "baseid" = ?, "siteid" = ?, "ordprodid" = ?, "code" = ?, "mtime" = ?, "editor" = ?
	        WHERE "id" = ?
	    ',
		'delete' => '
	        DELETE FROM "mshop_order_base_coupon"
	        WHERE :cond
	        AND siteid = ?
	    ',
		'search' => '
			SELECT DISTINCT mordbaco."id", mordbaco."baseid", mordbaco."siteid", mordbaco."ordprodid",
				mordbaco."code", mordbaco."mtime", mordbaco."editor", mordbaco."ctime"
			FROM "mshop_order_base_coupon" AS mordbaco
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(DISTINCT "id" ) AS "count"
			FROM "mshop_order_base_coupon" AS mordbaco
			WHERE :cond
		',
	),
);
