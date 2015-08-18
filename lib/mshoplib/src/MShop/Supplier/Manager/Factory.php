<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Supplier
 */


/**
 * Factory for a supplier manager
 *
 * @package MShop
 * @subpackage Supplier
 */
class MShop_Supplier_Manager_Factory
	extends MShop_Common_Factory_Abstract
	implements MShop_Common_Factory_Interface
{
	/**
	 * Creates a supplier DAO object.
	 *
	 * @param MShop_Context_Item_Interface $context Shop context instance with necessary objects
	 * @param string $name Manager name
	 * @return MShop_Common_Manager_Interface Manager object implementing the manager interface
	 * @throws MShop_Supplier_Exception|MShop_Exception If requested manager
	 * implementation couldn't be found or initialisation fails
	 */
	public static function createManager( MShop_Context_Item_Interface $context, $name = null )
	{
		if ( $name === null ) {
			$name = $context->getConfig()->get('classes/supplier/manager/name', 'Default');
		}

		if ( ctype_alnum($name) === false )
		{
			$classname = is_string($name) ? 'MShop_Supplier_Manager_' . $name : '<not a string>';
			throw new MShop_Supplier_Exception(sprintf('Invalid characters in class name "%1$s"', $classname));
		}

		$iface = 'MShop_Supplier_Manager_Interface';
		$classname = 'MShop_Supplier_Manager_' . $name;

		$manager = self::_createManager( $context, $classname, $iface );
		return self::_addManagerDecorators( $context, $manager, 'supplier' );
	}

}