<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 937 2012-07-12 10:47:51Z nsendetzky $
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_catalog_site"
			WHERE "id" = ?
		',
		'insert' => '
			INSERT INTO "mshop_catalog_site" ( "siteid", "parentid", "value", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_catalog_site"
			SET "siteid" = ?, "parentid" = ?, "value" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT mcatsi."id", mcatsi."siteid", mcatsi."parentid", mcatsi."value",
				mcatsi."mtime", mcatsi."ctime", mcatsi."editor"
			FROM "mshop_catalog_site" AS mcatsi
			:joins
			WHERE
				:cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mcatsi."id") AS "count"
			FROM "mshop_catalog_site" AS mcatsi
			:joins
			WHERE :cond
		',
	),
);
