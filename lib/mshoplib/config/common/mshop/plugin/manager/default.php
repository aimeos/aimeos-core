<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14854 2012-01-13 12:54:14Z doleiynyk $
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_plugin"( "siteid", "typeid", "label", "provider", "config", "pos", "status", "mtime", "editor", "ctime")
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_plugin"
			SET "siteid" = ?, "typeid" = ?, "label" = ?, "provider" = ?, "config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_plugin"
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mplu."id", mplu."siteid", mplu."typeid", mplu."label", mplu."provider",
				mplu."config", mplu."pos", mplu."status", mplu."mtime", mplu."editor", mplu."ctime"
			FROM "mshop_plugin" mplu
			:joins
			WHERE
				:cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mplu."id"
				FROM "mshop_plugin" mplu
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	)
);
