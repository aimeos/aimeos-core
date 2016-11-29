<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
				"email", "telefax", "website", "longitude", "latitude", "birthday",
				"status", "vdate", "password", "mtime", "editor", "ctime"
			) VALUES (
				?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
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
				"longitude" = ?, "latitude" = ?, "birthday" = ?, "status" = ?,
				"vdate" = ?, "password" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mcus."id" AS "customer.id", mcus."siteid" AS "customer.siteid",
				mcus."label" AS "customer.label", mcus."code" AS "customer.code",
				mcus."company" AS "customer.company", mcus."vatid" AS "customer.vatid",
				mcus."salutation" AS "customer.salutation", mcus."title" AS "customer.title",
				mcus."firstname" AS "customer.firstname", mcus."lastname" AS "customer.lastname",
				mcus."address1" AS "customer.address1", mcus."address2" AS "customer.address2",
				mcus."address3" AS "customer.address3", mcus."postal" AS "customer.postal",
				mcus."city" AS "customer.city", mcus."state" AS "customer.state",
				mcus."countryid" AS "customer.countryid", mcus."langid" AS "customer.langid",
				mcus."telephone" AS "customer.telephone", mcus."email" AS "customer.email",
				mcus."telefax" AS "customer.telefax", mcus."website" AS "customer.website",
				mcus."longitude" AS "customer.longitude", mcus."latitude" AS "customer.latitude",
				mcus."birthday" AS "customer.birthday", mcus."status" AS "customer.status",
				mcus."vdate" AS "customer.dateverified", mcus."password" AS "customer.password",
				mcus."ctime" AS "customer.ctime", mcus."mtime" AS "customer.mtime",
				mcus."editor" AS "customer.editor"
			FROM "mshop_customer" AS mcus
			:joins
			WHERE :cond
			GROUP BY mcus."id", mcus."siteid", mcus."label", mcus."code",
				mcus."company", mcus."vatid", mcus."salutation", mcus."title",
				mcus."firstname", mcus."lastname", mcus."address1", mcus."address2",
				mcus."address3", mcus."postal", mcus."city", mcus."state",
				mcus."countryid", mcus."langid", mcus."telephone", mcus."email",
				mcus."telefax", mcus."website", mcus."longitude", mcus."latitude",
				mcus."birthday", mcus."status", mcus."vdate", mcus."password",
				mcus."ctime", mcus."mtime", mcus."editor"
				/*-columns*/ , :columns /*columns-*/
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
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_customer_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);
