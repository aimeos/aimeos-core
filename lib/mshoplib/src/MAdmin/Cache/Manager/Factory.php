<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MAdmin
 * @subpackage Cache
 */


/**
 * Admin cache factory.
 *
 * @package MAdmin
 * @subpackage Cache
 */
class MAdmin_Cache_Manager_Factory
	extends MAdmin_Common_Factory_Abstract
	implements MShop_Common_Factory_Interface
{
	/**
	 * Creates an admin cache manager object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param string $name Manager name
	 * @return MShop_Common_Manager_Interface Manager object
	 */
	public static function createManager( MShop_Context_Item_Interface $context, $name = null )
	{
		/** classes/cache/manager/name
		 * Class name of the used cache manager implementation
		 *
		 * Each default manager can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Cache_Manager_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Cache_Manager_Mymanager
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/cache/manager/name = Mymanager
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
			$name = $context->getConfig()->get( 'classes/cache/manager/name', 'Default' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? 'MAdmin_Cache_Manager_' . $name : '<not a string>';
			throw new MAdmin_Cache_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'MAdmin_Cache_Manager_Interface';
		$classname = 'MAdmin_Cache_Manager_' . $name;

		$manager = self::createManagerBase( $context, $classname, $iface );

		/** madmin/cache/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the cache manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. cache what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for cacheged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "madmin/common/manager/decorators/default" before they are wrapped
		 * around the cache manager.
		 *
		 *  madmin/cache/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "madmin/common/manager/decorators/default" for the cache manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see madmin/common/manager/decorators/default
		 * @see madmin/cache/manager/decorators/global
		 * @see madmin/cache/manager/decorators/local
		 */

		/** madmin/cache/manager/decorators/global
		 * Adds a list of globally available decorators only to the cache manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. cache what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for cacheged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the cache manager.
		 *
		 *  madmin/cache/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the cache controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see madmin/common/manager/decorators/default
		 * @see madmin/cache/manager/decorators/excludes
		 * @see madmin/cache/manager/decorators/local
		 */

		/** madmin/cache/manager/decorators/local
		 * Adds a list of local decorators only to the cache manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. cache what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for cacheged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the cache manager.
		 *
		 *  madmin/cache/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the cache
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see madmin/common/manager/decorators/default
		 * @see madmin/cache/manager/decorators/excludes
		 * @see madmin/cache/manager/decorators/global
		 */
		return self::addManagerDecorators( $context, $manager, 'cache' );
	}
}
