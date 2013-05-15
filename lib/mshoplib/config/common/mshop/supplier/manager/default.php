<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_supplier"
			WHERE :cond
			AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_supplier" ("siteid", "code", "label", "status", "mtime", "editor", "ctime")
			VALUES ( ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_supplier"
			SET "siteid" = ?, "code" = ?, "label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT msup."id", msup."siteid", msup."code", msup."label", msup."status",
				msup."mtime", msup."editor", msup."ctime"
			FROM "mshop_supplier" AS msup
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT msup."id"
				FROM "mshop_supplier" AS msup
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);