<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Price
 */


/**
 * Factory for a price manager.
 *
 * @package MShop
 * @subpackage Price
 */
class MShop_Price_Manager_Factory
	extends MShop_Common_Factory_Base
	implements MShop_Common_Factory_Iface
{
	/**
	 * Creates a price manager DAO object.
	 *
	 * @param MShop_Context_Item_Iface $context Shop context instance with necessary objects
	 * @param string $name Manager name
	 * @return MShop_Common_Manager_Iface Manager object implementing the manager interface
	 * @throws MShop_Price_Exception|MShop_Exception If requested manager
	 * implementation couldn't be found or initialisation fails
	 */
	public static function createManager( MShop_Context_Item_Iface $context, $name = null )
	{
		/** classes/price/manager/name
		 * Class name of the used price manager implementation
		 *
		 * Each default manager can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Price_Manager_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Price_Manager_Mymanager
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/price/manager/name = Mymanager
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyManager"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */
		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/price/manager/name', 'Default' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? 'MShop_Price_Manager_' . $name : '<not a string>';
			throw new MShop_Price_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'MShop_Price_Manager_Iface';
		$classname = 'MShop_Price_Manager_' . $name;

		$manager = self::createManagerBase( $context, $classname, $iface );

		/** mshop/price/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the price manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the price manager.
		 *
		 *  mshop/price/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the price manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/price/manager/decorators/global
		 * @see mshop/price/manager/decorators/local
		 */

		/** mshop/price/manager/decorators/global
		 * Adds a list of globally available decorators only to the price manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the price manager.
		 *
		 *  mshop/price/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the price controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/price/manager/decorators/excludes
		 * @see mshop/price/manager/decorators/local
		 */

		/** mshop/price/manager/decorators/local
		 * Adds a list of local decorators only to the price manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the price manager.
		 *
		 *  mshop/price/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the price
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/price/manager/decorators/excludes
		 * @see mshop/price/manager/decorators/global
		 */
		return self::addManagerDecorators( $context, $manager, 'price' );
	}

}