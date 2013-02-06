<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 1334 2012-10-24 16:17:46Z doleiynyk $
 */

return array(
	'item' => array(
		'delete' => 'DELETE FROM "mshop_catalog_index_text" WHERE "prodid" = ? AND "siteid" = ?',
		'insert' => '
			INSERT INTO "mshop_catalog_index_text" ("prodid", "siteid", "textid", "langid", "listtype", "type", "domain", "value",
				"mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
		',
		'search' => '
			SELECT DISTINCT mpro."id", mpro."siteid", mpro."typeid", mpro."label", mpro."status",
				mpro."start", mpro."end", mpro."code", mpro."suppliercode",
				mpro."ctime", mpro."mtime", mpro."editor"
			FROM "mshop_product" AS mpro
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mpro."id"
				FROM "mshop_product" AS mpro
				:joins
				WHERE :cond
			) AS list
		',
	),
	'text' => array(
		'search' => '
			SELECT DISTINCT mcatinte."prodid", mcatinte."value"
			FROM "mshop_catalog_index_text" AS mcatinte
			JOIN "mshop_catalog_index_catalog" AS mcatinca ON mcatinte."prodid" = mcatinte."prodid"
			JOIN "mshop_product" AS mpro ON mpro."id" = mcatinte."prodid"
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
	)
);