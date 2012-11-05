<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14408 2011-12-17 13:24:46Z nsendetzky $
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_locale_language"
			WHERE "id" = ?
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
			SELECT COUNT( mlocla."id" ) AS "count"
			FROM "mshop_locale_language" AS mlocla
			WHERE :cond
		',
	),
);
