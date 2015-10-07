<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_product_tag"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_product_tag" (
				"siteid", "langid", "typeid", "label", "mtime", "editor",
				"ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_product_tag"
			SET "siteid" = ?, "langid" = ?, "typeid" = ?, "label" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mprota."id", mprota."siteid", mprota."typeid",
				mprota."langid", mprota."label", mprota."mtime",
				mprota."editor", mprota."ctime"
			FROM "mshop_product_tag" AS mprota
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mprota."id"
				FROM "mshop_product_tag" AS mprota
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
