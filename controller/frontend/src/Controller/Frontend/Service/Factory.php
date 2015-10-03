<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Frontend
 */


/**
 * Service frontend controller factory.
 *
 * @package Controller
 * @subpackage Frontend
 */
class Controller_Frontend_Service_Factory
	extends Controller_Frontend_Common_Factory_Base
	implements Controller_Frontend_Common_Factory_Interface
{
	public static function createController( MShop_Context_Item_Interface $context, $name = null )
	{
		/** classes/controller/frontend/service/name
		 * Class name of the used service frontend controller implementation
		 *
		 * Each default frontend controller can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the controller factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  Controller_Frontend_Service_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  Controller_Frontend_Service_Myservice
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/controller/jobs/frontend/service/name = Myservice
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyService"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */
		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/controller/frontend/service/name', 'Default' );
		}

		if( ctype_alnum( $name ) === false ) {
			$classname = is_string( $name ) ? 'Controller_Frontend_Service_' . $name : '<not a string>';
			throw new Controller_Frontend_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'Controller_Frontend_Service_Interface';
		$classname = 'Controller_Frontend_Service_' . $name;

		$manager = self::createControllerBase( $context, $classname, $iface );

		/** controller/frontend/service/decorators/excludes
		 * Excludes decorators added by the "common" option from the service frontend controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "controller/frontend/common/decorators/default" before they are wrapped
		 * around the frontend controller.
		 *
		 *  controller/frontend/service/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("Controller_Frontend_Common_Decorator_*") added via
		 * "controller/frontend/common/decorators/default" for the service frontend controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see controller/frontend/common/decorators/default
		 * @see controller/frontend/service/decorators/global
		 * @see controller/frontend/service/decorators/local
		 */

		/** controller/frontend/service/decorators/global
		 * Adds a list of globally available decorators only to the service frontend controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("Controller_Frontend_Common_Decorator_*") around the frontend controller.
		 *
		 *  controller/frontend/service/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "Controller_Frontend_Common_Decorator_Decorator1" only to the frontend controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see controller/frontend/common/decorators/default
		 * @see controller/frontend/service/decorators/excludes
		 * @see controller/frontend/service/decorators/local
		 */

		/** controller/frontend/service/decorators/local
		 * Adds a list of local decorators only to the service frontend controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("Controller_Frontend_Service_Decorator_*") around the frontend controller.
		 *
		 *  controller/frontend/service/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "Controller_Frontend_Catalog_Decorator_Decorator2" only to the frontend
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see controller/frontend/common/decorators/default
		 * @see controller/frontend/service/decorators/excludes
		 * @see controller/frontend/service/decorators/global
		 */
		return self::addControllerDecorators( $context, $manager, 'service' );
	}
}
