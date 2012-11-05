<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 937 2012-07-12 10:47:51Z nsendetzky $
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_price_list_type"( "siteid", "code", "domain", "label", "status", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_price_list_type"
			SET "siteid"=?, "code" = ?, "domain" = ?, "label" = ?, "status" = ?,"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_price_list_type"
			WHERE "id" = ?
		',
		'search' => '
			SELECT mprility."id", mprility."siteid", mprility."code", mprility."domain", mprility."label",
				mprility."status", mprility."mtime", mprility."editor", mprility."ctime"
			FROM "mshop_price_list_type" AS mprility
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mprility."id" ) AS "count"
			FROM "mshop_price_list_type" AS mprility
			:joins
			WHERE :cond
		',
	),
);
