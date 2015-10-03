<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Provides example decorator for html clients.
 */
class Client_Html_Common_Decorator_Example
	extends Client_Html_Common_Decorator_Base
	implements Client_Html_Common_Decorator_Iface
{
	public function additionalMethod()
	{
		return true;
	}


	protected function getSubClientNames()
	{
		return array();
	}
}
