<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


return array(
	'manager' => array(
		'currency' => array(
			'standard' => array(
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
			),
		),
		'language' => array(
			'standard' => array(
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
			),
		),
		'site' => array(
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_locale_site"
						WHERE :cond
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_locale_site" (
							"code", "label", "config", "status", "editor",
							"mtime", "ctime", "parentid", "level", "nleft", "nright"
						)
						SELECT ?, ?, ?, ?, ?, ?, ?, 0, 0, COALESCE( MAX("nright"), 0 ) + 1,
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
						SELECT mlocsi."id" AS "locale.site.id", mlocsi."code" AS "locale.site.code",
							mlocsi."label" AS "locale.site.label", mlocsi."config" AS "locale.site.config",
							mlocsi."status" AS "locale.site.status", mlocsi."editor" AS "locale.site.editor",
							mlocsi."mtime" AS "locale.site.mtime", mlocsi."ctime" AS "locale.site.ctime"
						FROM "mshop_locale_site" AS mlocsi
						WHERE mlocsi."level" = 0 AND :cond
						GROUP BY mlocsi."id", mlocsi."code", mlocsi."label", mlocsi."config",
							mlocsi."status", mlocsi."editor", mlocsi."mtime", mlocsi."ctime"
							:columns
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
			),
		),
		'standard' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_locale"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_locale" (
						"langid", "currencyid", "pos", "status",
						"mtime", "editor", "siteid", "ctime"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_locale"
					SET "langid" = ?, "currencyid" = ?, "pos" = ?,
						"status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT mloc."id" AS "locale.id", mloc."siteid" AS "locale.siteid",
						mloc."langid" AS "locale.languageid", mloc."currencyid" AS "locale.currencyid",
						mloc."pos" AS "locale.position", mloc."status" AS "locale.status",
						mloc."mtime" AS "locale.mtime", mloc."editor" AS "locale.editor",
						mloc."ctime" AS "locale.ctime"
					FROM "mshop_locale" AS mloc
					LEFT JOIN "mshop_locale_site" AS mlocsi ON (mloc."siteid" = mlocsi."id")
					LEFT JOIN "mshop_locale_language" AS mlocla ON (mloc."langid" = mlocla."id")
					LEFT JOIN "mshop_locale_currency" AS mloccu ON (mloc."currencyid" = mloccu."id")
					WHERE :cond
					GROUP BY mloc."id", mloc."siteid", mloc."langid", mloc."currencyid",
						mloc."pos", mloc."status", mloc."mtime", mloc."editor",
						mloc."ctime" :columns
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT DISTINCT mloc."id"
						FROM "mshop_locale" AS mloc
						LEFT JOIN "mshop_locale_site" AS mlocsi ON (mloc."siteid" = mlocsi."id")
						LEFT JOIN "mshop_locale_language" AS mlocla ON (mloc."langid" = mlocla."id")
						LEFT JOIN "mshop_locale_currency" AS mloccu ON (mloc."currencyid" = mloccu."id")
						WHERE :cond
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_locale_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
	),
);