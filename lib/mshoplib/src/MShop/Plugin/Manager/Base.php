<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2017
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
	 * Returns the plugin provider which is responsible for the plugin item.
	 *
	 * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Returns the decoratad plugin provider object
	 * @throws \Aimeos\MShop\Plugin\Exception If provider couldn't be found
	 */
	public function getProvider( \Aimeos\MShop\Plugin\Item\Iface $item )
	{
		$type = ucwords( $item->getType() );
		$names = explode( ',', $item->getProvider() );

		if( ctype_alnum( $type ) === false ) {
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Invalid characters in type name "%1$s"', $type ) );
		}

		if( ( $provider = array_shift( $names ) ) === null ) {
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Provider in "%1$s" not available', $item->getProvider() ) );
		}

		if( ctype_alnum( $provider ) === false ) {
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Invalid characters in provider name "%1$s"', $provider ) );
		}

		$interface = '\\Aimeos\\MShop\\Plugin\\Provider\\Factory\\Iface';
		$classname = '\\Aimeos\\MShop\\Plugin\\Provider\\' . $type . '\\' . $provider;

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$context = $this->getContext();
		$config = $context->getConfig();
		$provider = new $classname( $context, $item );

		if( ( $provider instanceof $interface ) === false )
		{
			$msg = sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface );
			throw new \Aimeos\MShop\Plugin\Exception( $msg );
		}

		/** mshop/plugin/provider/order/decorators
		 * Adds a list of decorators to all order plugin provider objects automatcally
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap decorators
		 * ("\Aimeos\MShop\Plugin\Provider\Decorator\*") around the order provider.
		 *
		 *  mshop/plugin/provider/order/decorators = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Plugin\Provider\Decorator\Decorator1" to all order provider
		 * objects.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/plugin/provider/order/decorators
		 */
		$decorators = $config->get( 'mshop/plugin/provider/' . $item->getType() . '/decorators', [] );

		$provider = $this->addPluginDecorators( $item, $provider, $names );
		$provider = $this->addPluginDecorators( $item, $provider, $decorators );

		return $provider->setObject( $provider );
	}


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