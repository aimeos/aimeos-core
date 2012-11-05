<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14408 2011-12-17 13:24:46Z nsendetzky $
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_attribute_type" ( "siteid", "code", "domain", "label", "status",
				"mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_attribute_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_attribute_type"
			WHERE "id" = ?
		',
		'search' => '
			SELECT mattty."id", mattty."siteid", mattty."code", mattty."domain", mattty."label",
				mattty."status", mattty."mtime", mattty."ctime", mattty."editor"
			FROM "mshop_attribute_type" AS mattty
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mattty."id" ) AS "count"
			FROM "mshop_attribute_type" AS mattty
			:joins
			WHERE :cond
		',
	),
);
