<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2024
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item;


/**
 * Default implementation of a publisher in the observer pattern
 *
 * @package MShop
 * @subpackage Observer
 */
trait Publisher
{
	protected array $listeners = [];


	/**
	 * Adds a listener object to the publisher.
	 *
	 * @param \Aimeos\MShop\Plugin\Provider\Iface $l Object implementing listener interface
	 * @param string $action Name of the action to listen for
	 * @return \Aimeos\MShop\Order\Item\Iface Publisher object for method chaining
	 */
	public function attach( \Aimeos\MShop\Plugin\Provider\Iface $l, string $action ) : Iface
	{
		$this->listeners[$action][] = $l;
		return $this;
	}


	/**
	 * Removes all attached listeners from the publisher
	 *
	 * @return \Aimeos\MShop\Order\Item\Iface Publisher object for method chaining
	 */
	public function off() : Iface
	{
		$this->listeners = [];
		return $this;
	}


	/**
	 * Sends updates to all listeners of the given action.
	 *
	 * @param string $action Name of the action
	 * @param mixed $value Value or object given to the listeners
	 * @return mixed Modified value parameter
	 */
	protected function notify( string $action, $value = null )
	{
		if( isset( $this->listeners[$action] ) )
		{
			foreach( $this->listeners[$action] as $key => $listener ) {
				$value = $listener->update( $this, $action, $value );
			}
		}

		return $value;
	}
}
