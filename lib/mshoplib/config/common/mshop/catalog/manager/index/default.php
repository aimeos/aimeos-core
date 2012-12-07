<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14509 2011-12-20 16:06:38Z nsendetzky $
 */

return array(
	'item' => array(
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
