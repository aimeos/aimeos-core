<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_locale_language"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_locale_language" (
				"label", "status", "siteid", "mtime", "editor", "id", "ctime"
			) VALUES(
				?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_locale_language"
			SET "label" = ?, "status" = ?, "siteid"=?, "mtime" = ?,
				"editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mlocla."id" AS "locale.language.id", mlocla."label" AS "locale.language.label",
				mlocla."siteid" AS "locale.language.siteid", mlocla."status" AS "locale.language.status",
				mlocla."mtime" AS "locale.language.mtime", mlocla."editor" AS "locale.language.editor",
				mlocla."ctime" AS "locale.language.ctime"
			FROM "mshop_locale_language" AS mlocla
			WHERE :cond
			GROUP BY mlocla."id", mlocla."label", mlocla."siteid", mlocla."status",
				mlocla."mtime", mlocla."editor", mlocla."ctime" :columns
			ORDER BY :order
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mlocla."id"
				FROM "mshop_locale_language" AS mlocla
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_locale_language_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

