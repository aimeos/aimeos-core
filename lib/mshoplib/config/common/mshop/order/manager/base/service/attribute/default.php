<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_order_base_service_attr" ( "siteid", "ordservid", "name", "code", "value",
				"mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
				UPDATE "mshop_order_base_service_attr"
				SET "siteid" = ?, "ordservid" = ?, "name" = ?, "code" = ?, "value" = ?, "mtime" = ?, "editor" = ?
				WHERE "id" = ?
			',
		'delete' => '
			DELETE FROM "mshop_order_base_service_attr"
			WHERE "id" = ?
		',
		'search' => '
			SELECT mordbaseat."id", mordbaseat."siteid", mordbaseat."ordservid", mordbaseat."name",
				mordbaseat."code", mordbaseat."value", mordbaseat."mtime", mordbaseat."editor", mordbaseat."ctime"
			FROM "mshop_order_base_service_attr" AS mordbaseat
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mordbaseat."id" ) AS "count"
			FROM "mshop_order_base_service_attr" AS mordbaseat
			:joins
			WHERE :cond
			LIMIT 10000 OFFSET 0
		',
	),
);