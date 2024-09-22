<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Manager;


/**
 * Abstract class for service managers.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
{
	/**
	 * Returns the service provider which is responsible for the service item.
	 *
	 * @param \Aimeos\MShop\Service\Item\Iface $item Delivery or payment service item object
	 * @param string $type Service type code
	 * @return \Aimeos\MShop\Service\Provider\Iface Service provider object
	 * @throws \LogicException If provider couldn't be found
	 */
	public function getProvider( \Aimeos\MShop\Service\Item\Iface $item, string $type ) : \Aimeos\MShop\Service\Provider\Iface
	{
		$type = ucwords( $type );
		$context = $this->context();
		$names = explode( ',', $item->getProvider() );

		if( ctype_alnum( $type ) === false ) {
			throw new \LogicException( sprintf( 'Invalid characters in type name "%1$s"', $type ), 400 );
		}

		if( ( $provider = array_shift( $names ) ) === null ) {
			throw new \LogicException( sprintf( 'Provider in "%1$s" not available', $item->getProvider() ), 400 );
		}

		if( ctype_alnum( $provider ) === false ) {
			throw new \LogicException( sprintf( 'Invalid characters in provider name "%1$s"', $provider ), 400 );
		}

		$classname = '\Aimeos\MShop\Service\Provider\\' . $type . '\\' . $provider;
		$interface = \Aimeos\MShop\Service\Provider\Factory\Iface::class;

		$provider = \Aimeos\Utils::create( $classname, [$context, $item], $interface );

		/** mshop/service/provider/delivery/decorators
		 * Adds a list of decorators to all delivery provider objects automatcally
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap decorators
		 * ("\Aimeos\MShop\Service\Provider\Decorator\*") around the delivery provider.
		 *
		 *  mshop/service/provider/delivery/decorators = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Service\Provider\Decorator\Decorator1" to all delivery provider
		 * objects.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @see mshop/service/provider/payment/decorators
		 */

		/** mshop/service/provider/payment/decorators
		 * Adds a list of decorators to all payment provider objects automatcally
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap decorators
		 * ("\Aimeos\MShop\Service\Provider\Decorator\*") around the payment provider.
		 *
		 *  mshop/service/provider/payment/decorators = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Service\Provider\Decorator\Decorator1" to all payment provider
		 * objects.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @see mshop/service/provider/delivery/decorators
		 */
		$decorators = $context->config()->get( 'mshop/service/provider/' . $item->getType() . '/decorators', [] );

		$provider = $this->addServiceDecorators( $item, $provider, $names );
		return $this->addServiceDecorators( $item, $provider, $decorators );
	}


	/**
	 * Wraps the named service decorators around the service provider.
	 *
	 * @param \Aimeos\MShop\Service\Item\Iface $serviceItem Service item object
	 * @param \Aimeos\MShop\Service\Provider\Iface $provider Service provider object
	 * @param array $names List of decorator names that should be wrapped around the provider object
	 * @return \Aimeos\MShop\Service\Provider\Iface
	 */
	protected function addServiceDecorators( \Aimeos\MShop\Service\Item\Iface $serviceItem,
		\Aimeos\MShop\Service\Provider\Iface $provider, array $names ) : \Aimeos\MShop\Service\Provider\Iface
	{
		$context = $this->context();
		$classprefix = '\Aimeos\MShop\Service\Provider\Decorator\\';

		foreach( $names as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$msg = $context->translate( 'mshop', 'Invalid characters in class name "%1$s"' );
				throw new \Aimeos\MShop\Service\Exception( sprintf( $msg, $name ), 400 );
			}

			$classname = $classprefix . $name;
			$interface = \Aimeos\MShop\Service\Provider\Decorator\Iface::class;

			$provider = \Aimeos\Utils::create( $classname, [$provider, $context, $serviceItem], $interface );
		}

		return $provider;
	}
}
