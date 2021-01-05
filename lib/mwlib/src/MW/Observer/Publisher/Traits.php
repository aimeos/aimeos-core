<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 * @package MW
 * @subpackage Observer
 */


namespace Aimeos\MW\Observer\Publisher;


/**
 * Default implementation of a publisher in the observer pattern
 *
 * @package MW
 * @subpackage Observer
 */
trait Traits
{
	protected $listeners = [];


	/**
	 * Adds a listener object to the publisher.
	 *
	 * @param \Aimeos\MW\Observer\Listener\Iface $l Object implementing listener interface
	 * @param string $action Name of the action to listen for
	 * @return \Aimeos\MW\Observer\Publisher\Iface Publisher object for method chaining
	 */
	public function attach( \Aimeos\MW\Observer\Listener\Iface $l, string $action ) : Iface
	{
		$this->listeners[$action][] = $l;
		return $this;
	}


	/**
	 * Removes a listener from the publisher object.
	 *
	 * @param \Aimeos\MW\Observer\Listener\Iface $l Object implementing listener interface
	 * @param string $action Name of the action to remove the listener from
	 * @return \Aimeos\MW\Observer\Publisher\Iface Publisher object for method chaining
	 */
	public function detach( \Aimeos\MW\Observer\Listener\Iface $l, string $action ) : Iface
	{
		if( isset( $this->listeners[$action] ) )
		{
			foreach( $this->listeners[$action] as $key => $listener )
			{
				if( $listener === $l ) {
					unset( $this->listeners[$action][$key] );
				}
			}
		}

		return $this;
	}


	/**
	 * Removes all attached listeners from the publisher
	 *
	 * @return \Aimeos\MW\Observer\Publisher\Iface Publisher object for method chaining
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
