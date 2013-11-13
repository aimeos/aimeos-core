<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_order_base_service" ("baseid", "siteid", "servid", "type", "code",
				"name", "mediaurl", "price", "costs", "rebate", "taxrate", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_order_base_service"
			SET "baseid" = ?, "siteid" = ?, "servid" = ?, "type" = ?, "code" = ?, "name" = ?, "mediaurl" = ?, "price" = ?,
				"costs" = ?, "rebate" = ?, "taxrate" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_order_base_service"
			WHERE :cond
			AND siteid = ?
		',
		'search' => '
			SELECT DISTINCT mordbase."id", mordbase."baseid", mordbase."siteid", mordbase."servid",
				mordbase."type", mordbase."code", mordbase."name", mordbase."mediaurl", mordbase."price",
				mordbase."costs", mordbase."rebate", mordbase."taxrate",
				mordbase."mtime", mordbase."editor", mordbase."ctime"
			FROM "mshop_order_base_service" AS mordbase
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM(
				SELECT DISTINCT mordbase."id"
				FROM "mshop_order_base_service" AS mordbase
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	)
);