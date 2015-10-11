<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_service"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_service" (
				"siteid", "pos", "typeid", "code", "label", "provider",
				"config", "status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_service"
			SET "siteid" = ?, "pos" = ?, "typeid" = ?, "code" = ?, "label" = ?,
				"provider" = ?, "config" = ?, "status" = ?, "mtime" = ?,
				"editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mser."id", mser."siteid", mser."pos",
				mser."typeid", mser."code", mser."label", mser."provider",
				mser."config", mser."status", mser."mtime", mser."editor",
				mser."ctime"
			FROM "mshop_service" AS mser
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT count(*) as "count"
			FROM (
				SELECT DISTINCT mser."id"
				FROM "mshop_service" AS mser
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

