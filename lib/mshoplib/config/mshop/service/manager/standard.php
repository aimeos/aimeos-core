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
			SELECT DISTINCT mser."id" AS "service.id", mser."siteid" AS "service.siteid",
				mser."pos" AS "service.position", mser."typeid" AS "service.typeid",
				mser."code" AS "service.code", mser."label" AS "service.label",
				mser."provider" AS "service.provider", mser."config" AS "service.config",
				mser."status" AS "service.status", mser."mtime" AS "service.mtime",
				mser."editor" AS "service.editor",	mser."ctime" AS "service.ctime"
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

