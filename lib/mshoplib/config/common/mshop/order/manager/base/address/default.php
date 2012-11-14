<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14408 2011-12-17 13:24:46Z nsendetzky $
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_order_base_address"
			WHERE "id" = ?
		',
		'insert' => '
			INSERT INTO "mshop_order_base_address" ( "baseid", "siteid", "type", "company", "salutation", "title",
				"firstname", "lastname", "address1", "address2", "address3", "postal", "city", "state",
				"countryid", "langid", "telephone", "email", "telefax", "website", "flag", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_order_base_address"
			SET "baseid" = ?, "siteid" = ?, "type" = ?, "company" = ?, "salutation" = ?, "title" = ?,
				"firstname" = ?, "lastname" = ?, "address1" = ?, "address2" = ?, "address3" = ?, "postal" = ?,
				"city" = ?, "state" = ?, "countryid" = ?, "langid" = ?, "telephone" = ?, "email" = ?,
				"telefax" = ?, "website" = ?, "flag" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT mordbaad."id", mordbaad."baseid", mordbaad."siteid", mordbaad."type", mordbaad."company", mordbaad."salutation", mordbaad."title",
				mordbaad."firstname", mordbaad."lastname", mordbaad."address1", mordbaad."address2", mordbaad."address3",
				mordbaad."postal", mordbaad."city", mordbaad."state", mordbaad."countryid", mordbaad."langid", mordbaad."telephone",
				mordbaad."email", mordbaad."telefax", mordbaad."website", mordbaad."flag", mordbaad."mtime", mordbaad."editor", mordbaad."ctime"
			FROM "mshop_order_base_address" AS mordbaad
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mordbaad."id" ) AS "count"
			FROM "mshop_order_base_address" AS mordbaad
			:joins
			WHERE :cond
			LIMIT 10000 OFFSET 0
		',
	),
);
