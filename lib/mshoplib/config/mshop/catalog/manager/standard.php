<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_catalog"
			WHERE "siteid" = :siteid AND "nleft" >= ? AND "nright" <= ?
		'
	),
	'get' => array(
		'ansi' => '
			SELECT mcat."id", mcat."code", mcat."label", mcat."config",
				mcat."status", mcat."level", mcat."parentid", mcat."siteid",
				mcat."nleft" AS "left", mcat."nright" AS "right",
				mcat."mtime", mcat."editor", mcat."ctime"
			FROM "mshop_catalog" AS mcat, "mshop_catalog" AS parent
			WHERE mcat."siteid" = :siteid AND mcat."nleft" >= parent."nleft"
				AND mcat."nleft" <= parent."nright"
				AND parent."siteid" = :siteid AND parent."id" = ?
				AND mcat."level" <= parent."level" + ? AND :cond
			GROUP BY mcat."id", mcat."code", mcat."label", mcat."config",
				mcat."status", mcat."level", mcat."parentid",
				mcat."siteid", mcat."nleft", mcat."nright",
				mcat."mtime", mcat."editor", mcat."ctime"
			ORDER BY mcat."nleft"
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_catalog" (
				"siteid", "label", "code", "status", "parentid", "level",
				"nleft", "nright"
			) VALUES (
				:siteid, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'insert-usage' => array(
		'ansi' => '
			UPDATE "mshop_catalog"
			SET "config" = ?, "mtime" = ?, "editor" = ?, "ctime" = ?
			WHERE "siteid" = ? AND "id" = ?
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_catalog"
			SET "label" = ?, "code" = ?, "status" = ?
			WHERE "siteid" = :siteid AND "id" = ?
		'
	),
	'update-parentid' => array(
		'ansi' => '
			UPDATE "mshop_catalog"
			SET "parentid" = ?
			WHERE "siteid" = :siteid AND "id" = ?
		'
	),
	'update-usage' => array(
		'ansi' => '
			UPDATE "mshop_catalog"
			SET "config" = ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ? AND "id" = ?
		'
	),
	'move-left' => array(
		'ansi' => '
			UPDATE "mshop_catalog"
			SET "nleft" = "nleft" + ?, "level" = "level" + ?
			WHERE "siteid" = :siteid AND "nleft" >= ? AND "nleft" <= ?
		'
	),
	'move-right' => array(
		'ansi' => '
			UPDATE "mshop_catalog"
			SET "nright" = "nright" + ?
			WHERE "siteid" = :siteid AND "nright" >= ? AND "nright" <= ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mcat."id", mcat."code", mcat."label", mcat."config",
				mcat."status", mcat."level", mcat."parentid", mcat."siteid",
				mcat."nleft" AS "left", mcat."nright" AS "right",
				mcat."mtime", mcat."editor", mcat."ctime"
			FROM "mshop_catalog" AS mcat
			WHERE mcat."siteid" = :siteid AND mcat."nleft" >= ?
				AND mcat."nright" <= ? AND :cond
			GROUP BY mcat."id", mcat."code", mcat."label", mcat."config",
				mcat."status", mcat."level", mcat."parentid", mcat."siteid",
				mcat."nleft", mcat."nright", mcat."mtime", mcat."editor", mcat."ctime"
				/*-orderby*/, :order /*orderby-*/
			ORDER BY :order
		'
	),
	'search-item' => array(
		'ansi' => '
			SELECT mcat."id", mcat."code", mcat."label", mcat."config",
				mcat."status", mcat."level", mcat."parentid", mcat."siteid",
				mcat."nleft" AS "left", mcat."nright" AS "right",
				mcat."mtime", mcat."editor", mcat."ctime"
			FROM "mshop_catalog" AS mcat
			:joins
			WHERE :cond
			GROUP BY mcat."id", mcat."code", mcat."label", mcat."config",
				mcat."status", mcat."level", mcat."parentid", mcat."siteid",
				mcat."nleft", mcat."nright", mcat."mtime", mcat."editor", mcat."ctime"
				/*-orderby*/, :order /*orderby-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcat."id"
				FROM "mshop_catalog" AS mcat
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

