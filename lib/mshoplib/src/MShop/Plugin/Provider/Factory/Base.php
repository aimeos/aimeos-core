<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Base class for plugin provider implementations
 *
 * @package MShop
 * @subpackage Plugin
 */
abstract class MShop_Plugin_Provider_Factory_Base
	extends MShop_Plugin_Provider_Base
{
	/**
	 * Initializes the object instance
	 *
	 * PHP 7 fails with a wierd fatal error that decorator constructors must be
	 * compatible with the constructor of the factory interface if this
	 * intermediate constructor isn't implemented!
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Plugin_Item_Interface $item Plugin item object
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Plugin_Item_Interface $item )
	{
		parent::__construct( $context, $item );
	}
}