<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Decorator;


/**
 * Base decorator methods for plugin provider.
 *
 * @package MShop
 * @subpackage Plugin
 */
abstract class Base
	extends \Aimeos\MShop\Plugin\Provider\Base
{
	private $object;


	/**
	 * Initializes the plugin instance
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 * @param \Aimeos\MShop\Plugin\Item\Iface $item Plugin item object
	 * @param \Aimeos\MShop\Plugin\Provider\Iface $provider Plugin provider object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MShop\Plugin\Item\Iface $item,
		\Aimeos\MShop\Plugin\Provider\Iface $provider )
	{
		parent::__construct( $context, $item );

		$this->object = $provider;
	}


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$this->object->register( $p );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @param boolean True if successful, false if not
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, $action, $value = null )
	{
		return $this->object->update( $order, $action, $value );
	}


	/**
	 * Returns the next provider or decorator.
	 *
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Provider or decorator object
	 */
	protected function getProvider()
	{
		return $this->object;
	}
}
