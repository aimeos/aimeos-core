<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Frontend
 */


namespace Aimeos\Controller\Frontend\Catalog;


/**
 * Catalog frontend controller factory.
 *
 * @package Controller
 * @subpackage Frontend
 */
class Factory
	extends \Aimeos\Controller\Frontend\Common\Factory\Base
	implements \Aimeos\Controller\Frontend\Common\Factory\Iface
{
	public static function createController( \Aimeos\MShop\Context\Item\Iface $context, $name = null )
	{
		/** classes/controller/frontend/catalog/name
		 * Class name of the used catalog frontend controller implementation
		 *
		 * Each default frontend controller can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the controller factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\Controller\Frontend\Catalog\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\Controller\Frontend\Catalog\Mycatalog
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/controller/jobs/frontend/catalog/name = Mycatalog
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyCatalog"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */
		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/controller/frontend/catalog/name', 'Standard' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? '\\Aimeos\\Controller\\Frontend\\Catalog\\' . $name : '<not a string>';
			throw new \Aimeos\Controller\Frontend\Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = '\\Aimeos\\Controller\\Frontend\\Catalog\\Iface';
		$classname = '\\Aimeos\\Controller\\Frontend\\Catalog\\' . $name;

		$manager = self::createControllerBase( $context, $classname, $iface );

		/** controller/frontend/catalog/decorators/excludes
		 * Excludes decorators added by the "common" option from the catalog frontend controllers
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
		 *  controller/frontend/catalog/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Controller\Frontend\Common\Decorator\*") added via
		 * "controller/frontend/common/decorators/default" for the catalog frontend controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developers
		 * @see controller/frontend/common/decorators/default
		 * @see controller/frontend/catalog/decorators/global
		 * @see controller/frontend/catalog/decorators/local
		 */

		/** controller/frontend/catalog/decorators/global
		 * Adds a list of globally available decorators only to the catalog frontend controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Controller\Frontend\Common\Decorator\*") around the frontend controller.
		 *
		 *  controller/frontend/catalog/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Controller\Frontend\Common\Decorator\Decorator1" only to the frontend controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developers
		 * @see controller/frontend/common/decorators/default
		 * @see controller/frontend/catalog/decorators/excludes
		 * @see controller/frontend/catalog/decorators/local
		 */

		/** controller/frontend/catalog/decorators/local
		 * Adds a list of local decorators only to the catalog frontend controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Controller\Frontend\Catalog\Decorator\*") around the frontend controller.
		 *
		 *  controller/frontend/catalog/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Controller\Frontend\Catalog\Decorator\Decorator2" only to the frontend
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developers
		 * @see controller/frontend/common/decorators/default
		 * @see controller/frontend/catalog/decorators/excludes
		 * @see controller/frontend/catalog/decorators/global
		 */
		return self::addControllerDecorators( $context, $manager, 'catalog' );
	}

}
