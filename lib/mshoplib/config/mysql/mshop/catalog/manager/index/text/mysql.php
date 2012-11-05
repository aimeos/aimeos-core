<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: mysql.php 1334 2012-10-24 16:17:46Z doleiynyk $
 */

return array(
	'cleanup' => '
		DELETE FROM "mshop_catalog_index_text"
		WHERE "siteid" = ? AND (
			"prodid" NOT IN ( SELECT mpro."id" FROM "mshop_product" mpro )
			OR "textid" NOT IN ( SELECT mtex."id" FROM "mshop_text" mtex )
		)
	',
);
