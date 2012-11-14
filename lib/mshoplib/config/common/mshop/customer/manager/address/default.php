<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_customer_address"
			WHERE "id"=?
		',
		'insert' => '
			INSERT INTO "mshop_customer_address" ("siteid", "refid", "company","salutation","title",
				"firstname","lastname","address1","address2","address3","postal","city","state",
				"countryid","langid","telephone","email","telefax","website","flag","pos", "mtime", "editor", "ctime" )
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
		',
		'update' => '
			UPDATE "mshop_customer_address"
			SET "siteid"=?, "refid"=?, "company"=?, "salutation"=?, "title"=?, "firstname"=?, "lastname"=?,
				"address1"=?, "address2"=?, "address3"=?, "postal"=?, "city"=?, "state"=?, "countryid"=?,
				"langid"=?, "telephone"=?, "email"=?, "telefax"=?, "website"=?, "flag"=?, "pos"=?,
				"mtime"=?, "editor"=?
			WHERE "id"=?
		',
		'search' => '
			SELECT mcusad."id", mcusad."siteid", mcusad."refid", mcusad."company", mcusad."salutation", mcusad."title",
				mcusad."firstname", mcusad."lastname", mcusad."address1", mcusad."address2", mcusad."address3",
				mcusad."postal", mcusad."city", mcusad."state", mcusad."countryid", mcusad."langid", mcusad."telephone",
				mcusad."email", mcusad."telefax", mcusad."website", mcusad."flag", mcusad."pos",
				mcusad."mtime", mcusad."editor", mcusad."ctime"
			FROM "mshop_customer_address" AS mcusad
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mcusad."id" ) AS "count"
			FROM "mshop_customer_address" AS mcusad
			:joins
			WHERE :cond
			LIMIT 10000 OFFSET 0
		',
	),
);