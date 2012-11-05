<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 937 2012-07-12 10:47:51Z nsendetzky $
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_product_site"
			WHERE "id" = ?
		',
		'insert' => '
			INSERT INTO "mshop_product_site" ( "siteid", "parentid", "value", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_product_site"
			SET "siteid" = ?, "parentid" = ?, "value" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT mprosi."id", mprosi."siteid", mprosi."parentid", mprosi."value",
				mprosi."mtime", mprosi."ctime", mprosi."editor"
			FROM "mshop_product_site" AS mprosi
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mprosi."id") AS "count"
			FROM "mshop_product_site" AS mprosi
			:joins
			WHERE :cond
		',
	),
);
