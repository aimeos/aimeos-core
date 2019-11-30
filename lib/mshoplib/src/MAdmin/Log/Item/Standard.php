<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MAdmin
 * @subpackage Log
 */


namespace Aimeos\MAdmin\Log\Item;


/**
 * Default log item implementation.
 *
 * @package MAdmin
 * @subpackage Log
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MAdmin\Log\Item\Iface
{
	/**
	 * Initializes the log item.
	 *
	 * @param array $values Associative list of key/value pairs
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'log.', $values );
	}


	/**
	 * Returns the facility of the item.
	 *
	 * @return string Returns the facility
	 */
	public function getFacility()
	{
		return (string) $this->get( 'log.facility', '' );
	}


	/**
	 * Sets the new facility of the of the item.
	 *
	 * @param string $facility Facility
	 * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
	 */
	public function setFacility( $facility )
	{
		return $this->set( 'log.facility', (string) $facility );
	}


	/**
	 * Returns the timestamp of the item.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimestamp()
	{
		return $this->get( 'log.timestamp' );
	}


	/**
	 * Returns the priority of the item.
	 *
	 * @return integer Returns the priority
	 */
	public function getPriority()
	{
		return (int) $this->get( 'log.priority', 0 );
	}


	/**
	 * Sets the new priority of the item.
	 *
	 * @param integer $priority Priority
	 * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
	 */
	public function setPriority( $priority )
	{
		return $this->set( 'log.priority', (int) $priority );
	}


	/**
	 * Returns the message of the item.
	 *
	 * @return string Returns the message
	 */
	public function getMessage()
	{
		return (string) $this->get( 'log.message', '' );
	}


	/**
	 * Sets the new message of the item.
	 *
	 * @param string $message Message
	 * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
	 */
	public function setMessage( $message )
	{
		return $this->set( 'log.message', (string) $message );
	}


	/**
	 * Returns the request of the item.
	 *
	 * @return string Returns the request
	 */
	public function getRequest()
	{
		return (string) $this->get( 'log.request', '' );
	}


	/**
	 * Sets the new request of the item.
	 *
	 * @param string $request Request
	 * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
	 */
	public function setRequest( $request )
	{
		return $this->set( 'log.request', (string) $request );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'log';
	}


	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param boolean True to set private properties too, false for public only
	 * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
	 */
	public function fromArray( array &$list, $private = false )
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'log.facility': $item = $item->setFacility( $value ); break;
				case 'log.priority': $item = $item->setPriority( $value ); break;
				case 'log.message': $item = $item->setMessage( $value ); break;
				case 'log.request': $item = $item->setRequest( $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param boolean True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( $private = false )
	{
		$list = parent::toArray( $private );

		$list['log.facility'] = $this->getFacility();
		$list['log.timestamp'] = $this->getTimestamp();
		$list['log.priority'] = $this->getPriority();
		$list['log.message'] = $this->getMessage();
		$list['log.request'] = $this->getRequest();

		return $list;
	}
}
