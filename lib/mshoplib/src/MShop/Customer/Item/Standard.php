<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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
class Standard
	extends \Aimeos\MShop\Common\Item\ListRef\Base
	implements \Aimeos\MShop\Customer\Item\Iface
{
	private $billingaddress;
	private $values;
	private $helper;
	private $salt;


	/**
	 * Initializes the customer item object
	 *
	 * @param array $values List of attributes that belong to the customer item
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Payment address item object
	 * @param \Aimeos\MShop\Common\Lists\Item\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @param string $salt Password salt (optional)
	 * @param \Aimeos\MShop\Common\Item\Helper\Password\Iface $helper Password encryption helper object
	 */
	public function __construct( \Aimeos\MShop\Common\Item\Address\Iface $address, array $values = array(),
		array $listItems = array(), array $refItems = array(), $salt = '',
		\Aimeos\MShop\Common\Item\Helper\Password\Iface $helper = null )
	{
		parent::__construct( 'customer.', $values, $listItems, $refItems );

		foreach( $values as $name => $value )
		{
			switch( $name )
			{
				case 'salutation': $address->setSalutation( $value ); break;
				case 'company': $address->setCompany( $value ); break;
				case 'vatid': $address->setVatID( $value ); break;
				case 'title': $address->setTitle( $value ); break;
				case 'firstname': $address->setFirstname( $value ); break;
				case 'lastname': $address->setLastname( $value ); break;
				case 'address1': $address->setAddress1( $value ); break;
				case 'address2': $address->setAddress2( $value ); break;
				case 'address3': $address->setAddress3( $value ); break;
				case 'postal': $address->setPostal( $value ); break;
				case 'city': $address->setCity( $value ); break;
				case 'state': $address->setState( $value ); break;
				case 'langid': $address->setLanguageId( $value ); break;
				case 'countryid': $address->setCountryId( $value ); break;
				case 'telephone': $address->setTelephone( $value ); break;
				case 'telefax': $address->setTelefax( $value ); break;
				case 'website': $address->setWebsite( $value ); break;
				case 'email': $address->setEmail( $value ); break;
			}
		}

		// set modified flag to false
		$address->setId( $this->getId() );

		$this->billingaddress = $address;
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
		$this->billingaddress->setId( null );
		$this->billingaddress->setId( $this->getId() );
	}


	/**
	 * Returns the label of the customer item.
	 *
	 * @return string Label of the customer item
	 */
	public function getLabel()
	{
		return ( isset( $this->values['label'] ) ? (string) $this->values['label'] : '' );
	}


	/**
	 * Sets the new label of the customer item.
	 *
	 * @param string $value Label of the customer item
	 */
	public function setLabel( $value )
	{
		if( $value == $this->getLabel() ) { return; }

		$this->values['label'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the status of the item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->values['status'] ) ? (int) $this->values['status'] : 0 );
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param integer $value Status of the item
	 */
	public function setStatus( $value )
	{
		if( $value == $this->getStatus() ) { return; }

		$this->values['status'] = (int) $value;
		$this->setModified();
	}


	/**
	 * Returns the code of the customer item.
	 *
	 * @return string Code of the customer item
	 */
	public function getCode()
	{
		return ( isset( $this->values['code'] ) ? (string) $this->values['code'] : '' );
	}


	/**
	 * Sets the new code of the customer item.
	 *
	 * @param string $value Code of the customer item
	 */
	public function setCode( $value )
	{
		$this->checkCode( $value );

		if( $value == $this->getCode() ) { return; }

		$this->values['code'] = (string) $value;
		$this->setModified();
	}


	/**
	 * Returns the billingaddress of the customer item.
	 *
	 * @return \Aimeos\MShop\Common\Item\Address\Iface
	 */
	public function getPaymentAddress()
	{
		return $this->billingaddress;
	}


	/**
	 * Sets the billingaddress of the customer item.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $address Billingaddress of the customer item
	 */
	public function setPaymentAddress( \Aimeos\MShop\Common\Item\Address\Iface $address )
	{
		if( $address === $this->billingaddress && $address->isModified() === false ) { return; }

		$this->billingaddress = $address;
		$this->setModified();
	}


	/**
	 * Returns the birthday of the customer item.
	 *
	 * @return string
	 */
	public function getBirthday()
	{
		return ( isset( $this->values['birthday'] ) ? (string) $this->values['birthday'] : null );
	}


	/**
	 * Sets the birthday of the customer item.
	 *
	 * @param string $value Birthday of the customer item
	 */
	public function setBirthday( $value )
	{
		if( $value === $this->getBirthday() ) { return; }

		$this->values['birthday'] = $this->checkDateOnlyFormat( $value );
		$this->setModified();
	}


	/**
	 * Returns the password of the customer item.
	 *
	 * @return string
	 */
	public function getPassword()
	{
		return ( isset( $this->values['password'] ) ? (string) $this->values['password'] : '' );
	}


	/**
	 * Sets the password of the customer item.
	 *
	 * @param string $value password of the customer item
	 */
	public function setPassword( $value )
	{
		if( $value == $this->getPassword() ) { return; }

		if( $this->helper !== null ) {
			$value = $this->helper->encode( $value, $this->salt );
		}

		$this->values['password'] = $value;
		$this->setModified();
	}


	/**
	 * Returns the last verification date of the customer.
	 *
	 * @return string|null Last verification date of the customer (YYYY-MM-DD format) or null if unknown
	 */
	public function getDateVerified()
	{
		return ( isset( $this->values['vdate'] ) ? (string) $this->values['vdate'] : null );
	}


	/**
	 * Sets the latest verification date of the customer.
	 *
	 * @param string|null $value Latest verification date of the customer (YYYY-MM-DD) or null if unknown
	 */
	public function setDateVerified( $value )
	{
		if( $value === $this->getDateVerified() ) { return; }

		$this->values['vdate'] = $this->checkDateOnlyFormat( $value );
		$this->setModified();
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
			$this->values['groups'] = array();

			foreach( $this->getListItems( 'customer/group' ) as $listItem ) {
				$this->values['groups'][] = $listItem->getRefId();
			}
		}

		return (array) $this->values['groups'];
	}


	/**
	 * Returns the item type
	 *
	 * @return Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'customer';
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
		$addr = $this->getPaymentAddress();

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'customer.label': $this->setLabel( $value ); break;
				case 'customer.code': $this->setCode( $value ); break;
				case 'customer.birthday': $this->setBirthday( $value ); break;
				case 'customer.status': $this->setStatus( $value ); break;
				case 'customer.password': $this->setPassword( $value ); break;
				case 'customer.dateverified': $this->setDateVerified( $value ); break;
				case 'customer.salutation': $addr->setSalutation( $value ); break;
				case 'customer.company': $addr->setCompany( $value ); break;
				case 'customer.vatid': $addr->setVatID( $value ); break;
				case 'customer.title': $addr->setTitle( $value ); break;
				case 'customer.firstname': $addr->setFirstname( $value ); break;
				case 'customer.lastname': $addr->setLastname( $value ); break;
				case 'customer.address1': $addr->setAddress1( $value ); break;
				case 'customer.address2': $addr->setAddress2( $value ); break;
				case 'customer.address3': $addr->setAddress3( $value ); break;
				case 'customer.postal': $addr->setPostal( $value ); break;
				case 'customer.city': $addr->setCity( $value ); break;
				case 'customer.state': $addr->setState( $value ); break;
				case 'customer.languageid': $addr->setLanguageId( $value ); break;
				case 'customer.countryid': $addr->setCountryId( $value ); break;
				case 'customer.telephone': $addr->setTelephone( $value ); break;
				case 'customer.email': $addr->setEmail( $value ); break;
				case 'customer.telefax': $addr->setTelefax( $value ); break;
				case 'customer.website': $addr->setWebsite( $value ); break;
				default: $unknown[$key] = $value;
			}
		}

		return $unknown;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @return array Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();

		$list['customer.label'] = $this->getLabel();
		$list['customer.code'] = $this->getCode();
		$list['customer.birthday'] = $this->getBirthday();
		$list['customer.status'] = $this->getStatus();
		$list['customer.password'] = $this->getPassword();
		$list['customer.dateverified'] = $this->getDateVerified();
		$list['customer.salutation'] = $this->getPaymentAddress()->getSalutation();
		$list['customer.company'] = $this->getPaymentAddress()->getCompany();
		$list['customer.vatid'] = $this->getPaymentAddress()->getVatID();
		$list['customer.title'] = $this->getPaymentAddress()->getTitle();
		$list['customer.firstname'] = $this->getPaymentAddress()->getFirstname();
		$list['customer.lastname'] = $this->getPaymentAddress()->getLastname();
		$list['customer.address1'] = $this->getPaymentAddress()->getAddress1();
		$list['customer.address2'] = $this->getPaymentAddress()->getAddress2();
		$list['customer.address3'] = $this->getPaymentAddress()->getAddress3();
		$list['customer.postal'] = $this->getPaymentAddress()->getPostal();
		$list['customer.city'] = $this->getPaymentAddress()->getCity();
		$list['customer.state'] = $this->getPaymentAddress()->getState();
		$list['customer.languageid'] = $this->getPaymentAddress()->getLanguageId();
		$list['customer.countryid'] = $this->getPaymentAddress()->getCountryId();
		$list['customer.telephone'] = $this->getPaymentAddress()->getTelephone();
		$list['customer.email'] = $this->getPaymentAddress()->getEmail();
		$list['customer.telefax'] = $this->getPaymentAddress()->getTelefax();
		$list['customer.website'] = $this->getPaymentAddress()->getWebsite();
		return $list;
	}


	/**
	 * Implements deep copies for clones.
	 */
	public function __clone()
	{
		$this->billingaddress = clone $this->billingaddress;
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
