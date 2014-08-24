<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'aggregate' => '
		SELECT "key", COUNT("id") AS "count"
		FROM (
			SELECT DISTINCT :key AS "key", mordbaad."id" AS "id"
			FROM "mshop_order_base_address" AS mordbaad
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		) AS list
		GROUP BY "key"
	',
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_order_base_address"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_order_base_address" (
				"baseid", "siteid", "addrid", "type", "company", "vatid", "salutation",
				"title", "firstname", "lastname", "address1", "address2",
				"address3", "postal", "city", "state", "countryid", "langid",
				"telephone", "email", "telefax", "website", "flag", "mtime",
				"editor", "ctime"
			) VALUES (
				?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
			)
		',
		'update' => '
			UPDATE "mshop_order_base_address"
			SET "baseid" = ?, "siteid" = ?, "addrid" = ?, "type" = ?,
				"company" = ?, "vatid" = ?, "salutation" = ?, "title" = ?, "firstname" = ?,
				"lastname" = ?, "address1" = ?, "address2" = ?,
				"address3" = ?, "postal" = ?, "city" = ?, "state" = ?,
				"countryid" = ?, "langid" = ?, "telephone" = ?, "email" = ?,
				"telefax" = ?, "website" = ?, "flag" = ?, "mtime" = ?,
				"editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mordbaad."id", mordbaad."baseid",
				mordbaad."siteid", mordbaad."addrid", mordbaad."type",
				mordbaad."company", mordbaad."vatid", mordbaad."salutation", mordbaad."title",
				mordbaad."firstname", mordbaad."lastname", mordbaad."address1",
				mordbaad."address2", mordbaad."address3", mordbaad."postal",
				mordbaad."city", mordbaad."state", mordbaad."countryid",
				mordbaad."langid", mordbaad."telephone", mordbaad."email",
				mordbaad."telefax", mordbaad."website", mordbaad."flag",
				mordbaad."mtime", mordbaad."editor", mordbaad."ctime"
			FROM "mshop_order_base_address" AS mordbaad
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( DISTINCT mordbaad."id" ) AS "count"
			FROM "mshop_order_base_address" AS mordbaad
			:joins
			WHERE :cond
		',
	),
);
