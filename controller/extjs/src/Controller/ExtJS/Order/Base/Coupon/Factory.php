<?php

/**
 * @copyright Copyright (c) 2010, Metaways Infosystems GmbH
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Order\Base\Coupon;


/**
 * ExtJS Order base coupon controller factory.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Factory
	extends \Aimeos\Controller\ExtJS\Common\Factory\Base
	implements \Aimeos\Controller\ExtJS\Common\Factory\Iface
{
	public static function createController( \Aimeos\MShop\Context\Item\Iface $context, $name = null )
	{
		/** classes/controller/extjs/order/base/coupon/name
		 * Class name of the used ExtJS order base coupon controller implementation
		 *
		 * Each default ExtJS controller can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the client factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\Controller\ExtJS\Order\Base\Coupon\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\Controller\ExtJS\Order\Base\Coupon\Mycoupon
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/controller/extjs/order/base/coupon/name = Mycoupon
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyCoupon"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.05
		 * @category Developer
		 */
		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/controller/extjs/order/base/coupon/name', 'Standard' );
		}

		$interface = '\\Aimeos\\Controller\\ExtJS\\Iface';
		$classname = '\\Aimeos\\Controller\\ExtJS\\Order\\Base\\Coupon\\' . $name;

		$controller = self::createControllerBase( $context, $classname, $interface );

		/** controller/extjs/order/base/coupon/decorators/excludes
		 * Excludes decorators added by the "common" option from the order base coupon ExtJS controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "controller/extjs/common/decorators/default" before they are wrapped
		 * around the ExtJS controller.
		 *
		 *  controller/extjs/order/base/coupon/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Controller\ExtJS\Common\Decorator\*") added via
		 * "controller/extjs/common/decorators/default" for the admin ExtJS controller.
		 *
		 * @param array List of decorator names
		 * @since 2015.09
		 * @category Developer
		 * @see controller/extjs/common/decorators/default
		 * @see controller/extjs/order/base/coupon/decorators/global
		 * @see controller/extjs/order/base/coupon/decorators/local
		*/

		/** controller/extjs/order/base/coupon/decorators/global
		 * Adds a list of globally available decorators only to the order base coupon ExtJS controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Controller\ExtJS\Common\Decorator\*") around the ExtJS controller.
		 *
		 *  controller/extjs/order/base/coupon/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Controller\ExtJS\Common\Decorator\Decorator1" only to the ExtJS controller.
		 *
		 * @param array List of decorator names
		 * @since 2015.09
		 * @category Developer
		 * @see controller/extjs/common/decorators/default
		 * @see controller/extjs/order/base/coupon/decorators/excludes
		 * @see controller/extjs/order/base/coupon/decorators/local
		*/

		/** controller/extjs/order/base/coupon/decorators/local
		 * Adds a list of local decorators only to the order base coupon ExtJS controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Controller\ExtJS\Order\Base\Coupon\Decorator\*") around the ExtJS controller.
		 *
		 *  controller/extjs/order/base/coupon/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Controller\ExtJS\Order\Base\Coupon\Decorator\Decorator2" only to the ExtJS
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2015.09
		 * @category Developer
		 * @see controller/extjs/common/decorators/default
		 * @see controller/extjs/order/base/coupon/decorators/excludes
		 * @see controller/extjs/order/base/coupon/decorators/global
		*/
		return self::addControllerDecorators( $context, $controller, 'order/base/coupon' );
	}
}
