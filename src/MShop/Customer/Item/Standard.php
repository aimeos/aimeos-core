<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
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
	private ?\Aimeos\Base\Password\Iface $passwd = null;
	private ?array $groups = null;


	/**
	 * Initializes the customer item object
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Payment address item object
	 * @param string $prefix Prefix for the keys returned by toArray()
	 * @param array $values List of attributes that belong to the customer item
	 * @param \Aimeos\Base\Password\Iface|null $passwd Password encryption object
	 */
	public function __construct( \Aimeos\MShop\Common\Item\Address\Iface $address, string $prefix,
		array $values = [], ?\Aimeos\Base\Password\Iface $passwd = null )
	{
		parent::__construct( $address, $prefix, $values );
		$this->passwd = $passwd;
	}


	/**
	 * Sets the new ID of the item.
	 *
	 * @param string|null $id ID of the item
	 * @return static Customer item for chaining method calls
	 */
	public function setId( ?string $id ) : static
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
		return (string) $this->get( 'customer.label', '' );
	}


	/**
	 * Sets the new label of the customer item.
	 *
	 * @param string $value Label of the customer item
	 * @return static Customer item for chaining method calls
	 */
	public function setLabel( ?string $value ) : static
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
		return (int) $this->get( 'customer.status', 1 );
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param int $value Status of the item
	 * @return static Customer item for chaining method calls
	 */
	public function setStatus( int $value ) : static
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
	 * @return static Customer item for chaining method calls
	 */
	public function setCode( string $value ) : static
	{
		if( $value !== $this->get( 'customer.code' ) ) {
			$this->setDateVerified( null );
		}

		return $this->set( 'customer.code', \Aimeos\Utils::code( $value, 255 ) );
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
	 * @return static Customer item for chaining method calls
	 */
	public function setPassword( string $value ) : static
	{
		if( $this->passwd && $value !== $this->getPassword() ) {
			$value = $this->passwd->hash( $value );
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
		// @phpstan-ignore return.type
		return $this->get( 'customer.dateverified' );
	}


	/**
	 * Sets the latest verification date of the customer.
	 *
	 * @param string|null $value Latest verification date of the customer (YYYY-MM-DD) or null if unknown
	 * @return static Customer item for chaining method calls
	 */
	public function setDateVerified( ?string $value ) : static
	{
		return $this->set( 'customer.dateverified', \Aimeos\Utils::date( $value ) );
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
			if( ( $list = (array) $this->get( 'customer.groups', [] ) ) === [] ) {
				$list = $this->getRefItems( 'group', null, 'default' )->col( 'group.id' )->all();
			}

			$this->groups = $list;
		}

		return $this->groups;
	}


	/**
	 * Sets the group IDs/codes the customer belongs to
	 *
	 * @param array $value List of group IDs
	 * @return static Customer item for chaining method calls
	 */
	public function setGroups( array $value ) : static
	{
		$list = $this->getGroups();

		// @phpstan-ignore argument.type, argument.type, argument.type, argument.type
		if( array_diff( $value, $list ) !== [] || array_diff( $list, $value ) !== [] )
		{
			$this->groups = $value;
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
	 * @type array &$list Associative list of item keys and their values
	 * @param bool $private True to set private properties too, false for public only
	 * @return static Customer item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : static
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'customer.label': $item->setLabel( $value ? (string) $value : null ); break;
				case 'customer.code': !$private ?: $item->setCode( (string) $value ); break;
				case 'customer.status': !$private ?: $item->setStatus( (int) $value ); break;
				case 'customer.groups': !$private ?: $item->setGroups( (array) $value ); break;
				case 'customer.password': !$private ?: $item->setPassword( (string) $value ); break;
				case 'customer.dateverified': !$private ?: $item->setDateVerified( $value ? (string) $value : null ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool $private True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['customer.label'] = $this->getLabel();
		$list['customer.code'] = $this->getCode();

		if( $private === true )
		{
			// The password hash is write-only
			$list['customer.status'] = $this->getStatus();
			$list['customer.groups'] = $this->getGroups();
			$list['customer.dateverified'] = $this->getDateVerified();
		}

		return $list;
	}
}
