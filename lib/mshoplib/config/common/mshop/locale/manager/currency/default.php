<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14408 2011-12-17 13:24:46Z nsendetzky $
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_locale_currency"
			WHERE "id" = ?
		',
		'insert' => '
			INSERT INTO "mshop_locale_currency" ("label", "status", "siteid", "mtime", "editor", "id", "ctime")
			VALUES( ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_locale_currency"
			SET "label" = ?, "status" = ?, "siteid"=?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT mloccu."id", mloccu."label", mloccu."siteid", mloccu."status",
				mloccu."mtime", mloccu."editor", mloccu."ctime"
			FROM "mshop_locale_currency" AS mloccu
			WHERE :cond
			ORDER BY :order
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mloccu."id"
				FROM "mshop_locale_currency" AS mloccu
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
