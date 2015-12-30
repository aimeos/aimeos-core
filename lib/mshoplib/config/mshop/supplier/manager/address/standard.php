<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_supplier_address"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_supplier_address" (
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
			UPDATE "mshop_supplier_address"
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
			SELECT msupad."id" AS "supplier.address.id", msupad."siteid" AS "supplier.address.siteid",
				msupad."parentid" AS "supplier.address.parentid", msupad."pos" AS "supplier.address.position",
				msupad."company" AS "supplier.address.company", msupad."vatid" AS "supplier.address.vatid",
				msupad."salutation" AS "supplier.address.salutation", msupad."title" AS "supplier.address.title",
				msupad."firstname" AS "supplier.address.firstname", msupad."lastname" AS "supplier.address.lastname",
				msupad."address1" AS "supplier.address.address1", msupad."address2" AS "supplier.address.address2",
				msupad."address3" AS "supplier.address.address3", msupad."postal" AS "supplier.address.postal",
				msupad."city" AS "supplier.address.city", msupad."state" AS "supplier.address.state",
				msupad."countryid" AS "supplier.address.countryid", msupad."langid" AS "supplier.address.languageid",
				msupad."telephone" AS "supplier.address.telephone", msupad."email" AS "supplier.address.email",
				msupad."telefax" AS "supplier.address.telefax", msupad."website" AS "supplier.address.website",
				msupad."flag" AS "supplier.address.flag", msupad."mtime" AS "supplier.address.mtime",
				msupad."editor" AS "supplier.address.editor", msupad."ctime" AS "supplier.address.ctime"
			FROM "mshop_supplier_address" AS msupad
			:joins
			WHERE :cond
			GROUP BY msupad."id", msupad."siteid", msupad."parentid", msupad."pos",
				msupad."company", msupad."vatid", msupad."salutation", msupad."title",
				msupad."firstname", msupad."lastname", msupad."address1", msupad."address2",
				msupad."address3", msupad."postal", msupad."city", msupad."state",
				msupad."countryid", msupad."langid", msupad."telephone", msupad."email",
				msupad."telefax", msupad."website", msupad."flag", msupad."mtime",
				msupad."editor", msupad."ctime" /*-orderby*/, :order /*orderby-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT msupad."id"
				FROM "mshop_supplier_address" AS msupad
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
