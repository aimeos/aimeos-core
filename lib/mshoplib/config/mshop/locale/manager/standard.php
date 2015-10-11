<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_locale"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_locale" (
				"siteid", "langid", "currencyid", "pos", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_locale"
			SET "siteid" = ?, "langid" = ?, "currencyid" = ?, "pos" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mloc."id", mloc."siteid", mloc."langid",
			mloc."currencyid", mloc."pos", mloc."status", mloc."mtime",
			mloc."editor", mloc."ctime"
			FROM "mshop_locale" AS mloc
			LEFT JOIN "mshop_locale_site" AS mlocsi ON (mloc."siteid" = mlocsi."id")
			LEFT JOIN "mshop_locale_language" AS mlocla ON (mloc."langid" = mlocla."id")
			LEFT JOIN "mshop_locale_currency" AS mloccu ON (mloc."currencyid" = mloccu."id")
			WHERE :cond
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
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);
