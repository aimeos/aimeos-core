<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Locale
 * @version $Id: Default.php 14852 2012-01-13 12:24:15Z doleiynyk $
 */


/**
 * Default implementation of a Language item.
 *
 * @package MShop
 * @subpackage Locale
 */
class MShop_Locale_Item_Language_Default
	extends MShop_Common_Item_Abstract
	implements MShop_Locale_Item_Language_Interface
{

	private $_modified = false;
	private $_values;


	/**
	 * Initialize the language object.
	 *
	 * @param array $values Possible params to be set on init.
	 */
	public function __construct( array $values = array( ) )
	{
		parent::__construct('locale.language.', $values);

		$this->_values = $values;

		if ( isset( $values['id'] ) ) {
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
		return ( isset( $this->_values['id'] ) ? (string) $this->_values['id'] : null );
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
	 * Returns the two letter ISO language code.
	 *
	 * @return string two letter ISO language code
	 */
	public function getCode()
	{
		return ( isset( $this->_values['code'] ) ? (string) $this->_values['code'] : '' );
	}


	/**
	 * Sets the two letter ISO language code.
	 *
	 * @param string $key two letter ISO language code
	 */
	public function setCode( $key )
	{
		if ( $key == $this->getCode() ) { return; }

		$len = strlen( $key );
		if ( $len < 2 || $len > 5 || preg_match( '/^[a-z]{2,3}((-|_)[a-zA-Z]{2})?$/', $key ) !== 1 ) {
			throw new MShop_Locale_Exception(sprintf( 'Invalid characters in ISO language code "%1$s"', $key ) );
		}

		$this->_values['code'] = (string) $key;
		$this->_modified = true;
	}


	/**
	 * Returns the label property.
	 *
	 * @return string Returns the label of the language
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the label property.
	 *
	 * @param string $label Label of the language
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
		return $this->_modified || parent::isModified();
	}

}
