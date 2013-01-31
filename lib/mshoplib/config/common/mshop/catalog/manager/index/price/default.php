<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 1168 2012-08-29 10:45:21Z doleiynyk $
 */

return array(
	'item' => array(
		'delete' => 'DELETE FROM "mshop_catalog_index_price" WHERE :cond AND "siteid" = ?',
		'insert' => '
			INSERT INTO "mshop_catalog_index_price" ("prodid", "siteid", "priceid", "currencyid", "listtype", "type", "value",
				"shipping", "rebate", "taxrate", "quantity", "mtime", "editor", "ctime" )
			VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )
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
				LIMIT 10000 OFFSET 0
			) AS list
		',
	)
);