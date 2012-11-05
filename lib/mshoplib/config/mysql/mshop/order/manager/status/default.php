<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14389 2011-12-16 14:31:10Z doleiynyk $
 */

return array(
	'item' => array(
		'newid' => '
			SELECT LAST_INSERT_ID()
		',
	),
);
