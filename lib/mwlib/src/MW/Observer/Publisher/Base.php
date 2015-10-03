<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Observer
 */


/**
 * Default implementation of a publisher in the observer pattern
 *
 * @package MW
 * @subpackage Observer
 */

abstract class MW_Observer_Publisher_Base implements MW_Observer_Publisher_Iface
{
	private $listeners = array();


	/**
	 * Adds a listener object to the publisher.
	 *
	 * @param MW_Observer_Listener_Iface $l Object implementing listener interface
	 * @param string $action Name of the action to listen for
	 */
	public function addListener( MW_Observer_Listener_Iface $l, $action )
	{
		$this->listeners[$action][] = $l;
	}


	/**
	 * Removes a listener from the publisher object.
	 *
	 * @param MW_Observer_Listener_Iface $l Object implementing listener interface
	 * @param string $action Name of the action to remove the listener from
	 */
	public function removeListener( MW_Observer_Listener_Iface $l, $action )
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
		$this->listeners = array();
	}
}
