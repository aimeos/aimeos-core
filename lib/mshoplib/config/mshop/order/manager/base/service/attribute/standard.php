<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'aggregate' => array(
		'ansi' => '
		SELECT "key", COUNT("id") AS "count"
		FROM (
			SELECT :key AS "key", mordbaseat."id" AS "id"
			FROM "mshop_order_base_service_attr" AS mordbaseat
			:joins
			WHERE :cond
			GROUP BY :key, mordbaseat."id" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		) AS list
		GROUP BY "key"
	'
	),
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_order_base_service_attr"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_order_base_service_attr" (
				"siteid", "attrid", "ordservid", "type", "code", "value",
				"name", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_order_base_service_attr"
			SET "siteid" = ?, "attrid" = ?, "ordservid" = ?, "type" = ?,
				"code" = ?, "value" = ?, "name" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mordbaseat."id" AS "order.base.service.attribute.id", mordbaseat."siteid" AS "order.base.service.attribute.siteid",
				mordbaseat."attrid" AS "order.base.service.attribute.attributeid", mordbaseat."ordservid" AS "order.base.service.attribute.parentid",
				mordbaseat."type" AS "order.base.service.attribute.type", mordbaseat."code" AS "order.base.service.attribute.code",
				mordbaseat."value" AS "order.base.service.attribute.value", mordbaseat."name" AS "order.base.service.attribute.name",
				mordbaseat."mtime" AS "order.base.service.attribute.mtime", mordbaseat."ctime" AS "order.base.service.attribute.ctime",
				mordbaseat."editor" AS "order.base.service.attribute.editor"
			FROM "mshop_order_base_service_attr" AS mordbaseat
			:joins
			WHERE :cond
			GROUP BY mordbaseat."id", mordbaseat."siteid", mordbaseat."attrid", mordbaseat."ordservid",
				mordbaseat."type", mordbaseat."code", mordbaseat."value", mordbaseat."name",
				mordbaseat."mtime", mordbaseat."ctime", mordbaseat."editor" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT( DISTINCT mordbaseat."id" ) AS "count"
			FROM "mshop_order_base_service_attr" AS mordbaseat
			:joins
			WHERE :cond
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_order_base_service_attr_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);
