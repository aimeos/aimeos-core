<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Item;


/**
 * Interface for customer DTO objects used by the shop.
 *
 * @package MShop
 * @subpackage Customer
 */
class Standard extends Base implements Iface
{
	private $groups;
	private $helper;
	private $salt;


	/**
	 * Initializes the customer item object
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Payment address item object
	 * @param array $values List of attributes that belong to the customer item
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param \Aimeos\MShop\Common\Item\Address\Iface[] $addrItems List of delivery addresses
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $propItems List of property items
	 * @param \Aimeos\MShop\Common\Helper\Password\Iface|null $helper Password encryption helper object
	 * @param string|null $salt Password salt
	 */
	public function __construct( \Aimeos\MShop\Common\Item\Address\Iface $address, array $values = [],
		array $listItems = [], array $refItems = [], array $addrItems = [], array $propItems = [],
		\Aimeos\MShop\Common\Helper\Password\Iface $helper = null, string $salt = null )
	{
		parent::__construct( $address, $values, $listItems, $refItems, $addrItems, $propItems );

		$this->helper = $helper;
		$this->salt = $salt;
	}


	/**
	 * Sets the new ID of the item.
	 *
	 * @param string|null $id ID of the item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setId( ?string $id ) : \Aimeos\MShop\Common\Item\Iface
	{
		parent::setId( $id );

		// set new ID and modified flag
		$this->getPaymentAddress()->setId( null )->setId( $this->getId() );

		return $this;
	}


	/**
	 * Returns the label of the customer item.
	 *
	 * @return string Label of the customer item
	 */
	public function getLabel() : string
	{
		return $this->get( 'customer.label', '' );
	}


	/**
	 * Sets the new label of the customer item.
	 *
	 * @param string $value Label of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setLabel( ?string $value ) : \Aimeos\MShop\Customer\Item\Iface
	{
		return $this->set( 'customer.label', (string) $value );
	}


	/**
	 * Returns the status of the item.
	 *
	 * @return int Status of the item
	 */
	public function getStatus() : int
	{
		return $this->get( 'customer.status', 1 );
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param int $value Status of the item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setStatus( int $value ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'customer.status', $value );
	}


	/**
	 * Returns the code of the customer item.
	 *
	 * @return string Code of the customer item
	 */
	public function getCode() : string
	{
		return (string) $this->get( 'customer.code', $this->getPaymentAddress()->getEmail() );
	}


	/**
	 * Sets the new code of the customer item.
	 *
	 * @param string $value Code of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setCode( string $value ) : \Aimeos\MShop\Customer\Item\Iface
	{
		if( $value !== $this->get( 'customer.code' ) ) {
			$this->setDateVerified( null );
		}

		return $this->set( 'customer.code', $this->checkCode( $value, 255 ) );
	}


	/**
	 * Returns the password of the customer item.
	 *
	 * @return string
	 */
	public function getPassword() : string
	{
		return (string) $this->get( 'customer.password', '' );
	}


	/**
	 * Sets the password of the customer item.
	 *
	 * @param string $value password of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setPassword( string $value ) : \Aimeos\MShop\Customer\Item\Iface
	{
		if( (string) $value !== $this->getPassword() && $this->helper !== null ) {
			$value = $this->helper->encode( $value, $this->salt );
		}

		return $this->set( 'customer.password', $value );
	}


	/**
	 * Returns the last verification date of the customer.
	 *
	 * @return string|null Last verification date of the customer (YYYY-MM-DD format) or null if unknown
	 */
	public function getDateVerified() : ?string
	{
		return $this->get( 'customer.dateverified' );
	}


	/**
	 * Sets the latest verification date of the customer.
	 *
	 * @param string|null $value Latest verification date of the customer (YYYY-MM-DD) or null if unknown
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setDateVerified( ?string $value ) : \Aimeos\MShop\Customer\Item\Iface
	{
		return $this->set( 'customer.dateverified', $this->checkDateOnlyFormat( $value ) );
	}


	/**
	 * Returns the group IDs the customer belongs to
	 *
	 * @return array List of group IDs
	 */
	public function getGroups() : array
	{
		if( !isset( $this->groups ) )
		{
			if( ( $list = (array) $this->get( 'customer.groups', [] ) ) === [] )
			{
				foreach( $this->getListItems( 'customer/group', 'default' ) as $listItem ) {
					$list[] = $listItem->getRefId();
				}
			}

			$this->groups = $list;
		}

		return $this->groups;
	}


	/**
	 * Sets the group IDs the customer belongs to
	 *
	 * @param string[] $ids List of group IDs
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setGroups( array $ids ) : \Aimeos\MShop\Customer\Item\Iface
	{
		$list = $this->getGroups();

		if( array_diff( $ids, $list ) !== [] || array_diff( $list, $ids ) !== [] )
		{
			$this->groups = $ids;
			$this->setModified();
		}

		return $this;
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable() && $this->getStatus() > 0;
	}


	/**
	 * Tests if the user is a super user
	 *
	 * @return bool TRUE if user is a super user, FALSE if not
	 */
	public function isSuper() : bool
	{
		return (bool) $this->get( '.super', false );
	}


	/*
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'customer.label': $item = $item->setLabel( $value ); break;
				case 'customer.code': !$private ?: $item = $item->setCode( $value ); break;
				case 'customer.status': !$private ?: $item = $item->setStatus( (int) $value ); break;
				case 'customer.groups': !$private ?: $item = $item->setGroups( (array) $value ); break;
				case 'customer.password': !$private ?: $item = $item->setPassword( $value ); break;
				case 'customer.dateverified': !$private ?: $item = $item->setDateVerified( $value ); break;
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

		$list['customer.label'] = $this->getLabel();
		$list['customer.code'] = $this->getCode();

		if( $private === true )
		{
			$list['customer.status'] = $this->getStatus();
			$list['customer.groups'] = $this->getGroups();
			$list['customer.password'] = $this->getPassword();
			$list['customer.dateverified'] = $this->getDateVerified();
		}

		return $list;
	}
}
