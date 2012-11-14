<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_product_tag_type" ( "siteid", "code", "domain", "label", "status",
				"mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_product_tag_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_product_tag_type"
			WHERE "id" = ?
		',
		'search' => '
			SELECT mprotaty."id", mprotaty."siteid", mprotaty."code", mprotaty."domain", mprotaty."label", mprotaty."status",
				mprotaty."mtime", mprotaty."editor", mprotaty."ctime"
			FROM "mshop_product_tag_type" mprotaty
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mprotaty."id" ) AS "count"
			FROM "mshop_product_tag_type" mprotaty
			:joins
			WHERE :cond
			LIMIT 10000 OFFSET 0
		',
	),
);
