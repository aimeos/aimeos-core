<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_catalog"
			WHERE "siteid" = :siteid AND "nleft" >= ? AND "nright" <= ?
		',
		'get' => '
			SELECT
				mcat."id", mcat."label", mcat."config", mcat."code", mcat."status", mcat."level",
				mcat."parentid", mcat."siteid", mcat."nleft" AS "left", mcat."nright" AS "right",
				mcat."mtime", mcat."editor", mcat."ctime"
			FROM "mshop_catalog" AS mcat, "mshop_catalog" AS parent
			WHERE
				mcat."siteid" = :siteid AND mcat."nleft" >= parent."nleft" AND mcat."nleft" <= parent."nright"
				AND parent."siteid" = :siteid AND parent."id" = ? AND mcat."level" <= parent."level" + ?
				AND :cond
			ORDER BY mcat."nleft"
		',
		'insert' => '
			INSERT INTO "mshop_catalog" ( "siteid", "label", "code", "status", "parentid", "level", "nleft", "nright" )
			VALUES ( :siteid, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_catalog"
			SET "label" = ?, "code" = ?, "status" = ?
			WHERE "siteid" = :siteid AND "id" = ?
		',
		'update-parentid' => '
			UPDATE "mshop_catalog"
			SET "parentid" = ?
			WHERE "siteid" = :siteid AND "id" = ?
		',
		'move-left' => '
			UPDATE "mshop_catalog"
			SET "nleft" = "nleft" + ?, "level" = "level" + ?
			WHERE "siteid" = :siteid AND "nleft" >= ? AND "nleft" <= ?
		',
		'move-right' => '
			UPDATE "mshop_catalog"
			SET "nright" = "nright" + ?
			WHERE "siteid" = :siteid AND "nright" >= ? AND "nright" <= ?
		',
		'search' => '
			SELECT
				mcat."id", mcat."label", mcat."config", mcat."code", mcat."status", mcat."level",
				mcat."siteid", mcat."nleft" AS "left", mcat."nright" AS "right",
				mcat."mtime", mcat."editor", mcat."ctime"
			FROM "mshop_catalog" AS mcat
			WHERE mcat."siteid" = :siteid AND mcat."nleft" >= ? AND mcat."nright" <= ? AND :cond
			ORDER BY :order
		',
		'search-item' => '
			SELECT DISTINCT
				mcat."id", mcat."label", mcat."config", mcat."code", mcat."status", mcat."level", mcat."parentid",
				mcat."siteid", mcat."nleft" AS "left", mcat."nright" AS "right",
				mcat."mtime", mcat."editor", mcat."ctime"
			FROM "mshop_catalog" AS mcat
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcat."id"
				FROM "mshop_catalog" AS mcat
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
		'usage' => array(
			'update' => '
				UPDATE "mshop_catalog"
				SET "config" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" = ? AND "id" = ?
			',
			'add' => '
				UPDATE "mshop_catalog"
				SET "config" = ?, "mtime" = ?, "editor" = ?, "ctime" = ?
				WHERE "siteid" = ? AND "id" = ?
			',
		),
	),
);
