<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


return array(
	'manager' => array(
		'cleanup' => 'DELETE FROM "madmin_cache" WHERE "expire" < ?',
		'clear' => 'DELETE FROM "madmin_cache"',
		'delete' => 'DELETE FROM "madmin_cache" WHERE "id" IN (?)',
		'deletebytag' => '
			DELETE FROM "madmin_cache" WHERE "id" IN (
				SELECT "tid" FROM "madmin_cache_tag" WHERE "tname" IN (?)
			)
		',
		'get' => '
			SELECT "id", "value", "expire" FROM "madmin_cache"
			WHERE ( "expire" >= ? OR "expire" IS NULL ) AND "id" IN (?)
		',
		'set' => '
			INSERT INTO "madmin_cache" (
				"id", "expire", "value"
			) VALUES (
				?, ?, ?
			)
		',
		'settag' => '
			INSERT INTO "madmin_cache_tag" (
				"tid", "tname"
			) VALUES (
				?, ?
			)
		',
		'search' => array(
			'ansi' => '
				SELECT "id", "value", "expire"
				FROM "madmin_cache"
				WHERE :cond
				ORDER BY "id"
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT "id", "value", "expire"
				FROM "madmin_cache"
				WHERE :cond
				ORDER BY "id"
				LIMIT :size OFFSET :start
			',
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM(
					SELECT "id"
					FROM "madmin_cache"
					WHERE :cond
					ORDER BY "id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM(
					SELECT "id"
					FROM "madmin_cache"
					WHERE :cond
					ORDER BY "id"
					LIMIT 10000 OFFSET 0
				) AS list
			',
		),
	),
);
