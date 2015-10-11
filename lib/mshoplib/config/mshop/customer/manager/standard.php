<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_customer"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_customer" (
				"siteid", "label", "code", "company", "vatid", "salutation", "title",
				"firstname", "lastname", "address1", "address2", "address3",
				"postal", "city", "state", "countryid", "langid", "telephone",
				"email", "telefax", "website", "birthday", "status", "vdate",
				"password", "mtime", "editor", "ctime"
			) VALUES (
				?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_customer"
			SET "siteid" = ?, "label" = ?, "code" = ?, "company" = ?, "vatid" = ?,
				"salutation" = ?, "title" = ?, "firstname" = ?, "lastname" = ?,
				"address1" = ?, "address2" = ?, "address3" = ?, "postal" = ?,
				"city" = ?, "state" = ?, "countryid" = ?, "langid" = ?,
				"telephone" = ?, "email" = ?, "telefax" = ?, "website" = ?,
				"birthday" = ?, "status" = ?, "vdate" = ?, "password" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mcus."id", mcus."siteid", mcus."label",
				mcus."code", mcus."company", mcus."vatid", mcus."salutation", mcus."title",
				mcus."firstname", mcus."lastname", mcus."address1",
				mcus."address2", mcus."address3", mcus."postal", mcus."city",
				mcus."state", mcus."countryid", mcus."langid",
				mcus."telephone", mcus."email", mcus."telefax", mcus."website",
				mcus."birthday", mcus."status", mcus."vdate", mcus."password",
				mcus."ctime", mcus."mtime", mcus."editor"
			FROM "mshop_customer" AS mcus
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcus."id"
				FROM "mshop_customer" AS mcus
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);
