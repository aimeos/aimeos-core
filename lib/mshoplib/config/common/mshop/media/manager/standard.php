<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_media"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_media" (
				"siteid", "langid", "typeid", "label", "mimetype", "link",
				"status", "domain", "preview", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_media"
			SET "siteid" = ?, "langid" = ?, "typeid" = ?, "label" = ?,
				"mimetype" = ?, "link" = ?, "status" = ?, "domain" = ?,
				"preview" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mmed."id", mmed."siteid", mmed."langid",
				mmed."typeid", mmed."link" AS "url", mmed."label",
				mmed."status", mmed."mimetype", mmed."domain", mmed."preview",
				mmed."mtime", mmed."editor", mmed."ctime"
			FROM "mshop_media" AS mmed
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mmed."id"
				FROM "mshop_media" AS mmed
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
