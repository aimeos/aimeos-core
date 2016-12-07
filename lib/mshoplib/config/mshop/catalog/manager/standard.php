<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'cleanup' => array(
		'ansi' => '
			DELETE FROM "mshop_catalog"
			WHERE :siteid AND "nleft" >= ? AND "nright" <= ?
		'
	),
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
				"nleft", "nright", "config", "mtime", "ctime", "editor"
			) VALUES (
				:siteid, ?, ?, ?, ?, ?, ?, ?, \'\', \'1970-01-01 00:00:00\', \'1970-01-01 00:00:00\', \'\'
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
				/*-columns*/ , :columns /*columns-*/
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
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_catalog_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

