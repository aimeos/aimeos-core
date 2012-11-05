<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14818 2012-01-12 09:53:56Z spopp $
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_order_base_service" ("baseid", "siteid", "type", "code",
				"name", "mediaurl", "price", "shipping", "rebate", "taxrate", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_order_base_service"
			SET "baseid" = ?, "siteid" = ?, "type" = ?, "code" = ?, "name" = ?, "mediaurl" = ?, "price" = ?,
				"shipping" = ?, "rebate" = ?, "taxrate" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_order_base_service"
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mordbase."id", mordbase."baseid", mordbase."siteid",
				mordbase."type", mordbase."code", mordbase."name", mordbase."mediaurl", mordbase."price",
				mordbase."shipping", mordbase."rebate", mordbase."taxrate",
				mordbase."mtime", mordbase."editor", mordbase."ctime"
			FROM "mshop_order_base_service" AS mordbase
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(DISTINCT mordbase."id") AS "count"
			FROM "mshop_order_base_service" AS mordbase
			:joins
			WHERE :cond
		',
	)
);