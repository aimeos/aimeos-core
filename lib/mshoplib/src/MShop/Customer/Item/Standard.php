<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	private $values;
	private $helper;
	private $salt;


	/**
	 * Initializes the customer item object
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Payment address item object
	 * @param array $values List of attributes that belong to the customer item
	 * @param \Aimeos\MShop\Common\Lists\Item\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param string $salt Password salt (optional)
	 * @param \Aimeos\MShop\Common\Item\Helper\Password\Iface|null $helper Password encryption helper object
	 * @param \Aimeos\MShop\Customer\Item\Address\Iface[] $addresses List of delivery addresses
	 */
	public function __construct( \Aimeos\MShop\Common\Item\Address\Iface $address, array $values = [],
		array $listItems = [], array $refItems = [], $salt = null,
		\Aimeos\MShop\Common\Item\Helper\Password\Iface $helper = null, array $addresses = [] )
	{
		parent::__construct( $address, $values, $listItems, $refItems, $addresses );

		$this->values = $values;
		$this->helper = $helper;
		$this->salt = $salt;
	}


	/**
	 * Sets the new ID of the item.
	 *
	 * @param string|null $id ID of the item
	 */
	public function setId( $id )
	{
		parent::setId( $id );

		// set modified flag
		$addr = $this->getPaymentAddress();
		$addr->setId( null );
		$addr->setId( $this->getId() );

		return $this;
	}


	/**
	 * Returns the label of the customer item.
	 *
	 * @return string Label of the customer item
	 */
	public function getLabel()
	{
		if( isset( $this->values['customer.label'] ) ) {
			return (string) $this->values['customer.label'];
		}

		return '';
	}


	/**
	 * Sets the new label of the customer item.
	 *
	 * @param string $value Label of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setLabel( $value )
	{
		if( $value == $this->getLabel() ) { return $this; }

		$this->values['customer.label'] = (string) $value;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the status of the item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		if( isset( $this->values['customer.status'] ) ) {
			return (int) $this->values['customer.status'];
		}

		return 0;
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param integer $value Status of the item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setStatus( $value )
	{
		if( $value == $this->getStatus() ) { return $this; }

		$this->values['customer.status'] = (int) $value;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the code of the customer item.
	 *
	 * @return string Code of the customer item
	 */
	public function getCode()
	{
		if( isset( $this->values['customer.code'] ) ) {
			return (string) $this->values['customer.code'];
		}

		return '';
	}


	/**
	 * Sets the new code of the customer item.
	 *
	 * @param string $value Code of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setCode( $value )
	{
		if( $value == $this->getCode() ) { return $this; }

		$this->values['customer.code'] = (string) $value;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the birthday of the customer item.
	 *
	 * @return string|null Birthday in YYYY-MM-DD format
	 */
	public function getBirthday()
	{
		if( isset( $this->values['customer.birthday'] ) ) {
			return (string) $this->values['customer.birthday'];
		}

		return null;
	}


	/**
	 * Sets the birthday of the customer item.
	 *
	 * @param string|null $value Birthday of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setBirthday( $value )
	{
		if( $value === $this->getBirthday() ) { return $this; }

		$this->values['customer.birthday'] = $this->checkDateOnlyFormat( $value );
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the password of the customer item.
	 *
	 * @return string
	 */
	public function getPassword()
	{
		if( isset( $this->values['customer.password'] ) ) {
			return (string) $this->values['customer.password'];
		}

		return '';
	}


	/**
	 * Sets the password of the customer item.
	 *
	 * @param string $value password of the customer item
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setPassword( $value )
	{
		if( $value == $this->getPassword() ) { return $this; }

		if( $this->helper !== null ) {
			$value = $this->helper->encode( $value, $this->salt );
		}

		$this->values['customer.password'] = (string) $value;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the last verification date of the customer.
	 *
	 * @return string|null Last verification date of the customer (YYYY-MM-DD format) or null if unknown
	 */
	public function getDateVerified()
	{
		if( isset( $this->values['customer.dateverified'] ) ) {
			return (string) $this->values['customer.dateverified'];
		}

		return null;
	}


	/**
	 * Sets the latest verification date of the customer.
	 *
	 * @param string|null $value Latest verification date of the customer (YYYY-MM-DD) or null if unknown
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setDateVerified( $value )
	{
		if( $value === $this->getDateVerified() ) { return $this; }

		$this->values['customer.dateverified'] = $this->checkDateOnlyFormat( $value );
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the group IDs the customer belongs to
	 *
	 * @return array List of group IDs
	 */
	public function getGroups()
	{
		if( !isset( $this->values['groups'] ) )
		{
			$this->values['groups'] = [];

			foreach( $this->getListItems( 'customer/group', 'default' ) as $listItem ) {
				$this->values['groups'][] = $listItem->getRefId();
			}
		}

		return (array) $this->values['groups'];
	}

	/**
	 * Sets the group IDs the customer belongs to
	 *
	 * @param array $ids List of group IDs
	 * @return \Aimeos\MShop\Customer\Item\Iface Customer item for chaining method calls
	 */
	public function setGroups( array $ids )
	{
		$this->values['groups'] = $ids;
		$this->setModified();

		return $this;
	}


	/**
	 * Sets the item values from the given array.
	 *
	 * @param array $list Associative list of item keys and their values
	 * @return array Associative list of keys and their values that are unknown
	 */
	public function fromArray( array $list )
	{
		$unknown = [];
		$list = parent::fromArray( $list );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'customer.label': $this->setLabel( $value ); break;
				case 'customer.code': $this->setCode( $value ); break;
				case 'customer.birthday': $this->setBirthday( $value ); break;
				case 'customer.status': $this->setStatus( $value ); break;
				case 'customer.groups': $this->setGroups( $value ); break;
				case 'customer.password': $this->setPassword( $value ); break;
				case 'customer.dateverified': $this->setDateVerified( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
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

		$list['customer.label'] = $this->getLabel();
		$list['customer.code'] = $this->getCode();
		$list['customer.birthday'] = $this->getBirthday();
		$list['customer.status'] = $this->getStatus();

		if( $private === true )
		{
			$list['customer.groups'] = $this->getGroups();
			$list['customer.password'] = $this->getPassword();
			$list['customer.dateverified'] = $this->getDateVerified();
		}

		return $list;
	}


	/**
	 * Tests if the date param represents an ISO format.
	 *
	 * @param string|null $date ISO date in YYYY-MM-DD format or null for no date
	 */
	protected function checkDateOnlyFormat( $date )
	{
		if( $date !== null )
		{
			if( preg_match( '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/', (string) $date ) !== 1 ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in date "%1$s". ISO format "YYYY-MM-DD" expected.', $date ) );
			}

			return (string) $date;
		}
	}
}
