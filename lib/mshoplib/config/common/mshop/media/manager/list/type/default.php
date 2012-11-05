<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14408 2011-12-17 13:24:46Z nsendetzky $
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_media_list_type"( "siteid", "code", "domain", "label", "status", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_media_list_type"
			SET "siteid"=?, "code" = ?, "domain" = ?, "label" = ?, "status" = ?,"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_media_list_type"
			WHERE "id" = ?
		',
		'search' => '
			SELECT mmedlity."id", mmedlity."siteid", mmedlity."code", mmedlity."domain", mmedlity."label",
				mmedlity."status", mmedlity."mtime", mmedlity."editor", mmedlity."ctime"
			FROM "mshop_media_list_type" AS mmedlity
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mmedlity."id" ) AS "count"
			FROM "mshop_media_list_type" AS mmedlity
			:joins
			WHERE :cond
		',
	),
);
