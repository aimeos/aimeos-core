<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'delete' => '
		DELETE FROM "madmin_log"
		WHERE :cond
		AND "siteid" = ?
	',
	'insert' => '
		INSERT INTO "madmin_log" (
			"siteid", "facility", "timestamp", "priority", "message", "request"
		) VALUES (
			?, ?, ?, ?, ?, ?
		)
	',
	'update' => '
		UPDATE "madmin_log"
		SET "siteid" = ?, "facility" = ?, "timestamp" = ?, "priority" = ?,
			"message" = ?, "request" = ?
		WHERE "id" = ?
	',
	'search' => '
		SELECT malog."id", malog."siteid", malog."facility", malog."timestamp",
			malog."priority", malog."message", malog."request"
		FROM "madmin_log" AS malog
		:joins
		WHERE :cond
		/*-orderby*/ ORDER BY :order /*orderby-*/
		LIMIT :size OFFSET :start
	',
	'count' => '
		SELECT COUNT(*) AS "count"
		FROM(
			SELECT DISTINCT malog."id"
			FROM "madmin_log" AS malog
			:joins
			WHERE :cond
			LIMIT 10000 OFFSET 0
		) AS list
	',
);
