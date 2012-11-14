<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14408 2011-12-17 13:24:46Z nsendetzky $
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_media_type" ( "siteid", "code", "domain", "label", "status",
				"mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_media_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_media_type"
			WHERE "id" = ?
		',
		'search' => '
			SELECT mmedty."id", mmedty."siteid", mmedty."code", mmedty."domain", mmedty."label",
				mmedty."status", mmedty."mtime", mmedty."editor", mmedty."ctime"
			FROM "mshop_media_type" mmedty
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mmedty."id" ) AS "count"
			FROM "mshop_media_type" mmedty
			:joins
			WHERE :cond
			LIMIT 10000 OFFSET 0
		',
	),
);
