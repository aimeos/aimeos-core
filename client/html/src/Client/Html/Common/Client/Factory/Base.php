<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Client
 * @subpackage Html
 */


/**
 * Base class for html clients
 *
 * @package Client
 * @subpackage Html
 */
abstract class Client_Html_Common_Client_Factory_Base
	extends Client_Html_Base
	implements Client_Html_Common_Client_Factory_Iface
{
	/**
	 * Initializes the object instance
	 *
	 * @param MShop_Context_Item_Iface $context Context object with required objects
	 * @param array $templatePaths Associative list of the file system paths to the core or the extensions as key
	 * 	and a list of relative paths inside the core or the extension as values
	 * @param Client_Html_Iface $client Client object
	 */
	public function __construct( MShop_Context_Item_Iface $context, array $templatePaths )
	{
		parent::__construct( $context, $templatePaths );
	}
}