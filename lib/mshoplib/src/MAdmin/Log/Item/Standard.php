<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	public function getFacility() : string
	{
		return (string) $this->get( 'log.facility', '' );
	}


	/**
	 * Sets the new facility of the of the item.
	 *
	 * @param string $facility Facility
	 * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
	 */
	public function setFacility( string $facility ) : \Aimeos\MAdmin\Log\Item\Iface
	{
		return $this->set( 'log.facility', (string) $facility );
	}


	/**
	 * Returns the timestamp of the item.
	 *
	 * @return string|null ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimestamp() : ?string
	{
		return $this->get( 'log.timestamp' );
	}


	/**
	 * Returns the priority of the item.
	 *
	 * @return int Returns the priority
	 */
	public function getPriority() : int
	{
		return $this->get( 'log.priority', 0 );
	}


	/**
	 * Sets the new priority of the item.
	 *
	 * @param int $priority Priority
	 * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
	 */
	public function setPriority( int $priority ) : \Aimeos\MAdmin\Log\Item\Iface
	{
		return $this->set( 'log.priority', $priority );
	}


	/**
	 * Returns the message of the item.
	 *
	 * @return string Returns the message
	 */
	public function getMessage() : string
	{
		return $this->get( 'log.message', '' );
	}


	/**
	 * Sets the new message of the item.
	 *
	 * @param string $message Message
	 * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
	 */
	public function setMessage( string $message ) : \Aimeos\MAdmin\Log\Item\Iface
	{
		return $this->set( 'log.message', $message );
	}


	/**
	 * Returns the request of the item.
	 *
	 * @return string Returns the request
	 */
	public function getRequest() : string
	{
		return (string) $this->get( 'log.request', '' );
	}


	/**
	 * Sets the new request of the item.
	 *
	 * @param string $request Request
	 * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
	 */
	public function setRequest( string $request ) : \Aimeos\MAdmin\Log\Item\Iface
	{
		return $this->set( 'log.request', $request );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'log';
	}


	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MAdmin\Log\Item\Iface Log item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
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
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
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
