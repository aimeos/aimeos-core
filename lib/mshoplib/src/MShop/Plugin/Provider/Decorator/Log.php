<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p ) : \Aimeos\MW\Observer\Listener\Iface
	{
		$level = \Aimeos\MW\Logger\Base::DEBUG;
		$class = get_class( $this->getProvider() );

		$this->getContext()->getLogger()->log( 'Plugin::register: ' . $class, $level, 'core/plugin' );

		$this->getProvider()->register( $p );

		return $this;
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, string $action, $value = null )
	{
		$level = \Aimeos\MW\Logger\Base::DEBUG;
		$class = get_class( $this->getProvider() );
		$payload = ( is_object( $value ) ? get_class( $value ) : ( is_scalar( $value ) ? $value : '' ) );

		$msg = 'Plugin::update:before: ' . $class . ', action: ' . $action . ', value: ' . $payload;
		$this->getContext()->getLogger()->log( $msg, $level, 'core/plugin' );

		$value = $this->getProvider()->update( $order, $action, $value );

		$msg = 'Plugin::update:after: ' . $class . ', action: ' . $action . ', value: ' . $payload;
		$this->getContext()->getLogger()->log( $msg, $level, 'core/plugin' );

		return $value;
	}
}
