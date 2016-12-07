<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_locale_currency"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_locale_currency" (
				"label", "status", "siteid", "mtime", "editor", "id", "ctime"
			) VALUES(
				?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_locale_currency"
			SET "label" = ?, "status" = ?, "siteid"=?, "mtime" = ?,
				"editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mloccu."id" AS "locale.currency.id", mloccu."label" AS "locale.currency.label",
				mloccu."siteid" AS "locale.currency.siteid", mloccu."status" AS "locale.currency.status",
				mloccu."mtime" AS "locale.currency.mtime", mloccu."editor" AS "locale.currency.editor",
				mloccu."ctime" AS "locale.currency.ctime"
			FROM "mshop_locale_currency" AS mloccu
			WHERE :cond
			GROUP BY mloccu."id", mloccu."label", mloccu."siteid", mloccu."status",
				mloccu."mtime", mloccu."editor", mloccu."ctime" :columns
			ORDER BY :order
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mloccu."id"
				FROM "mshop_locale_currency" AS mloccu
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_locale_currency_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

