<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14408 2011-12-17 13:24:46Z nsendetzky $
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_locale_site"
			WHERE "id" = ?
		',
		'insert' => '
			INSERT INTO "mshop_locale_site" ( "code", "label", "config", "status", "editor", "mtime", "ctime", "level", "nleft", "nright" )
			SELECT ?, ?, ?, ?, ?, ?, ?, 0, COALESCE( MAX("nright"), 0 ) + 1, COALESCE( MAX("nright"), 0 ) + 2
			FROM "mshop_locale_site"
		',
		'update' => '
			UPDATE "mshop_locale_site"
			SET "code" = ?, "label" = ?, "config" = ?, "status" = ?, "editor" = ?, "mtime" = ?
			WHERE id = ?
		',
		'search' => '
			SELECT mlocsi."id", mlocsi."code", mlocsi."label", mlocsi."config", mlocsi."status",
				mlocsi."editor", mlocsi."mtime", mlocsi."ctime"
			FROM "mshop_locale_site" AS mlocsi
			WHERE :cond
			ORDER BY :order
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mlocsi."id"
				FROM "mshop_locale_site" AS mlocsi
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
