<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


return array(
	'manager' => array(
		'extensions' => [
			'application/pdf' => 'pdf',
			'application/postscript' => 'ps',
			'application/vnd.ms-excel' => 'xls',
			'application/vnd.ms-powerpoint' => 'ppt',
			'application/vnd.ms-word' => 'doc',
			'application/vnd.oasis.opendocument.graphics' => 'odg',
			'application/vnd.oasis.opendocument.presentation' => 'odp',
			'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
			'application/vnd.oasis.opendocument.text' => 'odt',
			'application/epub+zip' => 'epub',
			'application/x-gzip' => 'gz',
			'application/zip' => 'zip',
			'image/bmp' => 'bmp',
			'image/gif' => 'gif',
			'image/jpeg' => 'jpg',
			'image/png' => 'png',
			'image/svg+xml' => 'svg',
			'image/tiff' => 'tif',
			'image/webp' => 'webp',
			'text/csv' => 'csv',
			'video/mp4' => 'mp4',
			'video/webm' => 'webm',
			'audio/mpeg' => 'mpeg',
			'audio/ogg' => 'ogg',
			'audio/webm' => 'weba',
		],
		'previews' => [
			'common' => [[
				'force-size' => 0,
			]],
			'catalog' => [
				'stage' => [[
					'maxwidth' => 960,
				], [
					'maxwidth' => 1920,
				]],
			],
			'product' => [[
				'maxwidth' => 240,
				'maxheight' => 320,
				'force-size' => 1,
			], [
				'maxwidth' => 480,
				'maxheight' => 640,
				'force-size' => 1,
			], [
				'maxwidth' => 960,
				'maxheight' => 1280,
				'force-size' => 1,
			], [
				'maxwidth' => 1920,
			]],
		],
		'property' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_media_property"
					WHERE :cond AND "siteid" LIKE ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_media_property" ( :names
						"parentid", "key", "type", "langid", "value",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_media_property"
					SET :names
						"parentid" = ?, "key" = ?, "type" = ?, "langid" = ?,
						"value" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" LIKE ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
					FROM "mshop_media_property" mmedpr
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
					FROM "mshop_media_property" mmedpr
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mmedpr."id"
						FROM "mshop_media_property" mmedpr
						:joins
						WHERE :cond
						ORDER BY mmedpr."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mmedpr."id"
						FROM "mshop_media_property" mmedpr
						:joins
						WHERE :cond
						ORDER BY mmedpr."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_media_property_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_media"
				WHERE :cond AND "siteid" LIKE ?
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_media" ( :names
					"langid", "type", "label", "mimetype", "link", "status", "fsname",
					"domain", "preview", "mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_media"
				SET :names
					"langid" = ?, "type" = ?, "label" = ?, "mimetype" = ?, "link" = ?, "status" = ?,
					"fsname" = ?, "domain" = ?, "preview" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" LIKE ? AND "id" = ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
				FROM "mshop_media" mmed
				:joins
				WHERE :cond
				GROUP BY :group
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
				FROM "mshop_media" mmed
				:joins
				WHERE :cond
				GROUP BY :group
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mmed."id"
					FROM "mshop_media" mmed
					:joins
					WHERE :cond
					GROUP BY mmed."id"
					ORDER BY mmed."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mmed."id"
					FROM "mshop_media" mmed
					:joins
					WHERE :cond
					GROUP BY mmed."id"
					ORDER BY mmed."id"
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
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
