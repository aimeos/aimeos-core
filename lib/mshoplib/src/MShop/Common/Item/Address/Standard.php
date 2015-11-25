<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Address;


/**
 * Interface for provider common address DTO objects used by the shop.
 * @package MShop
 * @subpackage Common
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Address\Base
	implements \Aimeos\MShop\Common\Item\Address\Iface, \Aimeos\MShop\Common\Item\Position\Iface
{
	private $prefix;
	private $values;

	/**
	 * Initializes the provider common address item object
	 *
	 * @param string $prefix Property prefix when converting to array
	 * @param array $values List of attributes that belong to the provider common address item
	 */
	public function __construct( $prefix, array $values = array( ) )
	{
		parent::__construct( $prefix, $values );

		$this->values = $values;
		$this->prefix = $prefix;
	}


	/**
	 * Returns the customer ID this address belongs to
	 *
	 * @return string Customer ID of the address
	 */
	public function getParentId()
	{
		if( isset( $this->values[$this->prefix . 'parentid'] ) ) {
			return (string) $this->values[$this->prefix . 'parentid'];
		}

		return '';
	}


	/**
	 * Sets the new customer ID this address belongs to
	 *
	 * @param string $parentid New customer ID of the address
	 */
	public function setParentId( $parentid )
	{
		if( $parentid == $this->getParentId() ) { return; }

		$this->values[$this->prefix . 'parentid'] = (string) $parentid;
		$this->setModified();
	}


	/**
	 * Returns the position of the address item.
	 *
	 * @return integer Position of the address item
	 */
	public function getPosition()
	{
		if( isset( $this->values[$this->prefix . 'position'] ) ) {
			return (int) $this->values[$this->prefix . 'position'];
		}

		return 0;
	}


	/**
	 * Sets the Position of the address item.
	 *
	 * @param integer $position Position of the address item
	 */
	public function setPosition( $position )
	{
		if( $position == $this->getPosition() ) { return; }

		$this->values[$this->prefix . 'position'] = (int) $position;
		$this->setModified();
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
				case $this->prefix . 'parentid': $this->setParentId( $value ); break;
				case $this->prefix . 'position': $this->setPosition( $value ); break;
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
		$properties = parent::toArray();

		$properties[$this->prefix . 'parentid'] = $this->getParentId();
		$properties[$this->prefix . 'position'] = $this->getPosition();

		return $properties;
	}

}
