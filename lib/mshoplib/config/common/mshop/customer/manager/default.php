<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14682 2012-01-04 11:30:14Z nsendetzky $
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_customer"
			WHERE "id" = ?
		',
		'insert' => '
			INSERT INTO "mshop_customer" ("siteid", "label", "code", "company", "salutation", "title",
				"firstname", "lastname", "address1", "address2", "address3", "postal", "city", "state",
				"countryid", "langid", "telephone", "email", "telefax", "website", "birthday", "status", "password",
				"mtime", "editor", "ctime")
			VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
		',
		'update' => '
			UPDATE "mshop_customer"
			SET "siteid"=?, "label"=?, "code"=?, "company"=?, "salutation"=?, "title"=?, "firstname"=?, "lastname"=?,
				"address1"=?, "address2"=?, "address3"=?, "postal"=?, "city"=?, "state"=?, "countryid"=?,
				"langid"=?, "telephone"=?, "email"=?, "telefax"=?, "website"=?, "birthday"=?, "status"=?, "password"=?,
				"mtime"=?, "editor"=?
			WHERE "id"=?
		',
		'search' => '
			SELECT DISTINCT mcus."id", mcus."siteid", mcus."label", mcus."code", mcus."company", mcus."salutation",
				mcus."title", mcus."firstname", mcus."lastname", mcus."address1", mcus."address2", mcus."address3",
				mcus."postal", mcus."city", mcus."state", mcus."countryid", mcus."langid", mcus."telephone",
				mcus."email", mcus."telefax", mcus."website", mcus."birthday", mcus."status", mcus."password",
				mcus."ctime", mcus."mtime", mcus."editor"
			FROM "mshop_customer" AS mcus
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(DISTINCT mcus."id") AS "count"
			FROM "mshop_customer" AS mcus
			:joins
			WHERE :cond
			LIMIT 10000 OFFSET 0
		',
	),
);