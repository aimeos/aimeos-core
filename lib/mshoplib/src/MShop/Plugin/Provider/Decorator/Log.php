<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Decorator;


/**
 * Logging and tracing for plugins.
 *
 * @package MShop
 * @subpackage Plugin
 */
class Log
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
		$this->getContext()->getLogger()->log( 'Plugin: ' . __METHOD__, \Aimeos\MW\Logger\Base::DEBUG );

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
		$msg = 'Plugin: ' . __METHOD__ . ', action: ' . $action . ( is_scalar( $value ) ? ', value: ' . $value : '' );
		$this->getContext()->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::DEBUG );

		return $this->getProvider()->update( $order, $action, $value );
	}
}
