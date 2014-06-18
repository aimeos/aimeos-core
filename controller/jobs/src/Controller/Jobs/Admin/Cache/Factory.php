<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Admin cache controller factory.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Admin_Cache_Factory
	extends Controller_Jobs_Common_Factory_Abstract
	implements Controller_Jobs_Common_Factory_Interface
{
	/**
	 * Creates a new controller specified by the given name.
	 *
	 * @param MShop_Context_Item_Interface $context Context object required by controllers
	 * @param Arcavias $arcavias Arcavias object
	 * @param string|null $name Name of the controller or "Default" if null
	 * @return Controller_Jobs_Interface New controller object
	 */
	public static function createController( MShop_Context_Item_Interface $context, Arcavias $arcavias, $name = null )
	{
		/** classes/controller/jobs/admin/cache/name
		 * Class name of the used admin cache scheduler controller implementation
		 *
		 * Each default cache controller can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the controller factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  Controller_Jobs_Admin_Cache_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  Controller_Jobs_Admin_Cache_Mycache
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/controller/jobs/admin/cache/name = Mycache
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyCache"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */
		if ( $name === null ) {
			$name = $context->getConfig()->get('classes/controller/jobs/admin/cache/name', 'Default');
		}

		if ( ctype_alnum($name) === false )
		{
			$classname = is_string($name) ? 'Controller_Jobs_Admin_Cache_' . $name : '<not a string>';
			throw new Controller_Jobs_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'Controller_Jobs_Interface';
		$classname = 'Controller_Jobs_Admin_Cache_' . $name;

		$controller = self::_createController( $context, $arcavias, $classname, $iface );

		/** controller/jobs/admin/decorators/excludes
		 * Excludes decorators added by the "common" option from the admin cache controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "controller/jobs/common/decorators/default" before they are wrapped
		 * around the cache controller.
		 *
		 *  controller/jobs/admin/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("Controller_Jobs_Common_Decorator_*") added via
		 * "controller/jobs/common/decorators/default" for the admin cache controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see controller/jobs/common/decorators/default
		 * @see controller/jobs/admin/decorators/global
		 * @see controller/jobs/admin/decorators/local
		 */

		/** controller/jobs/admin/decorators/global
		 * Adds a list of globally available decorators only to the admin cache controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("Controller_Jobs_Common_Decorator_*") around the cache controller.
		 *
		 *  controller/jobs/admin/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "Controller_Jobs_Common_Decorator_Decorator1" only to the cache controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see controller/jobs/common/decorators/default
		 * @see controller/jobs/admin/decorators/excludes
		 * @see controller/jobs/admin/decorators/local
		 */

		/** controller/jobs/admin/decorators/local
		 * Adds a list of local decorators only to the admin cache controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("Controller_Jobs_Admin_Decorator_*") around the cache controller.
		 *
		 *  controller/jobs/admin/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "Controller_Jobs_Admin_Decorator_Decorator2" only to the cache
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see controller/jobs/common/decorators/default
		 * @see controller/jobs/admin/decorators/excludes
		 * @see controller/jobs/admin/decorators/global
		 */
		return self::_addControllerDecorators( $context, $arcavias, $controller, 'admin/cache' );
	}
}