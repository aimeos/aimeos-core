<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id $
 */


/**
 * Interface for all type manager implementations.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Manager_Type_Interface
	extends MShop_Common_Manager_Interface
{
	/**
	 * Creates the type manager using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param array $config Associative list of SQL statements
	 * @param array $searchConfig Associative list of search configuration
	 *
	 * @throws MShop_Common_Exception if no configuration is available
	 */
	public function __construct( MShop_Context_Item_Interface $context, array $config, array $searchConfig );

}
