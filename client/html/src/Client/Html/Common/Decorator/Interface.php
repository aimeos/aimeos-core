<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Client
 * @subpackage Html
 */


/**
 * Decorator interface for html clients.
 *
 * @package Client
 * @subpackage Html
 */
interface Client_Html_Common_Decorator_Interface
	extends Client_Html_Interface
{
	/**
	 * Initializes a new client decorator object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param array $templatePaths Associative list of the file system paths to the core or the extensions as key
	 * 	and a list of relative paths inside the core or the extension as values
	 * @param Client_Html_Interface $client Client object
	 */
	public function __construct( MShop_Context_Item_Interface $context, array $templatePaths, Client_Html_Interface $client );
}