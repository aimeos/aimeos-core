<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_customer_list_type"( "siteid", "code", "domain", "label", "status",
				"mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_customer_list_type"
			SET "siteid"=?, "code" = ?, "domain" = ?, "label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_customer_list_type"
			WHERE "id" = ?
		',
		'search' => '
			SELECT mcuslity."id", mcuslity."siteid", mcuslity."code", mcuslity."domain", mcuslity."label", mcuslity."status",
				mcuslity."mtime", mcuslity."editor", mcuslity."ctime"
			FROM "mshop_customer_list_type" AS mcuslity
			:joins
			WHERE
				:cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mcuslity."id" ) AS "count"
			FROM "mshop_customer_list_type" as mcuslity
			:joins
			WHERE
				:cond
			LIMIT 10000 OFFSET 0
		',
	),
);
