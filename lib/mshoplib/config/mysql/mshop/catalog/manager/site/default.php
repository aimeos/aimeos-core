<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 589 2012-04-25 15:24:23Z nsendetzky $
 */

return array(
	'item' => array(
		'newid' => '
			SELECT LAST_INSERT_ID()
		',
	),
);
