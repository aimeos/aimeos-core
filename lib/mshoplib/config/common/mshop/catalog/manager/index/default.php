<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'aggregate' => '
		SELECT :key AS "key", COUNT(*) AS "count"
		FROM "mshop_product" mpro
		:joins
		WHERE :cond
		GROUP BY :key
	',
	'item' => array(
		'search' => '
			SELECT DISTINCT mpro."id"
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
				LIMIT 1000 OFFSET 0
			) AS list
		',
	),
);
