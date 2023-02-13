<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package MShop
 * @subpackage Coupon
 */

namespace Aimeos\MShop\Coupon\Manager;


/**
 * Abstract class for coupon managers.
 *
 * @package MShop
 * @subpackage Coupon
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
{
	/**
	 * Returns the coupon model which belongs to the given code.
	 *
	 * @param \Aimeos\MShop\Coupon\Item\Iface $item Coupon item interface
	 * @param string $code Coupon code
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Returns a coupon provider instance
	 * @throws \LogicException If coupon provider couldn't be found
	 */
	public function getProvider( \Aimeos\MShop\Coupon\Item\Iface $item, string $code ) : \Aimeos\MShop\Coupon\Provider\Iface
	{
		$context = $this->context();
		$names = explode( ',', $item->getProvider() );

		if( ( $providername = array_shift( $names ) ) === null ) {
			throw new \LogicException( sprintf( 'Provider in "%1$s" not available', $item->getProvider() ), 400 );
		}

		if( ctype_alnum( $providername ) === false ) {
			throw new \LogicException( sprintf( 'Invalid characters in provider name "%1$s"', $providername ), 400 );
		}

		$classname = '\Aimeos\MShop\Coupon\Provider\\' . $providername;
		$interface = \Aimeos\MShop\Coupon\Provider\Factory\Iface::class;

		$provider = \Aimeos\Utils::create( $classname, [$context, $item, $code], $interface );

		/** mshop/coupon/provider/decorators
		 * Adds a list of decorators to all coupon provider objects automatcally
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap decorators
		 * ("\Aimeos\MShop\Coupon\Provider\Decorator\*") around the coupon provider.
		 *
		 *  mshop/coupon/provider/decorators = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Coupon\Provider\Decorator\Decorator1" to all coupon provider
		 * objects.
		 *
		 * @param array List of decorator names
		 * @since 2014.05
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/account/favorite/decorators/excludes
		 * @see client/html/account/favorite/decorators/local
		 */
		$decorators = $context->config()->get( 'mshop/coupon/provider/decorators', [] );

		$object = $this->addCouponDecorators( $item, $code, $provider, $names );
		$object = $this->addCouponDecorators( $item, $code, $object, $decorators );

		return $object->setObject( $object );
	}


	/**
	 * Wraps the named coupon decorators around the coupon provider.
	 *
	 * @param \Aimeos\MShop\Coupon\Item\Iface $item Coupon item object
	 * @param string $code Coupon code
	 * @param \Aimeos\MShop\Coupon\Provider\Iface $provider Coupon provider object
	 * @param string[] $names List of decorator names
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Coupon provider wrapped by one or more coupon decorators
	 * @throws \LogicException If a coupon decorator couldn't be instantiated
	 */
	protected function addCouponDecorators( \Aimeos\MShop\Coupon\Item\Iface $item, string $code,
		\Aimeos\MShop\Coupon\Provider\Iface $provider, array $names ) : \Aimeos\MShop\Coupon\Provider\Iface
	{
		$context = $this->context();
		$classprefix = '\Aimeos\MShop\Coupon\Provider\Decorator\\';
		$interface = \Aimeos\MShop\Coupon\Provider\Decorator\Iface::class;

		foreach( $names as $name )
		{
			if( ctype_alnum( $name ) === false ) {
				throw new \LogicException( sprintf( 'Invalid characters in class name "%1$s"', $name ), 400 );
			}

			$provider = \Aimeos\Utils::create( $classprefix . $name, [$provider, $context, $item, $code], $interface );
		}

		return $provider;
	}
}
