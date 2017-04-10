<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Locale
 */


namespace Aimeos\MShop\Locale\Item\Currency;


/**
 * Default implementation of a currency item.
 *
 * @package MShop
 * @subpackage Locale
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Locale\Item\Currency\Iface
{
	private $modified = false;
	private $values;


	/**
	 * Initializes the currency object object.
	 *
	 * @param array $values Possible params to be set on initialization
	 */
	public function __construct( array $values = [] )
	{
		parent::__construct( 'locale.currency.', $values );

		$this->values = $values;

		if( isset( $values['locale.currency.id'] ) ) {
			$this->setId( $values['locale.currency.id'] );
		}
	}


	/**
	 * Returns the ID of the currency.
	 *
	 * @return string|null ID of the currency
	 */
	public function getId()
	{
		if( isset( $this->values['locale.currency.id'] ) ) {
			return (string) $this->values['locale.currency.id'];
		}

		return null;
	}


	/**
	 * Sets the ID of the currency.
	 *
	 * @param string $key ID of the currency
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
	 */
	public function setId( $key )
	{
		if( $key !== null )
		{
			$this->setCode( $key );
			$this->values['locale.currency.id'] = $this->values['locale.currency.code'];
			$this->modified = false;
		}
		else
		{
			$this->values['locale.currency.id'] = null;
			$this->modified = true;
		}

		return $this;
	}


	/**
	 * Returns the code of the currency.
	 *
	 * @return string Code of the currency
	 */
	public function getCode()
	{
		if( isset( $this->values['locale.currency.code'] ) ) {
			return (string) $this->values['locale.currency.code'];
		}

		return '';
	}


	/**
	 * Sets the code of the currency.
	 *
	 * @param string $key Code of the currency
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
	 */
	public function setCode( $key )
	{
		if( $key == $this->getCode() ) { return $this; }

		if( strlen( $key ) != 3 || ctype_alpha( $key ) === false ) {
			throw new \Aimeos\MShop\Locale\Exception( sprintf( 'Invalid characters in ISO currency code "%1$s"', $key ) );
		}

		$this->values['locale.currency.code'] = strtoupper( $key );
		$this->modified = true;

		return $this;
	}


	/**
	 * Returns the label or symbol of the currency.
	 *
	 * @return string Label or symbol of the currency
	 */
	public function getLabel()
	{
		if( isset( $this->values['locale.currency.label'] ) ) {
			return (string) $this->values['locale.currency.label'];
		}

		return '';
	}


	/**
	 * Sets the label or symbol of the currency.
	 *
	 * @param string $label Label or symbol of the currency
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
	 */
	public function setLabel( $label )
	{
		if( $label == $this->getLabel() ) { return $this; }

		$this->values['locale.currency.label'] = (string) $label;
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
		if( isset( $this->values['locale.currency.status'] ) ) {
			return (int) $this->values['locale.currency.status'];
		}

		return 0;
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param integer $status Status of the item
	 * @return \Aimeos\MShop\Locale\Item\Currency\Iface Locale currency item for chaining method calls
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return $this; }

		$this->values['locale.currency.status'] = (int) $status;
		$this->setModified();

		return $this;
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType()
	{
		return 'locale/currency';
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
				case 'locale.currency.id': $this->setId( $value ); break;
				case 'locale.currency.code': $this->setCode( $value ); break;
				case 'locale.currency.label': $this->setLabel( $value ); break;
				case 'locale.currency.status': $this->setStatus( $value ); break;
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

		$list['locale.currency.id'] = $this->getId();
		$list['locale.currency.code'] = $this->getCode();
		$list['locale.currency.label'] = $this->getLabel();
		$list['locale.currency.status'] = $this->getStatus();

		return $list;
	}


	/**
	 * Tests if the object was modified.
	 *
	 * @return boolean True if modified, false if not
	 */
	public function isModified()
	{
		return $this->modified || parent::isModified();
	}

}
