<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14408 2011-12-17 13:24:46Z nsendetzky $
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_attribute"
			WHERE "id" = ?
		',
		'insert' => '
			INSERT INTO "mshop_attribute" ("siteid", "typeid", "domain", "code", "status", "pos", "label",
				"mtime", "editor", "ctime")
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'update' => '
			UPDATE "mshop_attribute"
			SET "siteid" = ?, "typeid" = ?, "domain" = ?, "code" = ?, "status" = ?, "pos" = ?, "label" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT matt."id", matt."siteid", matt."typeid", matt."domain", matt."code", matt."status",
				matt."pos", matt."label", matt."mtime", matt."ctime", matt."editor"
			FROM "mshop_attribute" AS matt
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT count(DISTINCT matt."id") AS "count"
			FROM "mshop_attribute" AS matt
			:joins
			WHERE :cond
			LIMIT 10000 OFFSET 0
		',
	),
);
