<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Item\Base\Address;


/**
 * Default order address container object
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Order\Item\Base\Address\Base
	implements \Aimeos\MShop\Order\Item\Base\Address\Iface
{
	private $values;

	/**
	 * Initializes the objects with the given array of values.
	 *
	 * @param array $values List of address elements
	 */
	public function __construct( array $values = array( ) )
	{
		parent::__construct( 'order.base.address.', $values );

		$this->values = $values;
	}


	/**
	 * Returns the order base ID the address belongs to.
	 *
	 * @return integer|null Base ID
	 */
	public function getBaseId()
	{
		return ( isset( $this->values['baseid'] ) ? (int) $this->values['baseid'] : null );
	}


	/**
	 * Sets the order base ID the address belongs to.
	 *
	 * @param integer|null $value New base ID
	 */
	public function setBaseId( $value )
	{
		if( $value == $this->getBaseId() ) { return; }

		$this->values['baseid'] = ( $value !== null ? (int) $value : null );
		$this->setModified();
	}


	/**
	 * Returns the original customer address ID.
	 *
	 * @return string Customer address ID
	 */
	public function getAddressId()
	{
		return ( isset( $this->values['addrid'] ) ? (string) $this->values['addrid'] : '' );
	}


	/**
	 * Sets the original customer address ID.
	 *
	 * @param string $addrid New customer address ID
	 */
	public function setAddressId( $addrid )
	{
		if( $addrid == $this->getAddressId() ) { return; }

		$this->values['addrid'] = (string) $addrid;
		$this->setModified();
	}


	/**
	 * Returns the address type which can be billing or delivery.
	 *
	 * @return string Address type
	 */
	public function getType()
	{
		return ( isset( $this->values['type'] ) ? (string) $this->values['type'] : \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
	}


	/**
	 * Sets the new type of the address which can be billing or delivery.
	 *
	 * @param string $type New type of the address
	 */
	public function setType( $type )
	{
		if( $type == $this->getType() ) { return; }

		$this->checkType( $type );

		$this->values['type'] = (string) $type;
		$this->setModified();
	}


	/**
	 * Copys all data from a given address.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address New address
	 */
	public function copyFrom( \Aimeos\MShop\Common\Item\Address\Iface $address )
	{
		$this->setAddressId( $address->getId() );
		$this->setCompany( $address->getCompany() );
		$this->setVatID( $address->getVatID() );
		$this->setSalutation( $address->getSalutation() );
		$this->setTitle( $address->getTitle() );
		$this->setFirstname( $address->getFirstname() );
		$this->setLastname( $address->getLastname() );
		$this->setAddress1( $address->getAddress1() );
		$this->setAddress2( $address->getAddress2() );
		$this->setAddress3( $address->getAddress3() );
		$this->setPostal( $address->getPostal() );
		$this->setCity( $address->getCity() );
		$this->setState( $address->getState() );
		$this->setCountryId( $address->getCountryId() );
		$this->setTelephone( $address->getTelephone() );
		$this->setEmail( $address->getEmail() );
		$this->setTelefax( $address->getTelefax() );
		$this->setWebsite( $address->getWebsite() );
		$this->setLanguageId( $address->getLanguageId() );
		$this->setFlag( $address->getFlag() );

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
				case 'order.base.address.baseid': $this->setBaseId( $value ); break;
				case 'order.base.address.addressid': $this->setAddressId( $value ); break;
				case 'order.base.address.type': $this->setType( $value ); break;
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

		$list['order.base.address.baseid'] = $this->getBaseId();
		$list['order.base.address.addressid'] = $this->getAddressId();
		$list['order.base.address.type'] = $this->getType();

		return $list;
	}

}
