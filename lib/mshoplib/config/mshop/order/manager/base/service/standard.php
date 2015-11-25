<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'aggregate' => array(
		'ansi' => '
		SELECT "key", COUNT("id") AS "count"
		FROM (
			SELECT DISTINCT :key AS "key", mordbase."id" AS "id"
			FROM "mshop_order_base_service" AS mordbase
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		) AS list
		GROUP BY "key"
	'
	),
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_order_base_service"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_order_base_service" (
				"baseid", "siteid", "servid", "type", "code", "name",
				"mediaurl", "price", "costs", "rebate", "taxrate", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_order_base_service"
			SET "baseid" = ?, "siteid" = ?, "servid" = ?, "type" = ?,
				"code" = ?, "name" = ?, "mediaurl" = ?, "price" = ?,
				"costs" = ?, "rebate" = ?, "taxrate" = ?, "mtime" = ?,
				"editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mordbase."id" AS "order.base.service.id", mordbase."baseid" AS "order.base.service.baseid",
				mordbase."siteid" AS "order.base.service.siteid", mordbase."servid" AS "order.base.service.serviceid",
				mordbase."type" AS "order.base.service.type", mordbase."code" AS "order.base.service.code",
				mordbase."name" AS "order.base.service.name", mordbase."mediaurl" AS "order.base.service.mediaurl",
				mordbase."price" AS "order.base.service.price", mordbase."costs" AS "order.base.service.costs",
				mordbase."rebate" AS "order.base.service.rebate", mordbase."taxrate" AS "order.base.service.taxrate",
				mordbase."mtime" AS "order.base.service.mtime", mordbase."editor" AS "order.base.service.editor",
				mordbase."ctime" AS "order.base.service.ctime"
			FROM "mshop_order_base_service" AS mordbase
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT( DISTINCT mordbase."id" ) AS "count"
			FROM "mshop_order_base_service" AS mordbase
			:joins
			WHERE :cond
		'
	),
	'newid' => array(
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);
