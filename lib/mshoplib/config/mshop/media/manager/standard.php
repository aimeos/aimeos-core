<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_media"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_media" (
				"siteid", "langid", "typeid", "label", "mimetype", "link",
				"status", "domain", "preview", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_media"
			SET "siteid" = ?, "langid" = ?, "typeid" = ?, "label" = ?,
				"mimetype" = ?, "link" = ?, "status" = ?, "domain" = ?,
				"preview" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mmed."id" AS "media.id", mmed."siteid" AS "media.siteid",
				mmed."langid" AS "media.languageid", mmed."typeid" AS "media.typeid",
				mmed."link" AS "media.url", mmed."label" AS "media.label",
				mmed."status" AS "media.status", mmed."mimetype" AS "media.mimetype",
				mmed."domain" AS "media.domain", mmed."preview" AS "media.preview",
				mmed."mtime" AS "media.mtime", mmed."editor" AS "media.editor",
				mmed."ctime" AS "media.ctime"
			FROM "mshop_media" AS mmed
			:joins
			WHERE :cond
			GROUP BY mmed."id", mmed."siteid", mmed."langid", mmed."typeid",
				mmed."link", mmed."label", mmed."status", mmed."mimetype",
				mmed."domain", mmed."preview", mmed."mtime", mmed."editor",
				mmed."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mmed."id"
				FROM "mshop_media" AS mmed
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_media_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

