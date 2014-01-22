<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Provides example decorator for html clients.
 */
class Client_Html_Common_Decorator_Example
	extends Client_Html_Common_Decorator_Abstract
{
	public function additionalMethod()
	{
		return true;
	}
}
