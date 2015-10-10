<?php

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "madmin_cache" WHERE "siteid" = ? AND :cond
		',
	),
	'deletebytag' => array(
		'ansi' => '
			DELETE FROM "madmin_cache" WHERE "siteid" = ? AND id IN (
				SELECT "tid" FROM "madmin_cache_tag"
				WHERE "tsiteid" = ? AND :cond
			)
		',
	),
	'get' => array(
		'ansi' => '
			SELECT "id", "value", "expire" FROM "madmin_cache"
			WHERE "siteid" = ? AND :cond
		',
	),
	'getbytag' => array(
		'ansi' => '
			SELECT "id", "value", "expire" FROM "madmin_cache"
			JOIN "madmin_cache_tag" ON "tid" = "id"
			WHERE "siteid" = ? AND "tsiteid" = ? AND :cond
		',
	),
	'set' => array(
		'ansi' => '
			INSERT INTO "madmin_cache" (
				"id", "siteid", "expire", "value"
			) VALUES (
				?, ?, ?, ?
			)
		',
		'mysql' => '
			REPLACE INTO "madmin_cache" (
				"id", "siteid", "expire", "value"
			) VALUES (
				?, ?, ?, ?
			)
		',
	),
	'settag' => array(
		'ansi' => '
			INSERT INTO "madmin_cache_tag" (
				"tid", "tsiteid", "tname"
			) VALUES (
				?, ?, ?
			)
		',
		'mysql' => '
			REPLACE INTO "madmin_cache_tag" (
				"tid", "tsiteid", "tname"
			) VALUES :tuples
		',
	),
	'search' => array(
		'ansi' => '
			SELECT "id", "value", "expire" FROM "madmin_cache"
			WHERE :cond
			LIMIT :size OFFSET :start
		',
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM(
				SELECT DISTINCT "id"
				FROM "madmin_cache"
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);