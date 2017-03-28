<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
abstract class Base implements \Aimeos\MW\Observer\Publisher\Iface
{
	private $listeners = [];


	/**
	 * Adds a listener object to the publisher.
	 *
	 * @param \Aimeos\MW\Observer\Listener\Iface $l Object implementing listener interface
	 * @param string $action Name of the action to listen for
	 */
	public function addListener( \Aimeos\MW\Observer\Listener\Iface $l, $action )
	{
		$this->listeners[$action][] = $l;
	}


	/**
	 * Removes a listener from the publisher object.
	 *
	 * @param \Aimeos\MW\Observer\Listener\Iface $l Object implementing listener interface
	 * @param string $action Name of the action to remove the listener from
	 */
	public function removeListener( \Aimeos\MW\Observer\Listener\Iface $l, $action )
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
	}


	/**
	 * Sends updates to all listeners of the given action.
	 *
	 * @param string $action Name of the action
	 * @param mixed|null $value Value or object given to the listeners
	 * @return boolean Status of the operations
	 */
	protected function notifyListeners( $action, $value = null )
	{
		if( isset( $this->listeners[$action] ) )
		{
			foreach( $this->listeners[$action] as $key => $listener )
			{
				if( $listener->update( $this, $action, $value ) === false ) {
					return false;
				}
			}
		}

		return true;
	}


	/**
	 * Removes all attached listeners from the publisher
	 */
	protected function clearListeners()
	{
		$this->listeners = [];
	}
}
