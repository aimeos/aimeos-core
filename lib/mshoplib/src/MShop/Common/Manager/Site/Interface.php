<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Interface.php 589 2012-04-25 15:24:23Z nsendetzky $
 */


/**
 * Default site manager implementation
 * 
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Manager_Site_Interface
	extends MShop_Common_Manager_Interface
{
	/**
	 * Creates the common site manager using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param array $config array with SQL statements
	 * @param array $searchConfig array with search configuration
	 * @throws MShop_Common_Exception if no configuration is available
	 */
	public function __construct( MShop_Context_Item_Interface $context,	array $config, array $searchConfig );

}