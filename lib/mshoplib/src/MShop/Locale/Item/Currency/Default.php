<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Locale
 */


/**
 * Default implementation of a currency item.
 *
 * @package MShop
 * @subpackage Locale
 */
class MShop_Locale_Item_Currency_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Locale_Item_Currency_Interface
{
	private $_modified = false;
	private $_values;

	/**
	 * Initializes the currency object object.
	 *
	 * @param array $values Possible params to be set on initialization
	 */
	public function __construct( array $values = array( ) )
	{
		parent::__construct('locale.currency.', $values);

		$this->_values = $values;

		if( isset( $values['id'] ) ) {
			$this->setId( $values['id'] );
		}
	}


	/**
	 * Sets the ID of the currency.
	 *
	 * @param string $key ID of the currency
	 */
	public function setId( $key )
	{
		if( $key !== null )
		{
			$this->setCode($key);
			$this->_values['id'] = $this->_values['code'];
			$this->_modified = false;
		}
		else
		{
			$this->_values['id'] = null;
			$this->_modified = true;
		}
	}


	/**
	 * Returns the ID of the currency.
	 *
	 * @return string|null ID of the currency
	 */
	public function getId()
	{
		return ( isset( $this->_values['id'] ) ? (string) $this->_values['id'] : null );
	}


	/**
	 * Returns the code of the currency.
	 *
	 * @return string Code of the currency
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets the code of the currency.
	 *
	 * @param string $key Code of the currency
	 */
	public function setCode( $key )
	{
		if ( $key == $this->getCode() ) { return; }

		if ( strlen($key) != 3 || ctype_alpha( $key ) === false ) {
			throw new MShop_Locale_Exception( sprintf( 'Invalid characters in ISO currency code "%1$s"', $key ) );
		}

		$this->_values['code'] = strtoupper( $key );
		$this->_modified = true;
	}


	/**
	 * Returns the label or symbol of the currency.
	 *
	 * @return string Label or symbol of the currency
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the label or symbol of the currency.
	 *
	 * @param string $label Label or symbol of the currency
	 */
	public function setLabel( $label )
	{
		if ( $label == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Returns the status of the item.
	 *
	 * @return integer Status of the item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the status of the item.
	 *
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status )
	{
		if ( $status == $this->getStatus() ) { return; }

		$this->_values['status'] = (int) $status;
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
	 * @return Associative list of item properties and their values
	 */
	public function toArray()
	{
		$list = parent::toArray();
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
		return $this->_modified || parent::isModified();
	}

}
