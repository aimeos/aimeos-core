<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'aggregate' => array(
		'ansi' => '
		SELECT "key", COUNT("id") AS "count"
		FROM (
			SELECT :key AS "key", mordbaad."id" AS "id"
			FROM "mshop_order_base_address" AS mordbaad
			:joins
			WHERE :cond
			GROUP BY :key, mordbaad."id" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		) AS list
		GROUP BY "key"
	'
	),
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_order_base_address"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_order_base_address" (
				"baseid", "siteid", "addrid", "type", "company", "vatid", "salutation",
				"title", "firstname", "lastname", "address1", "address2",
				"address3", "postal", "city", "state", "countryid", "langid",
				"telephone", "email", "telefax", "website", "longitude", "latitude",
				"flag", "mtime", "editor", "ctime"
			) VALUES (
				?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_order_base_address"
			SET "baseid" = ?, "siteid" = ?, "addrid" = ?, "type" = ?,
				"company" = ?, "vatid" = ?, "salutation" = ?, "title" = ?, "firstname" = ?,
				"lastname" = ?, "address1" = ?, "address2" = ?,
				"address3" = ?, "postal" = ?, "city" = ?, "state" = ?,
				"countryid" = ?, "langid" = ?, "telephone" = ?, "email" = ?,
				"telefax" = ?, "website" = ?, "longitude" = ?, "latitude" = ?,
				"flag" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mordbaad."id" AS "order.base.address.id", mordbaad."baseid" AS "order.base.address.baseid",
				mordbaad."siteid" AS "order.base.address.siteid", mordbaad."addrid" AS "order.base.address.addressid",
				mordbaad."type" AS "order.base.address.type", mordbaad."flag" AS "order.base.address.flag",
				mordbaad."company" AS "order.base.address.company", mordbaad."vatid" AS "order.base.address.vatid",
				mordbaad."salutation" AS "order.base.address.salutation", mordbaad."title" AS "order.base.address.title",
				mordbaad."firstname" AS "order.base.address.firstname", mordbaad."lastname" AS "order.base.address.lastname",
				mordbaad."address1" AS "order.base.address.address1", mordbaad."address2" AS "order.base.address.address2",
				mordbaad."address3" AS "order.base.address.address3", mordbaad."postal" AS "order.base.address.postal",
				mordbaad."city" AS "order.base.address.city", mordbaad."state" AS "order.base.address.state",
				mordbaad."countryid" AS "order.base.address.countryid", mordbaad."langid" AS "order.base.address.languageid",
				mordbaad."telephone" AS "order.base.address.telephone", mordbaad."email" AS "order.base.address.email",
				mordbaad."telefax" AS "order.base.address.telefax", mordbaad."website" AS "order.base.address.website",
				mordbaad."longitude" AS "order.base.address.longitude", mordbaad."latitude" AS "order.base.address.latitude",
				mordbaad."mtime" AS "order.base.address.mtime", mordbaad."editor" AS "order.base.address.editor",
				mordbaad."ctime" AS "order.base.address.ctime"
			FROM "mshop_order_base_address" AS mordbaad
			:joins
			WHERE :cond
			GROUP BY mordbaad."id", mordbaad."baseid", mordbaad."siteid", mordbaad."addrid",
				mordbaad."type", mordbaad."flag", mordbaad."company", mordbaad."vatid",
				mordbaad."salutation", mordbaad."title", mordbaad."firstname", mordbaad."lastname",
				mordbaad."address1", mordbaad."address2", mordbaad."address3", mordbaad."postal",
				mordbaad."city", mordbaad."state", mordbaad."countryid", mordbaad."langid",
				mordbaad."telephone", mordbaad."email", mordbaad."telefax", mordbaad."website",
				mordbaad."longitude", mordbaad."latitude", mordbaad."mtime", mordbaad."editor",
				mordbaad."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT( DISTINCT mordbaad."id" ) AS "count"
			FROM "mshop_order_base_address" AS mordbaad
			:joins
			WHERE :cond
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_order_base_address_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

