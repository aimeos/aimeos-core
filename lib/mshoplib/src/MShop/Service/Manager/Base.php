<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	extends \Aimeos\MShop\Common\Manager\ListRef\Base
{
	/**
	 * Wraps the named service decorators around the service provider.
	 *
	 * @param \Aimeos\MShop\Service\Item\Iface $serviceItem Service item object
	 * @param \Aimeos\MShop\Service\Provider\Iface $provider Service provider object
	 * @param array $names List of decorator names that should be wrapped around the provider object
	 * @return \Aimeos\MShop\Service\Provider\Iface
	 */
	protected function addServiceDecorators( \Aimeos\MShop\Service\Item\Iface $serviceItem,
		\Aimeos\MShop\Service\Provider\Iface $provider, array $names )
	{
		$iface = '\\Aimeos\\MShop\\Service\\Provider\\Decorator\\Iface';
		$classprefix = '\\Aimeos\\MShop\\Service\\Provider\\Decorator\\';

		foreach( $names as $name )
		{
			if( ctype_alnum( $name ) === false ) {
				throw new \Aimeos\MShop\Service\Exception( sprintf( 'Invalid characters in class name "%1$s"', $name ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\MShop\Service\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$provider = new $classname( $provider, $this->getContext(), $serviceItem );

			if( ( $provider instanceof $iface ) === false ) {
				$msg = sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface );
				throw new \Aimeos\MShop\Service\Exception( $msg );
			}
		}

		return $provider;
	}
}