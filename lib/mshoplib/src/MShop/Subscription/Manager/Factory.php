<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 * @package MShop
 * @subpackage Subscription
 */


namespace Aimeos\MShop\Subscription\Manager;


/**
 * Factory for a subscription manager.
 *
 * @package MShop
 * @subpackage Subscription
 */
class Factory
	extends \Aimeos\MShop\Common\Factory\Base
	implements \Aimeos\MShop\Common\Factory\Iface
{
	/**
	 * Creates an subscription manager default DAO object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Shop context instance with necessary objects
	 * @param string|null $name Manager name
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 * @throws \Aimeos\MShop\Subscription\Exception|\Aimeos\MShop\Exception If requested manager
	 * implementation couldn't be found or initialisation fails
	 */
	public static function create( \Aimeos\MShop\Context\Item\Iface $context, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/subscription/manager/name
		 * Class name of the used subscription manager implementation
		 *
		 * Each default manager can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Subscription\Manager\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Subscription\Manager\Mymanager
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/subscription/manager/name = Mymanager
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
		 * @since 2018.04
		 * @category Developer
		 */
		if( $name === null ) {
			$name = $context->getConfig()->get( 'mshop/subscription/manager/name', 'Standard' );
		}

		$iface = \Aimeos\MShop\Subscription\Manager\Iface::class;
		$classname = '\Aimeos\MShop\Subscription\Manager\\' . $name;

		if( ctype_alnum( $name ) === false )
		{
			$msg = $context->translate( 'mshop', 'Invalid characters in class name "%1$s"' );
			throw new \Aimeos\MShop\Subscription\Exception( sprintf( $msg, $classname ) );
		}

		$manager = self::createManager( $context, $classname, $iface );

		/** mshop/subscription/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the subscription manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the subscription manager.
		 *
		 *  mshop/subscription/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the subscription manager.
		 *
		 * @param array List of decorator names
		 * @since 2018.04
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/subscription/manager/decorators/global
		 * @see mshop/subscription/manager/decorators/local
		 */

		/** mshop/subscription/manager/decorators/global
		 * Adds a list of globally available decorators only to the subscription manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the subscription
		 * manager.
		 *
		 *  mshop/subscription/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the
		 * subscription manager.
		 *
		 * @param array List of decorator names
		 * @since 2018.04
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/subscription/manager/decorators/excludes
		 * @see mshop/subscription/manager/decorators/local
		 */

		/** mshop/subscription/manager/decorators/local
		 * Adds a list of local decorators only to the subscription manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Subscription\Manager\Decorator\*") around the subscription
		 * manager.
		 *
		 *  mshop/subscription/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Subscription\Manager\Decorator\Decorator2" only to the
		 * subscription manager.
		 *
		 * @param array List of decorator names
		 * @since 2018.04
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/subscription/manager/decorators/excludes
		 * @see mshop/subscription/manager/decorators/global
		 */
		return self::addManagerDecorators( $context, $manager, 'subscription' );
	}

}
