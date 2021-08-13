<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	private $plugins = [];

	/**
	 * Returns the plugin provider which is responsible for the plugin item.
	 *
	 * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
	 * @param string $type Plugin type code
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Returns the decoratad plugin provider object
	 * @throws \Aimeos\MShop\Plugin\Exception If provider couldn't be found
	 */
	public function getProvider( \Aimeos\MShop\Plugin\Item\Iface $item, string $type ) : \Aimeos\MShop\Plugin\Provider\Iface
	{
		$type = ucwords( $type );
		$names = explode( ',', $item->getProvider() );

		if( ctype_alnum( $type ) === false )
		{
			$msg = $this->getContext()->translate( 'mshop', 'Invalid characters in type name "%1$s"' );
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( $msg, $type ) );
		}

		if( ( $provider = array_shift( $names ) ) === null )
		{
			$msg = $this->getContext()->translate( 'mshop', 'Provider in "%1$s" not available' );
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( $msg, $item->getProvider() ) );
		}

		if( ctype_alnum( $provider ) === false )
		{
			$msg = $this->getContext()->translate( 'mshop', 'Invalid characters in provider name "%1$s"' );
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( $msg, $provider ) );
		}

		$classname = '\Aimeos\MShop\Plugin\Provider\\' . $type . '\\' . $provider;

		if( class_exists( $classname ) === false )
		{
			$msg = $this->getContext()->translate( 'mshop', 'Class "%1$s" not available' );
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( $msg, $classname ) );
		}

		$context = $this->getContext();
		$config = $context->getConfig();
		$provider = new $classname( $context, $item );

		self::checkClass( \Aimeos\MShop\Plugin\Provider\Factory\Iface::class, $provider );

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
	 * Registers plugins to the given publisher.
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $publisher Publisher object
	 * @param string $type Unique plugin type code
	 * @return \Aimeos\MShop\Plugin\Manager\Iface Manager object for chaining method calls
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $publisher, string $type ) : \Aimeos\MShop\Plugin\Manager\Iface
	{
		if( !isset( $this->plugins[$type] ) )
		{
			$search = $this->getObject()->filter( true );

			$expr = array(
				$search->compare( '==', 'plugin.type', $type ),
				$search->getConditions(),
			);

			$search->setConditions( $search->and( $expr ) );
			$search->setSortations( array( $search->sort( '+', 'plugin.position' ) ) );

			$this->plugins[$type] = [];

			foreach( $this->getObject()->search( $search ) as $item ) {
				$this->plugins[$type][$item->getId()] = $this->getProvider( $item, $type );
			}
		}

		foreach( $this->plugins[$type] as $plugin ) {
			$plugin->register( $publisher );
		}

		return $this;
	}


	/**
	 *
	 * @param \Aimeos\MShop\Plugin\Item\Iface $pluginItem Plugin item object
	 * @param \Aimeos\MShop\Plugin\Provider\Iface $provider Plugin provider object
	 * @param array $names List of decorator names that should be wrapped around the plugin provider object
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin provider object
	 */
	protected function addPluginDecorators( \Aimeos\MShop\Plugin\Item\Iface $pluginItem,
		\Aimeos\MShop\Plugin\Provider\Iface $provider, array $names ) : \Aimeos\MShop\Plugin\Provider\Iface
	{
		$classprefix = '\Aimeos\MShop\Plugin\Provider\Decorator\\';

		foreach( $names as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$msg = $this->getContext()->translate( 'mshop', 'Invalid characters in class name "%1$s"' );
				throw new \Aimeos\MShop\Plugin\Exception( sprintf( $msg, $name ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false )
			{
				$msg = $this->getContext()->translate( 'mshop', 'Class "%1$s" not available' );
				throw new \Aimeos\MShop\Plugin\Exception( sprintf( $msg, $classname ) );
			}

			$provider = new $classname( $this->getContext(), $pluginItem, $provider );

			self::checkClass( \Aimeos\MShop\Plugin\Provider\Decorator\Iface::class, $provider );
		}

		return $provider;
	}
}
