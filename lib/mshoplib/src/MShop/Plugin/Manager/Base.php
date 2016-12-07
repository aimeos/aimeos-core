<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Manager;


/**
 * Abstract class for plugin managers.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
{
	/**
	 *
	 * @param \Aimeos\MShop\Plugin\Item\Iface $pluginItem Plugin item object
	 * @param \Aimeos\MShop\Plugin\Provider\Iface $provider Plugin provider object
	 * @param array $names List of decorator names that should be wrapped around the plugin provider object
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin provider object
	 */
	protected function addPluginDecorators( \Aimeos\MShop\Plugin\Item\Iface $pluginItem,
		\Aimeos\MShop\Plugin\Provider\Iface $provider, array $names )
	{
		$iface = '\\Aimeos\\MShop\\Plugin\\Provider\\Decorator\\Iface';
		$classprefix = '\\Aimeos\\MShop\\Plugin\\Provider\\Decorator\\';

		foreach( $names as $name )
		{
			if( ctype_alnum( $name ) === false ) {
				throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Invalid characters in class name "%1$s"', $name ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$provider = new $classname( $this->getContext(), $pluginItem, $provider );

			if( ( $provider instanceof $iface ) === false ) {
				$msg = sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface );
				throw new \Aimeos\MShop\Plugin\Exception( $msg );
			}
		}

		return $provider;
	}
}