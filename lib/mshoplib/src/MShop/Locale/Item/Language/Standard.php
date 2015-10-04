<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Locale
 */


/**
 * Default implementation of a Language item.
 *
 * @package MShop
 * @subpackage Locale
 */
class MShop_Locale_Item_Language_Standard
	extends MShop_Common_Item_Base
	implements MShop_Locale_Item_Language_Iface
{

	private $modified = false;
	private $values;


	/**
	 * Initialize the language object.
	 *
	 * @param array $values Possible params to be set on init.
	 */
	public function __construct( array $values = array( ) )
	{
		parent::__construct( 'locale.language.', $values );

		$this->values = $values;

		if( isset( $values['id'] ) ) {
			$this->setId( $values['id'] );
		}
	}


	/**
	 * Returns the id of the language.
	 *
	 * @return string|null Id of the language
	 */
	public function getId()
	{
		return ( isset( $this->values['id'] ) ? (string) $this->values['id'] : null );
	}


	/**
	 * Sets the id of the language.
	 *
	 * @param string $key Id to set
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
	 * Returns the two letter ISO language code.
	 *
	 * @return string two letter ISO language code
	 */
	public function getCode()
	{
		return ( isset( $this->values['code'] ) ? (string) $this->values['code'] : '' );
	}


	/**
	 * Sets the two letter ISO language code.
	 *
	 * @param string $key two letter ISO language code
	 */
	public function setCode( $key )
	{
		if( $key == $this->getCode() ) { return; }

		$len = strlen( $key );
		if( $len < 2 || $len > 5 || preg_match( '/^[a-z]{2,3}((-|_)[a-zA-Z]{2})?$/', $key ) !== 1 ) {
			throw new MShop_Locale_Exception( sprintf( 'Invalid characters in ISO language code "%1$s"', $key ) );
		}

		$this->values['code'] = (string) $key;
		$this->modified = true;
	}


	/**
	 * Returns the label property.
	 *
	 * @return string Returns the label of the language
	 */
	public function getLabel()
	{
		return ( isset( $this->values['label'] ) ? (string) $this->values['label'] : '' );
	}


	/**
	 * Sets the label property.
	 *
	 * @param string $label Label of the language
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
				case 'locale.language.id': $this->setId( $value ); break;
				case 'locale.language.code': $this->setCode( $value ); break;
				case 'locale.language.label': $this->setLabel( $value ); break;
				case 'locale.language.status': $this->setStatus( $value ); break;
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

		$list['locale.language.id'] = $this->getId();
		$list['locale.language.code'] = $this->getCode();
		$list['locale.language.label'] = $this->getLabel();
		$list['locale.language.status'] = $this->getStatus();

		return $list;
	}


	/**
	 * Tests if the needed object properties are modified.
	 *
	 * @return boolean True if modiefied flag was set otherwise false
	 */
	public function isModified()
	{
		return $this->modified || parent::isModified();
	}

}
