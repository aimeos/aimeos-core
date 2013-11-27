<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Text
 */


/**
 * Default text manager implementation.
 *
 * @package MShop
 * @subpackage Text
 */
class MShop_Text_Item_Default
	extends MShop_Common_Item_ListRef_Abstract
	implements MShop_Text_Item_Interface
{
	private $_values;


	/**
	 * Initializes the text item object with the given values.
	 *
	 * @param array $values Associative list of key/value pairs
	 */
	public function __construct( array $values = array(), array $listItems = array(), array $refItems = array() )
	{
		parent::__construct( 'text.', $values, $listItems, $refItems );

		$this->_values = $values;
	}


	/**
	 * Returns the language ID of the text item.
	 *
	 * @return string|null Language ID of the text item
	 */
	public function getLanguageId()
	{
		return ( isset( $this->_values['langid'] ) ? (string) $this->_values['langid'] : null );
	}


	/**
	 *  Sets the language ID of the text item.
	 *
	 * @param string|null $langid Language ID of the text type
	 */
	public function setLanguageId( $langid )
	{
		if ( $langid === $this->getLanguageId() ) { return; }

		if( $langid !== null && ( strlen( $langid ) !== 2 || ctype_alpha( $langid ) === false ) )
		{
			throw new MShop_Text_Exception( sprintf( 'Invalid characters in ISO language code "%1$s"', $langid ) );
		}

		$this->_values['langid'] = ( $langid !== null ? (string) $langid : null );
		$this->setModified();
	}


	/**
	 * Returns the type ID of the text item.
	 *
	 * @return integer|null Type ID of the text item
	 */
	public function getTypeId()
	{
		return ( isset( $this->_values['typeid'] ) ? (int) $this->_values['typeid'] : null );
	}


	/**
	 *  Sets the type ID of the text item.
	 *
	 * @param integer $typeid Type ID of the text type
	 */
	public function setTypeId( $typeid )
	{
		if ( $typeid == $this->getTypeId() ) { return; }

		$this->_values['typeid'] = (int) $typeid;
		$this->setModified();
	}


	/**
	 * Returns the type of the text item.
	 *
	 * @return string|null Type of the text item
	 */
	public function getType()
	{
		return ( isset( $this->_values['type'] ) ? (string) $this->_values['type'] : null );
	}


	/**
	 * Returns the domain of the text item.
	 *
	 * @return string Domain of the text item
	 */
	public function getDomain()
	{
		return ( isset( $this->_values['domain'] ) ? (string) $this->_values['domain'] : '' );
	}


	/**
	 * Sets the domain of the text item.
	 *
	 * @param string $domain Domain of the text item
	 */
	public function setDomain( $domain )
	{
		if ( $domain == $this->getDomain() ) { return; }

		$this->_values['domain'] = (string) $domain;
		$this->setModified();
	}


	/**
	 * Returns the content of the text item.
	 *
	 * @return string Content of the text item
	 */
	public function getContent()
	{
		return ( isset( $this->_values['content'] ) ? (string) $this->_values['content'] : '' );
	}


	/**
	 * Sets the content of the text item.
	 *
	 * @param string $text Content of the text item
	 */
	public function setContent( $text )
	{
		if ( $text == $this->getContent() ) { return; }

		$this->_values['content'] = (string) $text;
		$this->setModified();
	}


	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel()
	{
		return ( isset( $this->_values['label'] ) ? (string) $this->_values['label'] : '' );
	}


	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 */
	public function setLabel( $label )
	{
		if ( $label == $this->getLabel() ) { return; }

		$this->_values['label'] = (string) $label;
		$this->setModified();
	}


	/**
	 * Returns the status of the text item.
	 *
	 * @return integer Status of the text item
	 */
	public function getStatus()
	{
		return ( isset( $this->_values['status'] ) ? (int) $this->_values['status'] : 0 );
	}


	/**
	 * Sets the status of the text item.
	 *
	 * @param integer $status true/false for enabled/disabled
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

		$list['text.languageid'] =  $this->getLanguageId();
		$list['text.typeid'] = $this->getTypeId();
		$list['text.label'] = $this->getLabel();
		$list['text.type'] = $this->getType();
		$list['text.domain'] = $this->getDomain();
		$list['text.content'] = $this->getContent();
		$list['text.status'] = $this->getStatus();

		return $list;
	}
}
;