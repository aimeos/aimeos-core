<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_customer_address"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_customer_address" (
				"siteid", "parentid", "company", "vatid", "salutation", "title",
				"firstname", "lastname", "address1", "address2", "address3",
				"postal", "city", "state", "countryid", "langid", "telephone",
				"email", "telefax", "website", "flag", "pos", "mtime",
				"editor", "ctime"
			) VALUES (
				?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_customer_address"
			SET "siteid" = ?, "parentid" = ?, "company" = ?, "vatid" = ?, "salutation" = ?,
				"title" = ?, "firstname" = ?, "lastname" = ?, "address1" = ?,
				"address2" = ?, "address3" = ?, "postal" = ?, "city" = ?,
				"state" = ?, "countryid" = ?, "langid" = ?, "telephone" = ?,
				"email" = ?, "telefax" = ?, "website" = ?, "flag" = ?,
				"pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mcusad."id" AS "customer.address.id", mcusad."siteid" AS "customer.address.siteid",
				mcusad."parentid" AS "customer.address.parentid", mcusad."pos" AS "customer.address.position",
				mcusad."company" AS "customer.address.company", mcusad."vatid" AS "customer.address.vatid",
				mcusad."salutation" AS "customer.address.salutation", mcusad."title" AS "customer.address.title",
				mcusad."firstname" AS "customer.address.firstname", mcusad."lastname" AS "customer.address.lastname",
				mcusad."address1" AS "customer.address.address1", mcusad."address2" AS "customer.address.address2",
				mcusad."address3" AS "customer.address.address3", mcusad."postal" AS "customer.address.postal",
				mcusad."city" AS "customer.address.city", mcusad."state" AS "customer.address.state",
				mcusad."countryid" AS "customer.address.countryid", mcusad."langid" AS "customer.address.languageid",
				mcusad."telephone" AS "customer.address.telephone", mcusad."email" AS "customer.address.email",
				mcusad."telefax" AS "customer.address.telefax", mcusad."website" AS "customer.address.website",
				mcusad."flag" AS "customer.address.flag", mcusad."mtime" AS "customer.address.mtime",
				mcusad."editor" AS "customer.address.editor", mcusad."ctime" AS "customer.address.ctime"
			FROM "mshop_customer_address" AS mcusad
			:joins
			WHERE :cond
			GROUP BY mcusad."id", mcusad."siteid", mcusad."parentid", mcusad."pos",
				mcusad."company", mcusad."vatid", mcusad."salutation", mcusad."title",
				mcusad."firstname", mcusad."lastname", mcusad."address1", mcusad."address2",
				mcusad."address3", mcusad."postal", mcusad."city", mcusad."state",
				mcusad."countryid", mcusad."langid", mcusad."telephone", mcusad."email",
				mcusad."telefax", mcusad."website", mcusad."flag", mcusad."mtime",
				mcusad."editor", mcusad."ctime" /*-orderby*/, :order /*orderby-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcusad."id"
				FROM "mshop_customer_address" AS mcusad
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
