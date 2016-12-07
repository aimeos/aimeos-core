<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_locale_site"
			WHERE :cond
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_locale_site" (
				"code", "label", "config", "status", "parentid", "editor",
				"mtime", "ctime", "level", "nleft", "nright"
			)
			SELECT ?, ?, ?, ?, ?, ?, ?, ?, 0, COALESCE( MAX("nright"), 0 ) + 1,
				COALESCE( MAX("nright"), 0 ) + 2
			FROM "mshop_locale_site"
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_locale_site"
			SET "code" = ?, "label" = ?, "config" = ?, "status" = ?,
				"editor" = ?, "mtime" = ?
			WHERE id = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mlocsi."id" AS "locale.site.id", mlocsi."parentid" AS "locale.site.parentid",
				mlocsi."code" AS "locale.site.code", mlocsi."label" AS "locale.site.label",
				mlocsi."config" AS "locale.site.config", mlocsi."status" AS "locale.site.status",
				mlocsi."editor" AS "locale.site.editor", mlocsi."mtime" AS "locale.site.mtime",
				mlocsi."ctime" AS "locale.site.ctime"
			FROM "mshop_locale_site" AS mlocsi
			WHERE :cond
			GROUP BY mlocsi."id", mlocsi."parentid", mlocsi."code", mlocsi."label",
				mlocsi."config", mlocsi."status", mlocsi."editor", mlocsi."mtime",
				mlocsi."ctime" :columns
			ORDER BY :order
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mlocsi."id"
				FROM "mshop_locale_site" AS mlocsi
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_locale_site_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

