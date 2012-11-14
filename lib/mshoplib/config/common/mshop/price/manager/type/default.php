<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14408 2011-12-17 13:24:46Z nsendetzky $
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_price_type"( "siteid", "code", "domain", "label", "status", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_price_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_price_type"
			WHERE "id" = ?
		',
		'search' => '
			SELECT mprity."id", mprity."siteid", mprity."code", mprity."domain", mprity."label",
				mprity."status", mprity."mtime", mprity."editor", mprity."ctime"
			FROM "mshop_price_type" AS mprity
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mprity."id" ) AS "count"
			FROM "mshop_price_type" AS mprity
			:joins
			WHERE :cond
			LIMIT 10000 OFFSET 0
		',
	),
);
