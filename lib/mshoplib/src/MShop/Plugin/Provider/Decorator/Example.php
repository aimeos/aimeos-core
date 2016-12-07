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
 * Simple example implementation of a plugin decorator.
 *
 * @package MShop
 * @subpackage Plugin
 */
class Example
	extends \Aimeos\MShop\Plugin\Provider\Decorator\Base
	implements \Aimeos\MShop\Plugin\Provider\Decorator\Iface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$this->getProvider()->register( $p );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, $action, $value = null )
	{
		return $this->getProvider()->update( $order, $action, $value );
	}
}
