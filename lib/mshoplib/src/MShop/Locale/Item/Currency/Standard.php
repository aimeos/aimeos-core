<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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
	public function __construct( array $values = array( ) )
	{
		parent::__construct( 'locale.currency.', $values );

		$this->values = $values;

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
			$this->setCode( $key );
			$this->values['id'] = $this->values['code'];
			$this->modified = false;
		}
		else
		{
			$this->values['id'] = null;
			$this->modified = true;
		}
	}


	/**
	 * Returns the ID of the currency.
	 *
	 * @return string|null ID of the currency
	 */
	public function getId()
	{
		return ( isset( $this->values['id'] ) ? (string) $this->values['id'] : null );
	}


	/**
	 * Returns the code of the currency.
	 *
	 * @return string Code of the currency
	 */
	public function getCode()
	{
		return ( isset( $this->values['code'] ) ? (string) $this->values['code'] : '' );
	}


	/**
	 * Sets the code of the currency.
	 *
	 * @param string $key Code of the currency
	 */
	public function setCode( $key )
	{
		if( $key == $this->getCode() ) { return; }

		if( strlen( $key ) != 3 || ctype_alpha( $key ) === false ) {
			throw new \Aimeos\MShop\Locale\Exception( sprintf( 'Invalid characters in ISO currency code "%1$s"', $key ) );
		}

		$this->values['code'] = strtoupper( $key );
		$this->modified = true;
	}


	/**
	 * Returns the label or symbol of the currency.
	 *
	 * @return string Label or symbol of the currency
	 */
	public function getLabel()
	{
		return ( isset( $this->values['label'] ) ? (string) $this->values['label'] : '' );
	}


	/**
	 * Sets the label or symbol of the currency.
	 *
	 * @param string $label Label or symbol of the currency
	 */
	public function setLabel( $label )
	{
		if( $label == $this->getLabel() ) { return; }

		$this->values['label'] = (string) $label;
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
	 * @param integer $status Status of the item
	 */
	public function setStatus( $status )
	{
		if( $status == $this->getStatus() ) { return; }

		$this->values['status'] = (int) $status;
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
		return $this->modified || parent::isModified();
	}

}
