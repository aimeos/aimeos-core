<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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
	private $values;

	/**
	 * Initializes the log item.
	 *
	 * @param array $values Associative list of key/value pairs
	 */
	public function __construct( array $values = array() )
	{
		parent::__construct( 'log.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the facility of the item.
	 *
	 * @return string Returns the facility
	 */
	public function getFacility()
	{
		return ( isset( $this->values['facility'] ) ? (string) $this->values['facility'] : '' );
	}


	/**
	 * Sets the new facility of the of the item.
	 *
	 * @param string $facility Facility
	 */
	public function setFacility( $facility )
	{
		$this->values['facility'] = (string) $facility;
		$this->setModified();
	}


	/**
	 * Returns the timestamp of the item.
	 *
	 * @return string ISO date in YYYY-MM-DD hh:mm:ss format
	 */
	public function getTimestamp()
	{
		return ( isset( $this->values['timestamp'] ) ? (string) $this->values['timestamp'] : null );
	}


	/**
	 * Returns the priority of the item.
	 *
	 * @return integer Returns the priority
	 */
	public function getPriority()
	{
		return ( isset( $this->values['priority'] ) ? (int) $this->values['priority'] : 0 );
	}


	/**
	 * Sets the new priority of the item.
	 *
	 * @param integer $priority Priority
	 */
	public function setPriority( $priority )
	{
		$this->values['priority'] = (int) $priority;
		$this->setModified();
	}


	/**
	 * Returns the message of the item.
	 *
	 * @return string Returns the message
	 */
	public function getMessage()
	{
		return ( isset( $this->values['message'] ) ? (string) $this->values['message'] : '' );
	}


	/**
	 * Sets the new message of the item.
	 *
	 * @param string $message Message
	 */
	public function setMessage( $message )
	{
		$this->values['message'] = (string) $message;
		$this->setModified();
	}


	/**
	 * Returns the request of the item.
	 *
	 * @return string Returns the request
	 */
	public function getRequest()
	{
		return ( isset( $this->values['request'] ) ? (string) $this->values['request'] : '' );
	}


	/**
	 * Sets the new request of the item.
	 *
	 * @param string $request Request
	 */
	public function setRequest( $request )
	{
		$this->values['request'] = (string) $request;
		$this->setModified();
	}


	/**
	 * Returns the item type
	 *
	 * @return Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'log';
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = array();
		$list = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'log.facility': $this->setFacility( $value ); break;
				case 'log.priority': $this->setPriority( $value ); break;
				case 'log.message': $this->setMessage( $value ); break;
				case 'log.request': $this->setRequest( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['log.facility'] = $this->getFacility();
		$list['log.timestamp'] = $this->getTimestamp();
		$list['log.priority'] = $this->getPriority();
		$list['log.message'] = $this->getMessage();
		$list['log.request'] = $this->getRequest();

		return $list;
	}
}
