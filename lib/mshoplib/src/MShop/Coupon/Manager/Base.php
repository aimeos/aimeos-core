<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	 * Wraps the named coupon decorators around the coupon provider.
	 *
	 * @param \Aimeos\MShop\Coupon\Item\Iface $item Coupon item object
	 * @param string $code Coupon code
	 * @param \Aimeos\MShop\Coupon\Provider\Iface $provider Coupon provider object
	 * @param array $names List of decorator names
	 * @return \Aimeos\MShop\Coupon\Provider\Iface Coupon provider wrapped by one or more coupon decorators
	 * @throws \Aimeos\MShop\Coupon\Exception If a coupon decorator couldn't be instantiated
	 */
	protected function addCouponDecorators( \Aimeos\MShop\Coupon\Item\Iface $item, $code,
		\Aimeos\MShop\Coupon\Provider\Iface $provider, array $names )
	{
		$iface = '\\Aimeos\\MShop\\Coupon\\Provider\\Decorator\\Iface';
		$classprefix = '\\Aimeos\\MShop\\Coupon\\Provider\\Decorator\\';

		foreach( $names as $name )
		{
			if( ctype_alnum( $name ) === false ) {
				throw new \Aimeos\MShop\Coupon\Exception( sprintf( 'Invalid characters in class name "%1$s"', $name ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\MShop\Coupon\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$provider = new $classname( $provider, $this->getContext(), $item, $code );

			if( ( $provider instanceof $iface ) === false ) {
				throw new \Aimeos\MShop\Coupon\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
			}
		}

		return $provider;
	}
}