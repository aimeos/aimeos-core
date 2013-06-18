<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_locale_language"
			WHERE :cond
			AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_locale_language" ("label", "status", "siteid", "mtime", "editor", "id", "ctime")
			VALUES( ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_locale_language"
			SET "label" = ?, "status" = ?, "siteid"=?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT mlocla."id", mlocla."label", mlocla."siteid", mlocla."status",
				mlocla."mtime", mlocla."editor", mlocla."ctime"
			FROM "mshop_locale_language" AS mlocla
			WHERE :cond
			ORDER BY :order
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mlocla."id"
				FROM "mshop_locale_language" AS mlocla
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
